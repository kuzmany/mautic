<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\EventListener;

use Mautic\CampaignBundle\CampaignEvents;
use Mautic\CampaignBundle\Event\CampaignBuilderEvent;
use Mautic\CampaignBundle\Event\PendingEvent;
use Mautic\CoreBundle\Helper\ArrayHelper;
use Mautic\LeadBundle\Form\Type\CampaignActionAddDNCType;
use Mautic\LeadBundle\Form\Type\CampaignActionRemoveDNCType;
use Mautic\LeadBundle\LeadEvents;
use Mautic\LeadBundle\Model\DoNotContact;
use Mautic\LeadBundle\Model\LeadModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CampaignActionDNCSubscriber implements EventSubscriberInterface
{
    /**
     * @var DoNotContact
     */
    private $doNotContact;

    /**
     * @var LeadModel
     */
    private $leadModel;

    /**
     * CampaignActionDNCSubscriber constructor.
     */
    public function __construct(DoNotContact $doNotContact, LeadModel $leadModel)
    {
        $this->doNotContact = $doNotContact;
        $this->leadModel    = $leadModel;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD                  => ['configureAction', 0],
            LeadEvents::ON_CAMPAIGN_ACTION_ADD_DONOTCONTACT    => ['addDoNotContact', 0],
            LeadEvents::ON_CAMPAIGN_ACTION_REMOVE_DONOTCONTACT => ['removeDoNotContact', 0],
        ];
    }

    public function configureAction(CampaignBuilderEvent $event)
    {
        $event->addAction(
            'lead.adddnc',
            [
                'label'          => 'mautic.lead.lead.events.add_donotcontact',
                'description'    => 'mautic.lead.lead.events.add_donotcontact_desc',
                'batchEventName' => LeadEvents::ON_CAMPAIGN_ACTION_ADD_DONOTCONTACT,
                'formType'       => CampaignActionAddDNCType::class,
            ]
        );

        $event->addAction(
            'lead.removednc',
            [
                'label'          => 'mautic.lead.lead.events.remove_donotcontact',
                'description'    => 'mautic.lead.lead.events.remove_donotcontact_desc',
                'batchEventName' => LeadEvents::ON_CAMPAIGN_ACTION_REMOVE_DONOTCONTACT,
                'formType'       => CampaignActionRemoveDNCType::class,
            ]
        );
    }

    public function addDoNotContact(PendingEvent $event)
    {
        $config          = $event->getEvent()->getProperties();
        $channels        = ArrayHelper::getValue('channels', $config, []);
        $reason          = ArrayHelper::getValue('reason', $config, '');
        $dncPersists     = [];
        $contacts        = $event->getContactsKeyedById();
        foreach ($contacts as $contactId=>$contact) {
            foreach ($channels as $channel) {
                $dnc = $this->doNotContact->addDncForContact(
                    $contactId,
                    $channel,
                    \Mautic\LeadBundle\Entity\DoNotContact::MANUAL,
                    $reason,
                    false
                );
                if ($dnc) {
                    $dncPersists[] = $dnc;
                }
            }
        }
        $this->doNotContact->getDncRepo()->saveEntities($dncPersists);
        $this->leadModel->saveEntities($contacts);

        $event->passAll();
    }

    public function removeDoNotContact(PendingEvent $event)
    {
        $config          = $event->getEvent()->getProperties();
        $channels        = ArrayHelper::getValue('channels', $config, []);
        $contacts        = $event->getContactsKeyedById();
        foreach ($contacts as $contactId=>$contact) {
            foreach ($channels as $channel) {
                $this->doNotContact->removeDncForContact(
                    $contactId,
                    $channel,
                    false
                );
            }
        }
        $this->leadModel->saveEntities($contacts);

        $event->passAll();
    }
}
