<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendedConditionsBundle\Helper;

use Mautic\CampaignBundle\Model\EventModel;
use Mautic\CoreBundle\Event\TokenReplacementEvent;
use Mautic\DynamicContentBundle\DynamicContentEvents;
use Mautic\DynamicContentBundle\Entity\DynamicContent;
use Mautic\DynamicContentBundle\Model\DynamicContentModel;
use Mautic\LeadBundle\Entity\Lead;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Session;


class DynamicHelper
{
    /**
     * @var EventModel
     */
    protected $campaignEventModel;

    /**
     * @var ContainerAwareEventDispatcher
     */
    protected $dispatcher;

    /**
     * @var DynamicContentModel
     */
    protected $dynamicContentModel;

    /**
     * @var $session
     **/
    protected $session;

    /**
     * DynamicContentHelper constructor.
     *
     * @param DynamicContentModel $dynamicContentModel
     * @param EventModel $campaignEventModel
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        DynamicContentModel $dynamicContentModel,
        EventModel $campaignEventModel,
        EventDispatcherInterface $dispatcher,
        Session $session
    ) {
        $this->dynamicContentModel = $dynamicContentModel;
        $this->campaignEventModel = $campaignEventModel;
        $this->dispatcher = $dispatcher;
        $this->session = $session;
    }

    /**
     * @param            $slot
     * @param Lead|array $lead
     *
     * @return string
     */
    public function getDynamicForLead($slot, $lead)
    {
        $this->session->remove('dynamic.id.'.$slot.$lead->getId());

        $this->campaignEventModel->triggerEvent(
            'extendedconditions.dynamic_condition',
            ['slot' => $slot],
            'extendedconditions.dynamic_condition.'.$slot
        );

        $dynamicId = $this->session->get('dynamic.id.'.$slot.$lead->getId());

        $content = '';
        if (!empty($dynamicId)) {
            $dwc = $this->dynamicContentModel->getEntity($dynamicId);
            if ($dwc instanceof DynamicContent) {
                $content = $dwc->getContent();
                // Determine a translation based on contact's preferred locale
                /** @var DynamicContent $translation */
                list($ignore, $translation) = $this->dynamicContentModel->getTranslatedEntity($dwc, $lead);
                if ($translation !== $dwc) {
                    // Use translated version of content
                    $dwc = $translation;
                    $content = $dwc->getContent();
                }
                $this->dynamicContentModel->createStatEntry($dwc, $lead, $slot);

                $tokenEvent = new TokenReplacementEvent(
                    $content,
                    $lead,
                    ['slot' => $slot, 'dynamic_content_id' => $dwc->getId()]
                );
                $this->dispatcher->dispatch(DynamicContentEvents::TOKEN_REPLACEMENT, $tokenEvent);
                $content = $tokenEvent->getContent();
            }
        }

        return $content;
    }


}
