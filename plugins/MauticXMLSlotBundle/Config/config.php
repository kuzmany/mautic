<?php

/*
 * @copyright   Erasmus Student Network AISBL. 2017
 * @author      Gorka Guerrero Ruiz
 *
 * @link        http://esn.org
 *
 * @license     -
 */

return array(
  'name'        => 'MauticXMLSlotBundle',
  'description' => 'Add extra slot items into the config builder',
  'author'      => 'kuzmany.biz',
  'version'     => '1.0.0',

  'services'    => array(
    'events' => array(
      'plugin.xmlslot.pagebuilder.subscriber' => array(
        'class' => 'MauticPlugin\MauticXMLSlotBundle\EventListener\SlotSubscriber'
      )
    ),
    'forms' => array(
      'plugin.xmlslot.form.type.slot.xmlslotplugin' => [
        'class' => 'MauticPlugin\MauticXMLSlotBundle\Form\Type\XMLSlotPluginType',
        'alias' => 'xmlslot_plugin',
      ],  
    ),
  ),
);
