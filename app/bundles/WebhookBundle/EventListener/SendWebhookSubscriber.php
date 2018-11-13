<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\WebhookBundle\EventListener;

use Joomla\Http\Http;
use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event as Events;
use Mautic\CampaignBundle\Event\CampaignExecutionEvent;
use Mautic\CampaignBundle\Executioner\RealTimeExecutioner;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\WebhookBundle\Event\SendWebhookEvent;
use Mautic\WebhookBundle\Form\Type\CampaignEventSendWebhookResponseType;
use Mautic\WebhookBundle\WebhookEvents;

class SendWebhookSubscriber extends CommonSubscriber
{
    /**
     * @var RealTimeExecutioner
     */
    private $executioner;

    /**
     * SendWebhookSubscriber constructor.
     *
     * @param RealTimeExecutioner $executioner
     */
    public function __construct(RealTimeExecutioner $executioner)
    {
        $this->executioner = $executioner;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD           => ['onCampaignBuild', 0],
            WebhookEvents::ON_SEND_WEBHOOK              => ['onSendWebhook', 0],
            WebhookEvents::ON_CAMPAIGN_TRIGGER_DECISION => ['onCampaignTriggerDecision', 0],
        ];
    }

    /**
     * Add event triggers and actions.
     *
     * @param Events\CampaignBuilderEvent $event
     */
    public function onCampaignBuild(Events\CampaignBuilderEvent $event)
    {
        //Add action to remote url call
        $sendWebhookDecision = [
            'label'       => 'mautic.webhook.event.sendwebhook.response',
            'description' => 'mautic.webhook.event.sendwebhook_desc.response',
            'formType'    => CampaignEventSendWebhookResponseType::class,
            'eventName'   => WebhookEvents::ON_CAMPAIGN_TRIGGER_DECISION,
        ];
        $event->addDecision('send.webhook', $sendWebhookDecision);
    }

    /**
     * @param SendWebhookEvent $event
     */
    public function onSendWebhook(SendWebhookEvent $event)
    {
        $this->executioner->execute('send.webhook', $event->getResponse()->body);
    }

    /**
     * @param CampaignExecutionEvent $event
     */
    public function onCampaignTriggerDecision(CampaignExecutionEvent $event)
    {
        $eventDetails = $event->getEventDetails();
        $config       = $event->getConfig();

        if (!$event->checkContext('send.webhook')) {
            return false;
        }
        if ($config['match'] === $eventDetails) {
            return $event->setResult(true);
        }

        return $event->setResult(false);
    }
}
