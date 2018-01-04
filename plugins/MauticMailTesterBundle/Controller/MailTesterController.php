<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticMailTesterBundle\Controller;

use Mautic\CoreBundle\Controller\FormController;

class MailTesterController extends FormController
{
    /**
     * Generating the modal box content for
     * the send multiple example email option.
     */
    public function sendMailTestAction($objectId)
    {
        $model  = $this->getModel('email');
        $entity = $model->getEntity($objectId);

        // Prepare a fake lead
        /** @var \Mautic\LeadBundle\Model\FieldModel $fieldModel */
        $fieldModel = $this->getModel('lead.field');
        $fields     = $fieldModel->getFieldList(false, false);
        array_walk(
            $fields,
            function (&$field) {
                $field = "[$field]";
            }
        );
        $fields['id'] = 0;
        $errors       = [];

        $clientId = md5($this->get('mautic.helper.user')->getUser()->getEmail().
            $this->coreParametersHelper->getParameter('site_url'));
        $uniqueId = 'mautic-'.$clientId.'-'.time();
        $email    = $uniqueId.'@mail-tester.com';

        $users = [
            [
                // Setting the id, firstname and lastname to null as this is a unknown user
                'id'        => '',
                'firstname' => '',
                'lastname'  => '',
                'email'     => $email,
            ],
        ];

        // Send to current user
        $error = $model->sendSampleEmailToUser($entity, $users, $fields, [], [], false);
        if (count($error)) {
            array_push($errors, $error[0]);
        }

        $this->addFlash('mautic.email.notice.test_sent_multiple.success');

        return $this->postActionRedirect(
            [
                'returnUrl' => 'https://www.mail-tester.com/'.$uniqueId,
            ]
        );
    }
}
