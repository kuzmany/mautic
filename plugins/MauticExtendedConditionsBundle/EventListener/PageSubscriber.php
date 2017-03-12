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

use Guzzle\Plugin\Cookie\Cookie;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\PageBundle\Event as Events;
use Mautic\PageBundle\PageEvents;
use Mautic\LeadBundle\LeadEvent;
use Mautic\PageBundle\Event\PageHitEvent;
use Mautic\PageBundle\Event\PageEvent;
use Mautic\CampaignBundle\Model\EventModel;


/**
 * Class PageSubscriber.
 */
class PageSubscriber extends CommonSubscriber
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
    public function __construct(
        EventModel $campaignEventModel,
        LeadModel $leadModel
    ) {
        $this->campaignEventModel = $campaignEventModel;
        $this->leadModel = $leadModel;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PageEvents::PAGE_ON_HIT => ['onPageHit', 0],
        ];
    }


    /**
     * Trigger actions for page hits.
     *
     * @param PageHitEvent $event
     */
    public function onPageHit(PageHitEvent $event)
    {

        $lead = $event->getLead();
        $leadId = $lead->getId();
        $hit = $event->getHit();
        $redirect = $hit->getRedirect();

        if ($hit->getSource() && $hit->getSourceId() && $redirect) {
            $channel = 'page.redirect';
            $channelId = $redirect->getId();
            $this->campaignEventModel->triggerEvent(
                'extendedconditions.click_condition',
                $hit,
                $channel,
                $channelId
            );
        }

        if (!$hit->getPage() && !$redirect) {
            $channel = 'url.hit';
            $channelId = $hit->getId();
            $this->campaignEventModel->triggerEvent(
                'extendedconditions.page_session',
                $hit,
                $channel,
                $channelId
            );
        }
    }
}
