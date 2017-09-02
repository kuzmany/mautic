<?php

/*
 * @copyright   Erasmus Student Network AISBL. 2017
 * @author      Gorka Guerrero Ruiz
 *
 * @link        http://esn.org
 *
 * @license     -
 */

namespace MauticPlugin\MauticXMLSlotBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Mautic\CoreBundle\Form\Type\SlotType;

/**
 * Class SlotESNPluginType.
 */
class XMLSlotPluginType extends SlotType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add(
            'xmlfile',
            'text',
            [
                'label' => 'plugin.xmlslotplugin.builder.xml.file',
                'label_attr' => ['class' => 'control-label'],
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-slot-param' => 'xmlfile',
                ],
            ]
        );

        $builder->add(
            'tablecols',
            'text',
            [
                'label' => 'plugin.xmlslotplugin.builder.table.cols',
                'label_attr' => ['class' => 'control-label'],
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-slot-param' => 'tablecols',
                ],
            ]
        );

        $builder->add(
            'tablerows',
            'text',
            [
                'label' => 'plugin.xmlslotplugin.builder.table.rows',
                'label_attr' => ['class' => 'control-label'],
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-slot-param' => 'tablerows',
                ],
            ]
        );

        $builder->add(
            'customcss',
            'text',
            [
                'label' => 'plugin.xmlslotplugin.builder.custom.css',
                'label_attr' => ['class' => 'control-label'],
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-slot-param' => 'customcss',
                ],
            ]
        );



        $builder->add(
            'hideimages',
            'yesno_button_group',
            [
                'label' => 'plugin.xmlslotplugin.builder.images.hide',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'class' => 'form-control',
                    'data-slot-param' => 'hideimages',
                ],
                'data' => true,
                'required' => false,
            ]
        );

        $builder->add(
            'hidebuttons',
            'yesno_button_group',
            [
                'label' => 'plugin.xmlslotplugin.builder.buttons.hide',
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'class' => 'form-control',
                    'data-slot-param' => 'hidebuttons',
                ],
                'data' => true,
                'required' => false,
            ]
        );

        $builder->add(
            'buttonstext',
            'text',
            [
                'label' => 'plugin.xmlslotplugin.builder.buttons.text',
                'label_attr' => ['class' => 'control-label'],
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'data-slot-param' => 'link-text',
                    'data-show-on' => '{"xmlslot_plugin_hidebuttons_1":"checked"}',
                ],
            ]
        );




        parent::buildForm($builder, $options);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'xmlslot_plugin';
    }
}
