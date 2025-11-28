<?php

namespace Mautic\FormBundle\Model;

use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Helper\IpLookupHelper;
use Mautic\CoreBundle\Helper\UserHelper;
use Mautic\CoreBundle\Model\AbstractCommonModel;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Mautic\CoreBundle\Translation\Translator;
use Mautic\FormBundle\Entity\AbandonedSubmission;
use Mautic\FormBundle\Entity\AbandonedSubmissionRepository;
use Mautic\FormBundle\Entity\Form;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Tracker\ContactTracker;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @extends AbstractCommonModel<AbandonedSubmission>
 */
class AbandonedSubmissionModel extends AbstractCommonModel
{
    public function __construct(
        private IpLookupHelper $ipLookupHelper,
        private ContactTracker $contactTracker,
        EntityManager $em,
        CorePermissions $security,
        EventDispatcherInterface $dispatcher,
        UrlGeneratorInterface $router,
        Translator $translator,
        UserHelper $userHelper,
        LoggerInterface $mauticLogger,
        CoreParametersHelper $coreParametersHelper,
    ) {
        parent::__construct($em, $security, $dispatcher, $router, $translator, $userHelper, $mauticLogger, $coreParametersHelper);
    }

    public function getRepository(): AbandonedSubmissionRepository
    {
        return $this->em->getRepository(AbandonedSubmission::class);
    }

    public function saveAbandoned(Form $form, array $data, ?string $sessionId = null): AbandonedSubmission
    {
        $lead = $this->contactTracker->getContact();
        $lead = ($lead instanceof Lead && $lead->getId()) ? $lead : null;

        $entity = $this->getRepository()->findExisting($form, $lead, $sessionId) ?? new AbandonedSubmission();
        if (!$entity->getId()) {
            $entity->setForm($form);
        }

        $entity
            ->setDateModified(new \DateTime())
            ->setData($data)
            ->setSessionId($sessionId)
            ->setLead($lead)
            ->setIpAddress($this->ipLookupHelper->getIpAddress());

        $this->saveEntity($entity);

        return $entity;
    }

    public function deleteFor(Form $form, ?Lead $lead, ?string $sessionId): void
    {
        $this->getRepository()->deleteForForm($form, $lead, $sessionId);
    }
}
