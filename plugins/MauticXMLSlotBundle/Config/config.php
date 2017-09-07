<?php

/*
 * @copyright   Erasmus Student Network AISBL. 2017
 * @author      Gorka Guerrero Ruiz
 *
 * @link        http://esn.org
 *
 * @license     -
 */

return [
    'name'        => 'MauticXMLSlotBundle',
    'description' => 'Add extra slot items into the config builder',
    'author'      => 'kuzmany.biz',
    'version'     => '1.0.0',

    'services' => [
        'events' => [
            'plugin.xmlslot.pagebuilder.subscriber' => [
                'class' => 'MauticPlugin\MauticXMLSlotBundle\EventListener\SlotSubscriber',
            ],
            'plugin.xmlslot.emailbundle.subscriber' => [
                'class' => 'MauticPlugin\MauticXMLSlotBundle\EventListener\EmailSubscriber',
            ],
        ],
        'forms' => [
            'plugin.xmlslot.form.type.slot.xmlslotplugin' => [
                'class' => 'MauticPlugin\MauticXMLSlotBundle\Form\Type\XMLSlotPluginType',
                'alias' => 'xmlslot_plugin',
            ],
        ],
    ],
];
