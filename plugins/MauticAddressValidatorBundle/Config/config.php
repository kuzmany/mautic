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
                ],
            ],
//            'mautic.plugin.addressvalidator.configbundle.subscriber' => [
//                'class' => 'MauticPlugin\MauticAddressValidatorBundle\EventListener\ConfigSubscriber',
//                'arguments' => [
//                    'mautic.helper.core_parameters',
//                ],
//            ],
        ],
        'forms' => [
            'mautic.plugin.addressvalidator.type.addressvalidator.field' => [
                'class' => 'MauticPlugin\MauticAddressValidatorBundle\Form\Type\FormFieldAddressValidatordType',
                'alias' => 'addressvalidator',
                'arguments' => [
                    'mautic.lead.model.field',
                ],
            ],
        ],
    ],
];