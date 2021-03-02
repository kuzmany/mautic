<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\EmailBundle\Controller\Api;

use Doctrine\ORM\EntityNotFoundException;
use Mautic\ApiBundle\Controller\CommonApiController;
use Mautic\CoreBundle\Helper\EmojiHelper;
use Mautic\CoreBundle\Helper\InputHelper;
use Mautic\CoreBundle\Helper\RandomHelper\RandomHelperInterface;
use Mautic\EmailBundle\Helper\MailHelper;
use Mautic\EmailBundle\MonitoredEmail\Processor\Reply;
use Mautic\LeadBundle\Controller\LeadAccessTrait;
use Mautic\LeadBundle\Entity\Lead;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class EmailApiController extends CommonApiController
{
    use LeadAccessTrait;

    public function initialize(FilterControllerEvent $event)
    {
        $this->model            = $this->getModel('email');
        $this->entityClass      = 'Mautic\EmailBundle\Entity\Email';
        $this->entityNameOne    = 'email';
        $this->entityNameMulti  = 'emails';
        $this->serializerGroups = ['emailDetails', 'categoryList', 'publishDetails', 'assetList', 'formList', 'leadListList'];
        $this->dataInputMasks   = [
            'customHtml'     => 'html',
            'dynamicContent' => [
                'content' => 'html',
                'filters' => [
                    'content' => 'html',
                ],
            ],
        ];

        parent::initialize($event);
    }

    /**
     * Obtains a list of emails.
     *
     * @return Response
     */
    public function getEntitiesAction()
    {
        //get parent level only
        $this->listFilters[] = [
            'column' => 'e.variantParent',
            'expr'   => 'isNull',
        ];

        return parent::getEntitiesAction();
    }

    /**
     * Sends the email to it's assigned lists.
     *
     * @param int $id Email ID
     *
     * @return Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function sendAction($id)
    {
        $entity = $this->model->getEntity($id);

        if (null === $entity || !$entity->isPublished()) {
            return $this->notFound();
        }

        if (!$this->checkEntityAccess($entity)) {
            return $this->accessDenied();
        }

        $lists = $this->request->request->get('lists', null);
        $limit = $this->request->request->get('limit', null);

        [$count, $failed] = $this->model->sendEmailToLists($entity, $lists, $limit);

        $view = $this->view(
            [
                'success'          => 1,
                'sentCount'        => $count,
                'failedRecipients' => $failed,
            ],
            Response::HTTP_OK
        );

        return $this->handleView($view);
    }

    /**
     * Sends the email to a specific lead.
     *
     * @param int $id     Email ID
     * @param int $leadId Lead ID
     *
     * @return Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function sendLeadAction($id, $leadId)
    {
        $entity = $this->model->getEntity($id);
        if (null !== $entity) {
            if (!$this->checkEntityAccess($entity)) {
                return $this->accessDenied();
            }

            /** @var Lead $lead */
            $lead = $this->checkLeadAccess($leadId, 'edit');
            if ($lead instanceof Response) {
                return $lead;
            }

            $post       = $this->request->request->all();
            $tokens     = (!empty($post['tokens'])) ? $post['tokens'] : [];
            $assetsIds  = (!empty($post['assetAttachments'])) ? $post['assetAttachments'] : [];
            $response   = ['success' => false];

            $cleanTokens = [];

            foreach ($tokens as $token => $value) {
                $value = InputHelper::clean($value);
                if (!preg_match('/^{.*?}$/', $token)) {
                    $token = '{'.$token.'}';
                }

                $cleanTokens[$token] = $value;
            }

            $leadFields = array_merge(['id' => $leadId], $lead->getProfileFields());

            $result = $this->model->sendEmail(
                $entity,
                $leadFields,
                [
                    'source'            => ['api', 0],
                    'tokens'            => $cleanTokens,
                    'assetAttachments'  => $assetsIds,
                    'return_errors'     => true,
                ]
            );

            if (is_bool($result)) {
                $response['success'] = $result;
            } else {
                $response['failed'] = $result;
            }

            $view = $this->view($response, Response::HTTP_OK);

            return $this->handleView($view);
        }

        return $this->notFound();
    }

    public function sendCustomEmailAction($contactId)
    {
        /** @var Lead $lead */
        $lead = $this->checkLeadAccess($contactId, 'edit');
        if ($lead instanceof Response) {
            return $lead;
        }

        if (!$lead->getEmail()) {
            return $this->notFound('mautic.contact.error.notfound');
        }

        $response = ['success' => false];

        /** @var MailHelper $mailer */
        $mailer = $this->get('mautic.helper.mailer')->getMailer();

        $params = $this->request->request->all();

        $fromEmail = $params['fromEmail'] ?? null;

        if (null === $fromEmail) {
            return $this->badRequest('mautic.email.error.from.email.required');
        }

        $mailer->setFrom(
            $fromEmail,
            $params['fromName'] ?? null
        );

        if ($replyToEmail = $params['replyToEmail'] ?? null) {
            $mailer->setReplyTo(
                    $replyToEmail,
                    $params['replyToName'] ?? null
                );
        }

        $subject = $params['subject'] ?? null;

        if (null === $subject) {
            return $this->badRequest('mautic.core.subject.required');
        }

        $subject = EmojiHelper::toHtml($subject);
        $mailer->setSubject($subject);

        $content = $params['content'] ?? null;

        if (null === $content) {
            return $this->badrequest('mautic.email.error.content.required');
        }

        // Set Content
        $mailer->setBody($content);
        $mailer->parsePlainText($content);
        $mailer->setLead($lead->getProfileFields());
        $mailer->setIdHash();
        $mailer->setSource(['api', 0]);

        if ($mailer->send(true, false, false)) {
            /** @var Stat $stat */
            $stat                     = $mailer->createEmailStat();
            $response['trackingHash'] = ($stat && $stat->getTrackingHash()) ? $stat->getTrackingHash() : 0;
            $response['success']      = true;
        }

        $view = $this->view($response, Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * @param string $trackingHash
     *
     * @return Response
     */
    public function replyAction($trackingHash)
    {
        /** @var Reply $replyService */
        $replyService = $this->get('mautic.message.processor.replier');

        /** @var RandomHelperInterface $randomHelper */
        $randomHelper = $this->get('mautic.helper.random');

        try {
            $replyService->createReplyByHash($trackingHash, "api-{$randomHelper->generate()}");
        } catch (EntityNotFoundException $e) {
            return $this->notFound($e->getMessage());
        }

        return $this->handleView(
            $this->view(['success' => true], Response::HTTP_CREATED)
        );
    }
}
