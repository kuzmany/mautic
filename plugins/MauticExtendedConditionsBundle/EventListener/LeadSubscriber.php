<?php

/*
 * @copyright   2015 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendedConditionsBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Event\LeadEvent;
use Mautic\LeadBundle\Event\ListChangeEvent;
use Mautic\LeadBundle\LeadEvents;
use Mautic\CampaignBundle\Model\EventModel;
use Symfony\Component\Validator\Constraints\DateTime;
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\CoreBundle\Helper\CoreParametersHelper;

/**
 * Class PageSubscriber.
 */
class LeadSubscriber extends CommonSubscriber
{

    /**
     * @var CoreParametersHelper
     */
    protected $coreParametersHelper;

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
    public function __construct(
        EventModel $campaignEventModel,
        LeadModel $leadModel,
        CoreParametersHelper $coreParametersHelper
    ) {
        $this->campaignEventModel = $campaignEventModel;
        $this->leadModel = $leadModel;
        $this->coreParametersHelper = $coreParametersHelper;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::LEAD_PRE_SAVE => ['onLeadPreSave', 0],
            LeadEvents::LEAD_LIST_CHANGE => ['onLeadListChange', 0],
            LeadEvents::LEAD_LIST_BATCH_CHANGE => ['onLeadListChange', 0],
        ];
    }

    /**
     * Trigger actions for page hits.
     *
     * @param PageHitEvent $event
     */
    public function onLeadPreSave(LeadEvent $event)
    {
        $lead = $event->getLead();
        $changes = $lead->getChanges();
        // old date, new date
        if (isset($changes['dateLastActive']) && count(
                $changes['dateLastActive']
            ) == 2 && is_object($changes['dateLastActive'][0]) && is_object($changes['dateLastActive'][1])
        ) {
            static $difference = null;
            if ($difference == null) {
                //    echo $this->request->get('page_url');
                // die(print_r($this->request->));
                $to_time = strtotime($changes['dateLastActive'][1]->format("Y-d-m H:i:s"));
                $from_time = strtotime($changes['dateLastActive'][0]->format("Y-d-m H:i:s"));
                $difference = round(abs($to_time - $from_time) / 60, 2);
                $this->campaignEventModel->triggerEvent('extendedconditions.last_active_condition', $difference);
            }

        } elseif (isset($changes['dateLastActive']) && count($changes['dateLastActive']) == 2 && !is_object(
                $changes['dateLastActive'][0]
            ) && is_object($changes['dateLastActive'][1])
        ) {
            // new contact
            $lists = $this->coreParametersHelper->getParameter('lists');
            if (!empty($lists) && is_array($lists)) {
                foreach ($lists as $list) {
                    $this->leadModel->addToLists($lead, [$list]);
                }
            }
        }
    }

    /**
     * Trigger actions on lead list change
     *
     * @param ListChangeEvent $event
     */
    public function onLeadListChange(ListChangeEvent $event)
    {
        $leadsFromEvent = $event->getLeads();
        if ($leadsFromEvent) {
            foreach ($leadsFromEvent as $leadFromEvent) {
                $lead = $this->leadModel->getEntity($leadFromEvent);
                $this->leadModel->setSystemCurrentLead($lead);
                $this->campaignEventModel->triggerEvent(
                    'extendedconditions.on_change_segment',
                    new ListChangeEvent(
                        $lead,
                        $event->getList(),
                        $event->wasAdded() ? true : false
                    )
                );
            }
        } else {
            $this->campaignEventModel->triggerEvent('extendedconditions.on_change_segment', $event);
        }

    }
}
