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

use Mautic\ConfigBundle\ConfigEvents;
use Mautic\ConfigBundle\Event\ConfigBuilderEvent;
use Mautic\ConfigBundle\Event\ConfigEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use MauticPlugin\MauticAddressValidatorBundle\Helper\AddressValidatorHelper;


/**
 * Class ConfigSubscriber.
 */
class ConfigSubscriber extends CommonSubscriber
{


    /**
     * @var AddressValidatorHelper $addressValidatorHelper;
     */
    protected $addressValidatorHelper;

    /**
     * FormSubscriber constructor.
     *
     * @param LeadModel $leadModel
     */
    public function __construct(AddressValidatorHelper $addressValidatorHelper)
    {
        $this->addressValidatorHelper = $addressValidatorHelper;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ConfigEvents::CONFIG_ON_GENERATE => ['onConfigGenerate', 0],
            ConfigEvents::CONFIG_PRE_SAVE    => ['onConfigSave', 0],
        ];
    }

    /**
     * @param ConfigBuilderEvent $event
     */
    public function onConfigGenerate(ConfigBuilderEvent $event)
    {
        $event->addForm([
            'bundle'     => 'MauticAddressValidatorBundle',
            'formAlias'  => 'addressvalidator_config',
            'formTheme'  => 'MauticAddressValidatorBundle:FormTheme\Config',
            'parameters' => $event->getParametersFromConfig('MauticAddressValidatorBundle'),
        ]);
    }

    /**
     * @param ConfigEvent $event
     */
    public function onConfigSave(ConfigEvent $event)
    {
        /** @var array $values */
        $values = $event->getConfig();

        // Manipulate the values
        if (!empty($values['validatorApiKey']) && !$this->addressValidatorHelper->validation(true)) {
            $event->setError('mautic.user.saml.metadata.invalid', []);
        }

        // Set updated values
        $event->setConfig($values);
    }
}
