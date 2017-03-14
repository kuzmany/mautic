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
class CampaignEventPageSessionConditionType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'page_session_time_limit',
            'number',
            [
                'label'      => 'plugin.extended.conditions.config.page_session.time.limit',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                    'postaddon_text' => 'min',
                ],
                'required'    => true,
                'precision'  => 0,
                'data'       => (isset($options['data']['page_session_time_limit'])) ? $options['data']['page_session_time_limit'] : 30,
            ]
        );
        $builder->add(
            'page_session_min_count',
            'number',
            [
                'label'      => 'plugin.extended.conditions.config.page_session.count.min',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                ],
                'required'    => false,
                'precision'  => 0,
                'data'       => (isset($options['data']['page_session_min_count'])) ? $options['data']['page_session_min_count'] : 0,
            ]
        );
        $builder->add(
            'page_session_max_count',
            'number',
            [
                'label'      => 'plugin.extended.conditions.config.page_session.count.max',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                ],
                'required'    => false,
                'precision'  => 0,
                'data'       => (isset($options['data']['page_session_max_count'])) ? $options['data']['page_session_max_count'] : 0,
            ]
        );

        $builder->add(
            'page_session_referrer',
            'text',
            [
                'label'      => 'plugin.extended.conditions.config.page_session.referrer',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                ],
                'required'    => false,
            ]
        );

        $builder->add(
            'page_session_history_referrer',
            'text',
            [
                'label'      => 'plugin.extended.conditions.config.page_session.history.referrer',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                ],
                'required'    => false,
            ]
        );

        $builder->add(
            'page_session_referrer_min_count',
            'number',
            [
                'label'      => 'plugin.extended.conditions.config.page_session.min.referrer.count',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                ],
                'required'    => false,
                'precision'  => 0,
                'data'       => (isset($options['data']['page_session_referrer_min_count'])) ? $options['data']['page_session_referrer_min_count'] : 0,
            ]
        );
      
        $builder->add(
            'page_session_referrer_max_count',
            'number',
            [
                'label'      => 'plugin.extended.conditions.config.page_session.max.referrer.count',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'   => 'form-control',
                ],
                'required'    => false,
                'precision'  => 0,
                'data'       => (isset($options['data']['page_session_referrer_max_count'])) ? $options['data']['page_session_referrer_max_count'] : 0,
            ]
        );

    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'extendedconditionsnevent_page_session';
    }
}
