<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\SmsBundle\EventListener;

use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use Mautic\CampaignBundle\Event\DecisionEvent;
use Mautic\CampaignBundle\Model\EventModel;
use Mautic\SmsBundle\Event\DeliveryEvent;
use Mautic\SmsBundle\Sms\TransportChain;
use Mautic\SmsBundle\Sms\TransportSettingsInterface;
use Mautic\SmsBundle\SmsEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CampaignReplySubscriber.
 */
class CampaignDeliverySubscriber implements EventSubscriberInterface
{
    const TYPE_DELIVERED = 'sms.delivery';
    const TYPE_READ      = 'sms.read';
    const TYPE_FAILED    = 'sms.failed';

    /**
     * @var TransportChain
     */
    private $transportChain;

    /**
     * @var EventModel
     */
    private $realTimeExecutioner;

    /**
     * CampaignReplySubscriber constructor.
     *
     * @param TransportChain $transportChain
     * @param EventModel     $realTimeExecutioner
     */
    public function __construct(TransportChain $transportChain, EventModel $realTimeExecutioner)
    {
        $this->transportChain      = $transportChain;
        $this->realTimeExecutioner = $realTimeExecutioner;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD => ['onCampaignBuild', 0],
            SmsEvents::ON_CAMPAIGN_DELIVERY   => ['onCampaignDelivery', 0],
            SmsEvents::ON_DELIVERY            => ['onDelivery', 0],
        ];
    }

    /**
     * @param CampaignBuilderEvent $event
     *
     * @throws \Mautic\SmsBundle\Exception\PrimaryTransportNotEnabledException
     */
    public function onCampaignBuild(CampaignBuilderEvent $event)
    {
        if (count($this->transportChain->getEnabledTransports()) === 0) {
            return;
        }

        if ($this->transportChain->getSettings()->hasSetting(TransportSettingsInterface::STAT_DELIVERED)) {
            $event->addDecision(
                self::TYPE_DELIVERED,
                [
                    'label'                  => 'mautic.campaign.sms.delivered',
                    'description'            => 'mautic.campaign.sms.delivered.tooltip',
                    'eventName'              => SmsEvents::ON_CAMPAIGN_DELIVERY,
                    'connectionRestrictions' => [
                        'source' => [
                            'action' => [
                                'sms.send_text_sms',
                            ],
                        ],
                    ],
                ]
            );
        }

        if ($this->transportChain->getSettings()->hasSetting(TransportSettingsInterface::STAT_READ)) {
            $event->addDecision(
                self::TYPE_READ,
                [
                    'label'                  => 'mautic.campaign.sms.read',
                    'description'            => 'mautic.campaign.sms.read.tooltip',
                    'eventName'              => SmsEvents::ON_CAMPAIGN_DELIVERY,
                    'connectionRestrictions' => [
                        'source' => [
                            'action' => [
                                'sms.send_text_sms',
                            ],
                        ],
                    ],
                ]
            );
        }

        if ($this->transportChain->getSettings()->hasSetting(TransportSettingsInterface::STAT_FAILED)) {
            $event->addDecision(
                self::TYPE_FAILED,
                [
                    'label'                  => 'mautic.campaign.sms.failed',
                    'description'            => 'mautic.campaign.sms.failed.tooltip',
                    'eventName'              => SmsEvents::ON_CAMPAIGN_DELIVERY,
                    'connectionRestrictions' => [
                        'source' => [
                            'action' => [
                                'sms.send_text_sms',
                            ],
                        ],
                    ],
                ]
            );
        }
    }

    /**
     * @param DecisionEvent $decisionEvent
     */
    public function onCampaignDelivery(DecisionEvent $decisionEvent)
    {
        /** @var DeliveryEvent $deliveryEvent */
        $deliveryEvent = $decisionEvent->getPassthrough();

        if (!$deliveryEvent instanceof DeliveryEvent) {
            return;
        }

        if ($decisionEvent->checkContext(self::TYPE_DELIVERED) || $decisionEvent->checkContext(self::TYPE_READ) || $decisionEvent->checkContext(self::TYPE_FAILED)) {
            $decisionEvent->setChannel('sms');
            $decisionEvent->setAsApplicable();
        }
    }

    /**
     * @param DeliveryEvent $event
     *
     * @throws \Mautic\CampaignBundle\Executioner\Dispatcher\Exception\LogNotProcessedException
     * @throws \Mautic\CampaignBundle\Executioner\Dispatcher\Exception\LogPassedAndFailedException
     * @throws \Mautic\CampaignBundle\Executioner\Exception\CannotProcessEventException
     * @throws \Mautic\CampaignBundle\Executioner\Scheduler\Exception\NotSchedulableException
     */
    public function onDelivery(DeliveryEvent $event)
    {
        $deliveryStatusDAO = $event->getDeliveryStatusDAO();
        if ($deliveryStatusDAO->isDelivered()) {
            $type = self::TYPE_DELIVERED;
        } elseif ($deliveryStatusDAO->isRead()) {
            $type = self::TYPE_READ;
        } elseif ($deliveryStatusDAO->isFailed()) {
            $type = self::TYPE_FAILED;
        } else {
            return;
        }

        $this->realTimeExecutioner->triggerEvent($type, $event, 'sms');
    }
}
