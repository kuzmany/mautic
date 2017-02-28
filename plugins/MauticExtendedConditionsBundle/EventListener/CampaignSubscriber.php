<?php
namespace MauticPlugin\MauticExtendedConditionsBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CampaignBundle\Model\EventModel;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\MauticExtendedConditionsBundle\ExtendedConditionsEvents;
use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignExecutionEvent;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;

class CampaignSubscriber extends CommonSubscriber
{

    /**
     * @var LeadModel
     */
    protected $leadModel;

    /**
     * @var EventModel
     */
    protected $campaignEventModel;

    /**
     * CampaignSubscriber constructor.
     *
     * @param EventModel $campaignEventModel
     */
    public function __construct(EventModel $campaignEventModel, LeadModel $leadModel)
    {
        $this->campaignEventModel = $campaignEventModel;
        $this->leadModel = $leadModel;
    }


    static public function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD => ['onCampaignBuild', 0],
            ExtendedConditionsEvents::ON_CAMPAIGN_TRIGGER_DECISION => array('onCampaignTriggerDecision', 0),
        ];
    }


    /**
     * Add event triggers and actions.
     *
     * @param CampaignBuilderEvent $event
     */
    public function onCampaignBuild(CampaignBuilderEvent $event)
    {

        $pageHitTrigger = [
            'label' => 'plugin.extended.conditions.campaign.event.last_active',
            'description' => 'plugin.extended.conditions.campaign.event.last_active.description',
            'formType' => 'extendedconditionsnevent_last_active',
            'eventName' => ExtendedConditionsEvents::ON_CAMPAIGN_TRIGGER_DECISION,
            'channel' => 'page',
            'channelIdField' => 'pages',
        ];
        $event->addDecision('extendedconditions.last_active_condition', $pageHitTrigger);
    }


    /**
     * @param CampaignExecutionEvent $event
     */
    public function onCampaignTriggerDecision(CampaignExecutionEvent $event)
    {
        $eventConfig = $event->getConfig();
        $eventDetails = $event->getEventDetails();
        if ($eventConfig['last_active_limit'] < $eventDetails) {
            return $event->setResult(true);
        }
    }

}
