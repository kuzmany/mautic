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
use Mautic\LeadBundle\LeadEvents;
use Mautic\CampaignBundle\Model\EventModel;

/**
 * Class PageSubscriber.
 */
class LeadSubscriber extends CommonSubscriber
{
    /**
     * @var EventModel
     */
    protected $campaignEventModel;

    /**
     * CampaignSubscriber constructor.
     *
     * @param EventModel $campaignEventModel
     */
    public function __construct(EventModel $campaignEventModel)
    {
        $this->campaignEventModel = $campaignEventModel;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            LeadEvents::LEAD_PRE_SAVE => ['onLeadPreSave', 0],
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
        if (isset($changes['dateLastActive']) && count($changes['dateLastActive']) == 2) {

            $difference = ($changes['dateLastActive'][0])->diff($changes['dateLastActive'][1])->format('%m');
            if($difference > 30){
                $this->campaignEventModel->triggerEvent('extendedconditions.last_active_condition', true);
            } else {
                $this->campaignEventModel->triggerEvent('extendedconditions.last_active_condition', false);
            }
        }
    }
}
