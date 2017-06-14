<?php

/*
 * @copyright   2015 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticCustomEsetBundle\EventListener;

use Guzzle\Plugin\Cookie\Cookie;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Model\LeadModel;
use Mautic\PageBundle\Event as Events;
use Mautic\PageBundle\PageEvents;
use Mautic\LeadBundle\LeadEvent;
use Mautic\PageBundle\Event\PageHitEvent;
use Mautic\PageBundle\Event\PageEvent;
use Mautic\CampaignBundle\Model\EventModel;
use Mautic\UserBundle\Security\Provider\UserProvider;


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
     * @var UserProvider
     */
    protected $userProvider;

    /**
     * CampaignSubscriber constructor.
     *
     * @param EventModel $campaignEventModel
     */
    public function __construct(
        EventModel $campaignEventModel,
        LeadModel $leadModel,
        UserProvider $userProvider
    ) {
        $this->campaignEventModel = $campaignEventModel;
        $this->leadModel = $leadModel;
        $this->userProvider = $userProvider;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            PageEvents::PAGE_ON_HIT => ['onPageHit', 1500],
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
        $hit = $event->getHit();
        $redirect = $hit->getRedirect();
        $request = $event->getRequest();

        if (!$hit->getPage() && !$redirect) {
            //change points unique by url
            if ($request->get('owner')) {
                $currentOwner = $lead->getOwner();
                if (!$currentOwner || ($currentOwner && $currentOwner->getUsername() != $request->get('owner'))) {
                    $newOwner = $this->userProvider->loadUserByUsername($request->get('owner'));
                    if ($newOwner) {
                        $lead->setOwner($newOwner);
                    }
                }
            }
            // change points
            if ($request->get('points')) {
                $lead->adjustPoints((int)$request->get('points'));
            }    // change points

            if ($request->get('owner')) {
                if ($request->get('owner') == 'mt2') {
                    $lead->addUpdatedField('preferred_locale', 'de');

                } elseif ($request->get('owner') == 'mt1') {
                    $lead->addUpdatedField('preferred_locale', 'sk');
                } else {
                    $lead->addUpdatedField('preferred_locale', $request->get('owner'));
                }
            }

            if ($request->get('utm_campaign')) {
                $lead->addTag('campaign:'.$request->get('utm_campaign'));
            }

            if ($lead->getChanges()) {
                $this->leadModel->saveEntity($lead);
            }
        }
    }
}
