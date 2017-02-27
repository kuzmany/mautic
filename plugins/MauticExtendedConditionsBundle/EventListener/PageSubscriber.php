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
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\PageBundle\Event as Events;
use Mautic\PageBundle\PageEvents;
use Mautic\LeadBundle\LeadEvent;
use Mautic\PageBundle\Event\PageHitEvent;
use Mautic\PageBundle\Event\PageEvent;
use Mautic\CampaignBundle\Model\EventModel;
use Doctrine\DBAL\Connection;

/**
 * Class PageSubscriber.
 */
class PageSubscriber extends CommonSubscriber
{
    /**
     * @var $cookieHelper
     */
    protected $db;

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
    public function __construct(EventModel $campaignEventModel, LeadModel $leadModel, Connection $db)
    {
        $this->campaignEventModel = $campaignEventModel;
        $this->leadModel = $leadModel;
        $this->db = $db;
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

        $qb = $this->db->createQueryBuilder();

        $latestDateHit = $qb->select('date_hit')
            ->from(MAUTIC_TABLE_PREFIX.'page_hits', 'ph')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('ph.lead_id', ':leadId')
                )
            )
            ->setParameter('leadId', $leadId)
            ->orderBy('ph.id', 'DESC')
            ->setFirstResult(1)
            ->setMaxResults(1)
            ->execute()
            ->fetchColumn();

        $hit = $event->getHit();
        $channel = 'page';
        $channelId = null;

        if ($redirect = $hit->getRedirect()) {
            $channel = 'page.redirect';
            $channelId = $redirect->getId();
        } elseif ($page = $hit->getPage()) {
            $channelId = $page->getId();
        }

        $this->campaignEventModel->triggerEvent('extendedconditions.last_active_condition', $hit, $channel, $channelId);
    }
}
