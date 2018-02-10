<?php
namespace MauticPlugin\MauticCustomEsetBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CampaignBundle\Model\EventModel;
use Mautic\LeadBundle\Entity\LeadField;
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
use MauticPlugin\MauticCustomEsetBundle\CustomEsetEvents;
use MauticPlugin\MauticExtendedConditionsBundle\ExtendedConditionsEvents;
use MauticPlugin\MauticSocialBundle\Entity\Lead;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\DBAL\Connection;
use Mautic\CoreBundle\Helper\CookieHelper;

class CampaignSubscriber extends CommonSubscriber
{
    /*
     * @var LeadModel
     */
    protected $leadModel;


    /**
     * @var Connection
     */
    protected $db;
    /**
     * CampaignSubscriber constructor.
     *
     * @param LeadModel $leadModel
     */
    public function __construct(
        LeadModel $leadModel,
        Connection $db
    ) {
        $this->leadModel = $leadModel;
        $this->db = $db;
    }


    static public function getSubscribedEvents()
    {
        return [
            CampaignEvents::CAMPAIGN_ON_BUILD => ['onCampaignBuild', 0],
            CustomEsetEvents::ON_CAMPAIGN_TRIGGER_ACTION => ['onCampaignTriggerAction', 0],
        ];
    }


    /**
     * Add event triggers and actions.
     *
     * @param CampaignBuilderEvent $event
     */
    public function onCampaignBuild(CampaignBuilderEvent $event)
    {

        $action = [
            'label' => 'plugin.custom.eset.update.page.session',
            'eventName' => CustomEsetEvents::ON_CAMPAIGN_TRIGGER_ACTION,
            'formType' => 'extendedconditionsnevent_dynamic_stop',
            'formType' => 'update_page_session',
        ];
        $event->addAction('custom.eset.update.page.session', $action);

    }

    /**
     * @param CampaignExecutionEvent $event
     */
    public function onCampaignTriggerAction(
        CampaignExecutionEvent $event
    ) {
        $lead = $event->getLead();
        $eventConfig = $event->getConfig();
        if ($event->checkContext('custom.eset.update.page.session')) {
            $leadField = $eventConfig['leadField'];
            if (!empty($leadField)) {
                $table = 'page_hits';
                $select = 'COUNT(h1.id)';
                $alias = 'h1';
                $alias2 = 'h2';

                $q = $this->db
                    ->createQueryBuilder();
                $subqb = $q
                    ->select($select)
                    ->from(MAUTIC_TABLE_PREFIX.$table, $alias);

                $subqb2 = $this->db
                    ->createQueryBuilder()
                    ->select($alias2.'.id')
                    ->from(MAUTIC_TABLE_PREFIX.$table, $alias2);

                $subqb2->where($q->expr()
                    ->andX(
                        $q->expr()->eq($alias2.'.lead_id', ':leadId'),
                        $q->expr()->gt($alias2.'.date_hit', '('.$alias.'.date_hit - INTERVAL 30 MINUTE)'),
                        $q->expr()->lt($alias2.'.date_hit', $alias.'.date_hit')
                    ));

                $subqb->where($q->expr()
                    ->andX($q->expr()
                        ->eq($alias.'.lead_id', ':leadId'),$q->expr()
                        ->isNull($alias.'.email_id'),$q->expr()
                        ->isNull($alias.'.redirect_id'),
                        sprintf('%s (%s)', 'NOT EXISTS', $subqb2->getSQL())))
                    ->setParameter('leadId', 	$lead->getId());
                $sessionCountforLead = $subqb->execute()->fetchColumn();
                $update[$leadField] = $sessionCountforLead;
                $this->leadModel->setFieldValues($lead, $update);
                $this->leadModel->saveEntity($lead);
                return $event->setResult(true);

            }
        }
       return $event->setResult(false);
    }
}
