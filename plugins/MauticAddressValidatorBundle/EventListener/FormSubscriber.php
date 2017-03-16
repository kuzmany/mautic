<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressValidatorBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\FormEvents;
use Mautic\FormBundle\Event as Events;
use MauticPlugin\MauticAddressValidatorBundle\MauticAddressValidatorEvents;



/**
 * Class FormSubscriber.
 */
class FormSubscriber extends CommonSubscriber
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::FORM_ON_BUILD => ['onFormBuilder', 0],
        ];
    }

    /**
     * Add a lead generation action to available form submit actions.
     *
     * @param FormBuilderEvent $event
     */
    public function onFormBuilder(FormBuilderEvent $event)
    {
        $action = [
            'label'          => 'mautic.plugin.field.addressvalidator',
            'formType'       => 'addressvalidator',
            'template'         => 'MauticAddressValidatorBundle:SubscribedEvents\Field:addressvalidator.html.php',
            'builderOptions' => [
                'addLeadFieldList' => false,
                'addIsRequired'    => false,
                'addDefaultValue'  => false,
                'addSaveResult'    => false,
                'addShowLabel'     => false,
                'addHelpMessage'   => false,
                'addLabelAttributes'    => false,
                'addInputAttributes'    => false,
                'addBehaviorFields'    => false,
                'addContainerAttributes'=> false,
                'allowCustomAlias'=> false,
                'labelText'=> false,
            ],
        ];

        $event->addValidator(
            'plugin.addressvalidator.validate',
            [
                'eventName' => MauticAddressValidatorEvents::ON_FORM_VALIDATE_ACTION,
            ]
        );

        $event->addFormField('plugin.addressvalidator', $action);
    }
}
