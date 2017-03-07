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
    public function __construct(EventModel $campaignEventModel, LeadModel $leadModel, oreParametersHelper $coreParametersHelper)
    {
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
        ];
    }

    /**
     * Trigger actions for page hits.
     *
     * @param PageHitEvent $event
     */
    public function onLeadPreSave(LeadEvent $event)
    {
        static $difference = null;
        $lead = $event->getLead();
        $changes = $lead->getChanges();
        if ($difference == null) {
            // old date, new date
            if (isset($changes['dateLastActive']) && count($changes['dateLastActive']) == 2 && is_object($changes['dateLastActive'][0])  && is_object($changes['dateLastActive'][1])){
                $difference = (($changes['dateLastActive'][1])->diff($changes['dateLastActive'][0]))->format('%m');
                    $this->campaignEventModel->triggerEvent('extendedconditions.last_active_condition', $difference);
            }else if (isset($changes['dateLastActive']) && count($changes['dateLastActive']) == 2 && !is_object($changes['dateLastActive'][0])  && is_object($changes['dateLastActive'][1])){
                // new contact
                $lists = $this->coreParametersHelper->getParameter('lists');
                if(!empty($lists) && is_array($lists)){
                    foreach($lists as $list){
                        $this->leadModel->addToLists($lead, $list);
                    }
                }
            }
        }
    }
}
