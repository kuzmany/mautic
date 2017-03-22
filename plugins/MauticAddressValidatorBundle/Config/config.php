<?php
return [
    'name' => 'Address Validator',
    'description' => '',
    'author' => 'MadeSimple.shop',
    'version' => '1.0.0',
    'services' => [
        'events' => [
            'mautic.plugin.addressvalidator.formbundle.subscriber' => [
                'class'     => 'MauticPlugin\MauticAddressValidatorBundle\EventListener\FormSubscriber',
                'arguments' => [
                    'mautic.lead.model.lead',
                    'mautic.helper.core_parameters',
                ],
            ],
            'mautic.plugin.addressvalidator.configbundle.subscriber' => [
                'class' => 'MauticPlugin\MauticAddressValidatorBundle\EventListener\ConfigSubscriber'
            ],
        ],
        'forms' => [
            'mautic.plugin.addressvalidator.type.addressvalidator.field' => [
                'class' => 'MauticPlugin\MauticAddressValidatorBundle\Form\Type\FormFieldAddressValidatordType',
                'alias' => 'addressvalidator',
                'arguments' => [
                    'mautic.lead.model.field',
                ],
            ],
            'mautic.plugin.addressvalidator.type.config' => [
                'class' => 'MauticPlugin\MauticAddressValidatorBundle\Form\Type\ConfigType',
                'alias' => 'addressvalidator_config',
            ],
        ],
    ],
    'parameters' => [
        'apiKey'                       => '',
    ],
];