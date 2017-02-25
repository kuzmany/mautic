<?php
namespace MauticPlugin\MauticExtendedConditionsBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CampaignBundle\Model\EventModel;
use MauticPlugin\MauticExtendedConditionsBundle\ExtendedConditionsEvents;
use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use Mautic\PageBundle\Entity\Page;
use Mautic\PageBundle\Event\PageHitEvent;
use Mautic\PageBundle\PageEvents;
use Mautic\PageBundle\Model\PageModel;

class CampaignSubscriber extends CommonSubscriber
{

    /**
     * @var PageModel
     */
    protected $pageModel;

    /**
     * @var EventModel
     */
    protected $campaignEventModel;

    /**
     * CampaignSubscriber constructor.
     *
     * @param PageModel  $pageModel
     * @param EventModel $campaignEventModel
     */
    public function __construct(PageModel $pageModel, EventModel $campaignEventModel)
    {
        $this->pageModel          = $pageModel;
        $this->campaignEventModel = $campaignEventModel;
    }


    static public function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD        => ['onCampaignBuild', 0],
            ExtendedConditionsEvents::ON_CAMPAIGN_TRIGGER_DECISION  => array('onCampaignTriggerDecision', 0)
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
            'label'          => 'plugin.extended.conditions.campaign.event.last_active',
            'description'    => 'plugin.extended.conditions.campaign.event.last_active.description',
            'formType'       => 'extendedconditionsnevent_last_active',
            'eventName'      => ExtendedConditionsEvents::ON_CAMPAIGN_TRIGGER_DECISION,
            'channel'        => 'page',
            'channelIdField' => 'pages',
        ];
        $event->addDecision('extendedconditions.last_active_condition', $pageHitTrigger);
    }


    /**
     * @param CampaignExecutionEvent $event
     */
    public function onCampaignTriggerDecision(CampaignExecutionEvent $event)
    {
        $eventDetails = $event->getEventDetails();
        $config       = $event->getConfig();

        die('test');
        //return $event->setResult(true);
    }

}
