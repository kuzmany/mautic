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
use Mautic\CoreBundle\Helper\IpLookupHelper;
use Doctrine\DBAL\Connection;


/**
 * Class PageSubscriber.
 */
class PageSubscriber extends CommonSubscriber
{

    /**
     * @var IpLookupHelper
     */
    protected $ipLookupHelper;

    /**
     * @var LeadModel
     */
    protected $leadModel;

    /**
     * @var EventModel
     */
    protected $campaignEventModel;

    /**
     * @var Connection
     */
    protected $db;


    /**
     * CampaignSubscriber constructor.
     *
     * @param EventModel $campaignEventModel
     */
    public function __construct(
        EventModel $campaignEventModel,
        LeadModel $leadModel,
        IpLookupHelper $ipLookupHelper,
        Connection $db
    ) {
        $this->campaignEventModel = $campaignEventModel;
        $this->leadModel = $leadModel;
        $this->ipLookupHelper = $ipLookupHelper;
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
        $hit = $event->getHit();
        $redirect = $hit->getRedirect();
        $request = $event->getRequest();

        // echo $request->get('page_url');
        //die();

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

            //change points unique by url
            if ($request->get('onclickpoints')) {
                $qb = $this->db->createQueryBuilder();
                $exist = $qb->select('id')
                    ->from(MAUTIC_TABLE_PREFIX.'lead_points_change_log', 'lp')
                    ->where(
                        $qb->expr()->andX(
                            $qb->expr()->eq('lp.event_name', ':eventName'),
                            $qb->expr()->eq('lp.lead_id', ':leadId')
                        )
                    )
                    ->setParameter('eventName', $request->get('page_url'))
                    ->setParameter('leadId', $leadId)
                    ->execute()
                    ->fetchColumn();
                if (!$exist) {
                    $lead->adjustPoints((int)$request->get('onclickpoints'));
                    $lead->addPointsChangeLogEntry(
                        'url',
                        $request->get('page_url'),
                        'hit',
                        $request->get('onclickpoints'),
                        $this->ipLookupHelper->getIpAddress()
                    );
                   $this->leadModel->saveEntity($lead);
                }
            }

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
