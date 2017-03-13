<?php
namespace MauticPlugin\MauticExtendedConditionsBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CampaignBundle\Model\EventModel;
use Mautic\LeadBundle\Event\LeadEvent;
use Mautic\LeadBundle\Event\ListChangeEvent;
use Mautic\LeadBundle\LeadEvents;
use Mautic\CampaignBundle\Model\CampaignModel;
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\PageBundle\Model\PageModel;
use Mautic\PageBundle\Entity\Hit;
use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignExecutionEvent;
use Mautic\CampaignBundle\Event\CampaignLeadChangeEvent;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use MauticPlugin\MauticExtendedConditionsBundle\ExtendedConditionsEvents;
use MauticPlugin\MauticSocialBundle\Entity\Lead;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\DBAL\Connection;
use Mautic\CoreBundle\Helper\CookieHelper;

class CampaignSubscriber extends CommonSubscriber
{

    /**
     * @var $campaignModel
     */
    protected $campaignModel;
    /**
     * @var $cookieHelper ;
     */
    protected $cookieHelper;

    /**
     * @var $db
     */
    protected $db;

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
        RequestStack $requestStack,
        Connection $db,
        CookieHelper $cookieHelper,
        CampaignModel $campaignModel
    ) {
        $this->campaignEventModel = $campaignEventModel;
        $this->leadModel = $leadModel;
        $this->session = $session;
        $this->pageModel = $pageModel;
        $this->request = $requestStack->getCurrentRequest();
        $this->db = $db;
        $this->cookieHelper = $cookieHelper;
        $this->campaignModel = $campaignModel;
    }


    static public function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD => ['onCampaignBuild', 0],
            CampaignEvents::CAMPAIGN_ON_LEADCHANGE => ['onCampaignLeadChange', 0],
            CampaignEvents::LEAD_CAMPAIGN_BATCH_CHANGE => ['onCampaignLeadChange', 0],
            ExtendedConditionsEvents::ON_CAMPAIGN_TRIGGER_DECISION => array('onCampaignTriggerDecision', 0),
            ExtendedConditionsEvents::ON_CAMPAIGN_TRIGGER_ACTION => array('onCampaignTriggerAction', 0),
        ];
    }


    /**
     * Add event triggers and actions.
     *
     * @param CampaignLeadChangeEvent $event
     */
    public function onCampaignLeadChange(CampaignLeadChangeEvent $event)
    {
        $leadsFromEvent = $event->getLeads();
        if ($leadsFromEvent) {
            foreach ($leadsFromEvent as $leadFromEvent) {
                $lead = $this->leadModel->getEntity($leadFromEvent);
                $this->leadModel->setSystemCurrentLead($lead);
                $this->campaignEventModel->triggerEvent(
                    'extendedconditions.on_change_campaign',
                    new CampaignLeadChangeEvent(
                        $event->getCampaign(),
                        $lead,
                        $event->wasAdded() ? true : false
                    )
                );
            }
        } else {
            $this->campaignEventModel->triggerEvent('extendedconditions.on_change_campaign', $event);
        }
    }

    /**
     * Add event triggers and actions.
     *
     * @param CampaignBuilderEvent $event
     */
    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        $trigger = [
            'label' => 'plugin.extended.conditions.campaign.event.on_change_segment',
            'description' => 'plugin.extended.conditions.campaign.event.on_change_segment.description',
            'formType' => 'extendedconditionsnevent_on_change_segment',
            'eventName' => ExtendedConditionsEvents::ON_CAMPAIGN_TRIGGER_DECISION,
            'channel' => 'page',
            'channelIdField' => 'pages',
        ];
        $event->addDecision('extendedconditions.on_change_segment', $trigger);

        $trigger = [
            'label' => 'plugin.extended.conditions.campaign.event.on_change_campaign',
            'description' => 'plugin.extended.conditions.campaign.event.on_change_campaign.description',
            'formType' => 'extendedconditionsnevent_on_change_campaign',
            'eventName' => ExtendedConditionsEvents::ON_CAMPAIGN_TRIGGER_DECISION,
            'channel' => 'page',
            'channelIdField' => 'pages',
        ];
        $event->addDecision('extendedconditions.on_change_campaign', $trigger);

        $trigger = [
            'label' => 'plugin.extended.conditions.campaign.event.last_active',
            'description' => 'plugin.extended.conditions.campaign.event.last_active.description',
            'formType' => 'extendedconditionsnevent_last_active',
            'eventName' => ExtendedConditionsEvents::ON_CAMPAIGN_TRIGGER_DECISION,
            'channel' => 'page',
            'channelIdField' => 'pages',
        ];
        $event->addDecision('extendedconditions.last_active_condition', $trigger);

        $trigger = [
            'label' => 'plugin.extended.conditions.campaign.event.page_session',
            'description' => 'plugin.extended.conditions.campaign.event.page_session.description',
            'formType' => 'extendedconditionsnevent_page_session',
            'eventName' => ExtendedConditionsEvents::ON_CAMPAIGN_TRIGGER_DECISION,
            'channel' => 'page',
            'channelIdField' => 'pages',
        ];
        $event->addDecision('extendedconditions.page_session', $trigger);

        $trigger = [
            'label' => 'plugin.extended.conditions.campaign.event.click',
            'description' => 'plugin.extended.conditions.campaign.event.click.description',
            'formType' => 'extendedconditionsnevent_click',
            'eventName' => ExtendedConditionsEvents::ON_CAMPAIGN_TRIGGER_DECISION,
            'channel' => 'page',
            'channelIdField' => 'pages',
        ];
        $event->addDecision('extendedconditions.click_condition', $trigger);

        $trigger = [
            'label' => 'plugin.extended.conditions.campaign.event.dynamic',
            'description' => 'plugin.extended.conditions.campaign.event.dynamic.description',
            'formType' => 'extendedconditionsnevent_dynamic',
            'eventName' => ExtendedConditionsEvents::ON_CAMPAIGN_TRIGGER_DECISION,
            'channel' => 'page',
            'channelIdField' => 'pages',
        ];
        $event->addDecision('extendedconditions.dynamic_condition', $trigger);


        $action = [
            'label' => 'plugin.extended.conditions.campaign.event.dynamic.stop',
            'eventName' => ExtendedConditionsEvents::ON_CAMPAIGN_TRIGGER_ACTION,
            'formType' => 'extendedconditionsnevent_dynamic_stop',
            'channel' => 'dynamic',
            'channelIdField' => 'slots',
        ];
        $event->addAction('extendedconditions.dynamic_stop', $action);

        $action = [
            'label' => 'plugin.extended.conditions.campaign.event.remove.logs',
            'eventName' => ExtendedConditionsEvents::ON_CAMPAIGN_TRIGGER_ACTION,
            'formType' => 'extendedconditionsnevent_remove_logs',
            'channel' => 'campaign',
            'channelIdField' => 'campaign_id',
        ];
        $event->addAction('extendedconditions.campaign_logs_remove', $action);
    }

    /**
     * @param CampaignExecutionEvent $event
     */
    public function onCampaignTriggerDecision(CampaignExecutionEvent $event)
    {
        $eventConfig = $event->getConfig();
        $eventDetails = $event->getEventDetails();
        $lead = $event->getLead();

        if ($event->checkContext('extendedconditions.on_change_segment')) {
            $check = 'SEGMENT_CHANGED_'.$event->getEvent()['id'].'_'.$lead->getId();
            if (!defined($check) && $lead->getId() && $eventDetails instanceof ListChangeEvent) {
                $leadFromEvent = $eventDetails->getLead();

                if ($leadFromEvent->getId() == $lead->getId()) {
                    $actionListId = $eventDetails->getList()->getId();
                    if ($eventDetails->wasAdded()) {
                        $lists = $eventConfig['addedSegments'];
                    } elseif ($eventDetails->wasRemoved()) {
                        $lists = $eventConfig['removedSegments'];
                    }
                    if (!empty($lists) && in_array($actionListId, $lists)) {
                        define($check, 1);

                        return $event->setResult(true);
                    }
                }
            }

            return $event->setResult(false);
        } elseif ($event->checkContext('extendedconditions.on_change_campaign')) {

            $check = 'CAMPAIGN_CHANGED_'.$event->getEvent()['id'].'_'.$lead->getId();
            if (!defined($check) && $eventDetails instanceof CampaignLeadChangeEvent) {
                $leadFromEvent = $eventDetails->getLead();
                if ($leadFromEvent->getId() == $lead->getId()) {
                    $actionListId = $eventDetails->getCampaign()->getId();
                    if ($eventDetails->wasAdded()) {
                        $campaigns = $eventConfig['addedCampaigns'];
                    } elseif ($eventDetails->wasRemoved()) {
                        $campaigns = $eventConfig['removedCampaigns'];
                    }
                    if (!empty($campaigns) && in_array($actionListId, $campaigns)) {
                        define($check, 1);

                        return $event->setResult(true);
                    }
                }
            }

            return $event->setResult(false);
        } elseif ($event->checkContext('extendedconditions.last_active_condition')) {
            if ($eventConfig['last_active_limit'] < $eventDetails) {
                return $event->setResult(true);
            } else {
                return $event->setResult(false);
            }
        } elseif ($event->checkContext('extendedconditions.page_session')) {
            if ($eventDetails instanceof Hit) {
                $hit = $eventDetails;
                $lead = $hit->getLead();
                // just one time
                if (!$this->request->cookies->get('page_session_check') || 1==1) {
                    $sessionTimeLimit = $eventConfig['page_session_time_limit'] ?: 30;
                    // is session
                    $qb = $this->db->createQueryBuilder();
                    $latestDateHit = $qb->select('date_hit')
                        ->from(MAUTIC_TABLE_PREFIX.'page_hits', 'ph')
                        ->where(
                            $qb->expr()->andX(
                                $qb->expr()->eq('ph.lead_id', ':leadId'),
                                $qb->expr()->isNull('ph.page_id'),
                                $qb->expr()->isNull('ph.redirect_id'),
                                $qb->expr()->isNull('ph.email_id')
                            )
                        )
                        ->setParameter('leadId', $lead->getId())
                        ->orderBy('ph.id', 'DESC')
                        ->setFirstResult(1)
                        ->setMaxResults(1)
                        ->execute()
                        ->fetchColumn();

                    $from_time = strtotime($latestDateHit);
                    $to_time = strtotime($hit->getDateHit()->format("Y-m-d H:i:s"));
                    $difference = round(abs($to_time - $from_time) / 60, 2);

                    // just check once per sesssion timout - performance
                    $this->cookieHelper->setCookie('page_session_check', $difference, $sessionTimeLimit * 60);
                    // not new session, continue
                    if ($sessionTimeLimit > $difference) {
                        return $event->setResult(false);
                    }

                    //sesssioncount
                    //session sub query
                    $sessionCountsMin = $eventConfig['page_session_min_count'] ?: 0;
                    $sessionCountsMax = $eventConfig['page_session_max_count'] ?: 0;
                    if ($sessionCountsMin || $sessionCountsMax) {
                        $alias = 'ph';
                        $alias2 = 'ph2';
                        $qb2 = $this->db->createQueryBuilder();
                        $qb2->select($alias2.'.id')
                            ->from(MAUTIC_TABLE_PREFIX.'page_hits', $alias2)
                            ->where(
                                $qb2->expr()
                                    ->andX(
                                        $qb2->expr()->eq($alias2.'.lead_id', 'ph.lead_id'),
                                        $qb2->expr()->isNull('ph2.page_id'),
                                        $qb2->expr()->isNull('ph2.redirect_id'),
                                        $qb2->expr()->isNull('ph2.email_id'),
                                        $qb2->expr()->lt($alias2.'.date_hit', $alias.'.date_hit'),
                                        $qb2->expr()->gt(
                                            $alias2.'.date_hit',
                                            '('.$alias.'.date_hit - INTERVAL '.$sessionTimeLimit.' MINUTE)'
                                        )
                                    )
                            );

                        //session main query
                        $qb = $this->db->createQueryBuilder();
                        $sessionCounts = $qb->select('COUNT(id)')
                            ->from(MAUTIC_TABLE_PREFIX.'page_hits', 'ph')
                            ->where(
                                $qb->expr()->andX(
                                    $qb->expr()->eq('ph.lead_id', ':leadId'),
                                    $qb->expr()->isNull('ph.page_id'),
                                    $qb->expr()->isNull('ph.redirect_id'),
                                    $qb->expr()->isNull('ph.email_id'),
                                    sprintf('%s (%s)', 'NOT EXISTS', $qb2->getSQL())
                                )
                            )
                            ->setParameter('leadId', $lead->getId())
                            ->execute()
                            ->fetchColumn();

                        if (($sessionCountsMin > 0 && $sessionCountsMin > $sessionCounts) || ($sessionCountsMax > 0 && $sessionCountsMax < $sessionCounts)) {
                            return $event->setResult(false);
                        }
                    }


                    //referrer chceck
                    $limitToUrl = str_replace('\|', '|', preg_quote(trim($eventConfig['page_session_referrer']), '/'));
                    $currentUrl = $this->request->server->get('HTTP_REFERER');
                    if ($limitToUrl && !preg_match('/'.$limitToUrl.'/', $currentUrl)) {
                        return $event->setResult(false);
                    }

                    // referrer in history check
                    $referer = trim($eventConfig['page_session_history_referrer']);
                    if ($referer) {
                        $qb = $this->db->createQueryBuilder();
                        $existsReferer = $qb->select('date_hit')
                            ->from(MAUTIC_TABLE_PREFIX.'page_hits', 'ph')
                            ->where(
                                $qb->expr()->andX(
                                    $qb->expr()->eq('ph.lead_id', ':leadId'),
                                    $qb->expr()->isNull('ph.page_id'),
                                    $qb->expr()->isNull('ph.redirect_id'),
                                    $qb->expr()->isNull('ph.email_id'),
                                    $qb->expr()->lt('ph.id', $hit->getId()),
                                    'ph.referer  REGEXP :referer'
                                )
                            )
                            ->setParameter('leadId', $lead->getId())
                            ->setParameter('referer', $referer)
                            ->orderBy('ph.id', 'DESC')
                            ->setMaxResults(1)
                            ->execute()
                            ->fetchColumn();

                        if (!$existsReferer) {
                            return $event->setResult(false);
                        }

                        // referer session sub query
                        $sessionCountsMin = $eventConfig['page_session_referrer_min_count'] ?: 0;
                        $sessionCountsMax = $eventConfig['page_session_referrer_max_count'] ?: 0;
                        if ($sessionCountsMin || $sessionCountsMax) {
                            $alias = 'ph';
                            $alias2 = 'ph2';
                            $qb2 = $this->db->createQueryBuilder();
                            $qb2->select($alias2.'.id')
                                ->from(MAUTIC_TABLE_PREFIX.'page_hits', $alias2)
                                ->where(
                                    $qb2->expr()
                                        ->andX(
                                            $qb2->expr()->eq($alias2.'.lead_id', 'ph.lead_id'),
                                            $qb2->expr()->isNull('ph2.page_id'),
                                            $qb2->expr()->isNull('ph2.redirect_id'),
                                            $qb2->expr()->isNull('ph2.email_id'),
                                            $qb2->expr()->lt($alias2.'.date_hit', $alias.'.date_hit'),
                                            $qb2->expr()->gt(
                                                $alias2.'.date_hit',
                                                '('.$alias.'.date_hit - INTERVAL '.$sessionTimeLimit.' MINUTE)'
                                            )
                                        )
                                );

                            //session main query
                            $qb = $this->db->createQueryBuilder();
                            $sessionCounts = $qb->select('COUNT(id)')
                                ->from(MAUTIC_TABLE_PREFIX.'page_hits', 'ph')
                                ->where(
                                    $qb->expr()->andX(
                                        $qb->expr()->eq('ph.lead_id', ':leadId'),
                                        $qb->expr()->isNull('ph.page_id'),
                                        $qb->expr()->isNull('ph.redirect_id'),
                                        $qb->expr()->isNull('ph.email_id'),
                                        'ph.referer  REGEXP :referer',
                                        sprintf('%s (%s)', 'NOT EXISTS', $qb2->getSQL())
                                    )
                                )
                                ->setParameter('leadId', $lead->getId())
                                ->setParameter('referer', $referer)
                                ->execute()
                                ->fetchColumn();
                            if (($sessionCountsMin > 0 && $sessionCountsMin > $sessionCounts) || ($sessionCountsMax > 0 && $sessionCountsMax < $sessionCounts)) {
                                return $event->setResult(false);
                            }
                        }
                    }

                    return $event->setResult(true);
                }
            }
            return $event->setResult(false);
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

            $slot = $eventDetails['slot'];
            $stop = isset($eventDetails['stop']) ? true : false;

            //go to false
            if ($stop) {
                //go true if stop
                return $event->setResult(true);
            }

            if ($slot) {
                $limitToUrl = str_replace(['\|','\$','\^'],['|','$','^'], preg_quote(trim($eventConfig['url']), '/'));
                $currentUrl = $this->request->server->get('HTTP_REFERER');
                // if url match
                preg_match('/'.$limitToUrl.'/', $currentUrl, $matches);
                if (!$limitToUrl || !empty($matches[0])) {
                    $this->session->set('dynamic.id.'.$slot.$lead->getId(), $eventConfig['dynamic_id']);
                }else{
                    $this->session->remove('dynamic.id.'.$slot.$lead->getId());
                }
            }
            return $event->setResult(false);
        }
    }


    /**
     * @param CampaignExecutionEvent $event
     */
    public function onCampaignTriggerAction(
        CampaignExecutionEvent $event
    ) {
        $lead = $event->getLead();
        $eventConfig = $event->getConfig();
        $eventDetails = $event->getEventDetails();
        if ($event->checkContext('extendedconditions.dynamic_stop')) {
            $slots = $eventConfig['slots'];
            if ($slots) {
                $slotsArray = explode(',', $slots);
                foreach ($slotsArray as $slot) {
                    $this->campaignEventModel->triggerEvent(
                        'extendedconditions.dynamic_condition',
                        ['slot' => $slot, 'stop' => true],
                        'extendedconditions.dynamic_condition.'.$slot
                    );
                }
            }

        } elseif ($event->checkContext('extendedconditions.campaign_logs_remove')) {
            $qb = $this->db;
            $qb->delete(
                MAUTIC_TABLE_PREFIX.'campaign_lead_event_log',
                [
                    'lead_id' => (int)$lead->getId(),
                    'campaign_id' => (int)$event->getEvent()['campaign']['id'],
                ]
            );
        }
    }
}
