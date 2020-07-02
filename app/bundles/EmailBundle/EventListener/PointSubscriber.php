<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\EmailBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Entity\Email;
use Mautic\EmailBundle\Event\EmailOpenEvent;
use Mautic\EmailBundle\Event\EmailSendEvent;
use Mautic\EmailBundle\Form\Type\EmailToUserType;
use Mautic\EmailBundle\Form\Type\PointActionEmailOpenType;
use Mautic\EmailBundle\Form\Type\PointActionEmailSendType;
use Mautic\EmailBundle\Helper\PointEventHelper;
use Mautic\EmailBundle\Model\EmailModel;
use Mautic\PointBundle\Event\PointBuilderEvent;
use Mautic\PointBundle\Event\PointChangeActionExecutedEvent;
use Mautic\PointBundle\Event\TriggerBuilderEvent;
use Mautic\PointBundle\Model\PointModel;
use Mautic\PointBundle\PointEvents;

/**
 * Class PointSubscriber.
 */
class PointSubscriber extends CommonSubscriber
{
    /**
     * @var PointModel
     */
    protected $pointModel;

    /**
     * @var EmailModel
     */
    private $emailModel;

    /**
     * PointSubscriber constructor.
     *
     * @param PointModel $pointModel
     * @param EmailModel $emailModel
     */
    public function __construct(PointModel $pointModel, EmailModel $emailModel)
    {
        $this->pointModel = $pointModel;
        $this->emailModel = $emailModel;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PointEvents::POINT_ON_BUILD                     => ['onPointBuild', 0],
            PointEvents::TRIGGER_ON_BUILD                   => ['onTriggerBuild', 0],
            EmailEvents::EMAIL_ON_OPEN                      => ['onEmailOpen', 0],
            EmailEvents::EMAIL_ON_SEND                      => ['onEmailSend', 0],
            EmailEvents::ON_POINT_CHANGE_ACTION_EXECUTED    => [
                ['onEmailOpenPointChange', 0],
                ['onEmailSentPointChange', 1],
            ],
        ];
    }

    /**
     * @param PointBuilderEvent $event
     */
    public function onPointBuild(PointBuilderEvent $event)
    {
        $action = [
            'group'     => 'mautic.email.actions',
            'label'     => 'mautic.email.point.action.open',
            'eventName' => EmailEvents::ON_POINT_CHANGE_ACTION_EXECUTED,
            'formType'  => PointActionEmailOpenType::class,
        ];

        $event->addAction('email.open', $action);

        $action = [
            'group'     => 'mautic.email.actions',
            'label'     => 'mautic.email.point.action.send',
            'eventName' => EmailEvents::ON_POINT_CHANGE_ACTION_EXECUTED,
            'formType'  => PointActionEmailSendType::class,
        ];

        $event->addAction('email.send', $action);
    }

    /**
     * @param TriggerBuilderEvent $event
     */
    public function onTriggerBuild(TriggerBuilderEvent $event)
    {
        $sendEvent = [
            'group'           => 'mautic.email.point.trigger',
            'label'           => 'mautic.email.point.trigger.sendemail',
            'callback'        => ['\\Mautic\\EmailBundle\\Helper\\PointEventHelper', 'sendEmail'],
            'formType'        => 'emailsend_list',
            'formTypeOptions' => ['update_select' => 'pointtriggerevent_properties_email'],
            'formTheme'       => 'MauticEmailBundle:FormTheme\EmailSendList',
        ];

        $event->addEvent('email.send', $sendEvent);

        $sendToOwnerEvent = [
          'group'           => 'mautic.email.point.trigger',
          'label'           => 'mautic.email.point.trigger.send_email_to_user',
          'formType'        => EmailToUserType::class,
          'formTypeOptions' => ['update_select' => 'pointtriggerevent_properties_email'],
          'formTheme'       => 'MauticEmailBundle:FormTheme\EmailSendList',
          'eventName'       => EmailEvents::ON_SENT_EMAIL_TO_USER,
        ];

        $event->addEvent('email.send_to_user', $sendToOwnerEvent);
    }

    /**
     * Trigger point actions for email open.
     *
     * @param EmailOpenEvent $event
     */
    public function onEmailOpen(EmailOpenEvent $event)
    {
        $this->pointModel->triggerAction('email.open', $event->getEmail());
    }

    /**
     * Trigger point actions for email send.
     *
     * @param EmailSendEvent $event
     */
    public function onEmailSend(EmailSendEvent $event)
    {
        if ($leadArray = $event->getLead()) {
            $lead = $this->em->getReference('MauticLeadBundle:Lead', $leadArray['id']);
        } else {
            return;
        }

        $this->pointModel->triggerAction('email.send', $event->getEmail(), null, $lead);
    }

    /**
     * @param PointChangeActionExecutedEvent $changeActionExecutedEvent
     */
    public function onEmailOpenPointChange(PointChangeActionExecutedEvent $changeActionExecutedEvent)
    {
        $action = $changeActionExecutedEvent->getPointAction();

        if ($action->getType() != 'email.open') {
            return;
        }
        /** @var Email $eventDetails */
        $eventDetails = $changeActionExecutedEvent->getEventDetails();

        if (!PointEventHelper::validateEmail($eventDetails, $action->convertToArray())) {
            $changeActionExecutedEvent->setFailed();

            return;
        }
        $triggerMode = isset($action->getProperties()['triggerMode']) ? $action->getProperties()['triggerMode'] : null;
        if ($triggerMode == 'internalId') {
            $changeActionExecutedEvent->setStatusFromLogsForInternalId($eventDetails->getId());

            return;
        }

        $changeActionExecutedEvent->setStatusFromLogs();
    }

    /**
     * @param PointChangeActionExecutedEvent $changeActionExecutedEvent
     */
    public function onEmailSentPointChange(PointChangeActionExecutedEvent $changeActionExecutedEvent)
    {
        $action = $changeActionExecutedEvent->getPointAction();

        if ($action->getType() != 'email.send') {
            return;
        }

        /** @var Email $eventDetails */
        $eventDetails = $changeActionExecutedEvent->getEventDetails();

        if (!PointEventHelper::validateEmail($eventDetails, $action->convertToArray())) {
            $changeActionExecutedEvent->setFailed();

            return;
        }

        $changeActionExecutedEvent->setStatusFromLogs();
    }
}
