<?php
namespace MauticPlugin\MauticExtendedConditionsBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CampaignBundle\Model\EventModel;
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\PageBundle\Model\PageModel;
use Mautic\PageBundle\Entity\Hit;
use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignExecutionEvent;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use MauticPlugin\MauticExtendedConditionsBundle\ExtendedConditionsEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

class CampaignSubscriber extends CommonSubscriber
{

    /**
     * @var $request
     */
    protected $request;

    /**
     * @var $sesion
     */
    protected $sesion;

    /**
     * @var LeadModel
     */
    protected $leadModel;

    /**
     * @var EventModel
     */
    protected $campaignEventModel;

    /**
     * @var $pageModel
     */
    protected $pageModel;

    /**
     * CampaignSubscriber constructor.
     *
     * @param EventModel $campaignEventModel
     */
    public function __construct(
        EventModel $campaignEventModel,
        LeadModel $leadModel,
        Session $session,
        PageModel $pageModel,
        RequestStack $requestStack
    ) {
        $this->campaignEventModel = $campaignEventModel;
        $this->leadModel = $leadModel;
        $this->session = $session;
        $this->pageModel = $pageModel;
        $this->request = $requestStack->getCurrentRequest();
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

        $pageHitTrigger = [
            'label' => 'plugin.extended.conditions.campaign.event.click',
            'description' => 'plugin.extended.conditions.campaign.event.click.description',
            'formType' => 'extendedconditionsnevent_click',
            'eventName' => ExtendedConditionsEvents::ON_CAMPAIGN_TRIGGER_DECISION,
            'channel' => 'page',
            'channelIdField' => 'pages',
        ];
        $event->addDecision('extendedconditions.click_condition', $pageHitTrigger);

        $pageHitTrigger = [
            'label' => 'plugin.extended.conditions.campaign.event.dynamic',
            'description' => 'plugin.extended.conditions.campaign.event.dynamic.description',
            'formType' => 'extendedconditionsnevent_dynamic',
            'eventName' => ExtendedConditionsEvents::ON_CAMPAIGN_TRIGGER_DECISION,
            'channel' => 'page',
            'channelIdField' => 'pages',
        ];
        $event->addDecision('extendedconditions.dynamic_condition', $pageHitTrigger);
    }


    /**
     * @param CampaignExecutionEvent $event
     */
    public function onCampaignTriggerDecision(CampaignExecutionEvent $event)
    {
        $eventConfig = $event->getConfig();
        $eventDetails = $event->getEventDetails();

        if ($event->checkContext('extendedconditions.last_active_condition')) {
            if ($eventConfig['last_active_limit'] < $eventDetails) {
                return $event->setResult(true);
            } else {
                return $event->setResult(false);
            }
        } elseif ($event->checkContext('extendedconditions.click_condition')) {
            if (is_object($eventDetails)) {
                $hit = $eventDetails;
                if ($eventConfig['source'] == $hit->getSource() && $eventConfig['source_id'] == $hit->getSourceId()) {
                    return $event->setResult(true);
                } else {
                    return $event->setResult(false);
                }
            }
        } elseif ($event->checkContext('extendedconditions.dynamic_condition')) {

            $lead = $this->leadModel->getCurrentLead();
            $limitToUrl = html_entity_decode(trim($eventConfig['url']));
            $currentUrl = $this->request->server->get('HTTP_REFERER');
            if (!$limitToUrl || !fnmatch($limitToUrl, $currentUrl)) {
                $this->session->remove('dynamic.id.'.$eventConfig['slot'].$lead->getId());

                return $event->setResult(false);
            }
            $this->session->set('dynamic.id.'.$eventConfig['slot'].$lead->getId(), $eventConfig['dynamic_id']);

            return $event->setResult(true);
        }
    }
}
