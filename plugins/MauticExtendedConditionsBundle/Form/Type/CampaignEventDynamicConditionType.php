<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendedConditionsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CampaignEventPageHitType.
 */
class CampaignEventDynamicConditionType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'url',
            'text',
            [
                'label'      => 'plugin.extended.conditions.dynamic.url',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                ],
            ]
        );
        $builder->add(
            'slot',
            'text',
            [
                'label'      => 'plugin.extended.conditions.dynamic.slot',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                ],
            ]
        );
        $builder->add(
            'dynamic_id',
            'text',
            [
                'label'      => 'plugin.extended.conditions.dynamic.dynamic_id',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                ],
            ]
        );

    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'extendedconditionsnevent_dynamic';
    }
}
