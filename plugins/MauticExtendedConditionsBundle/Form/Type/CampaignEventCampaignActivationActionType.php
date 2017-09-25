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
 * Class CampaignEventCampaignActivationActionType.
 */
class CampaignEventCampaignActivationActionType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add(
            'enable',
            'all_campaign_list', [
            'label'      => 'plugin.extended.conditions.camapign.enable',
            'attr'       => [
                'class'   => 'form-control',
            ],
            'multiple'   => true,
            'required' => false,
        ]);

        $builder->add(
            'disable',
            'all_campaign_list', [
            'label'      => 'plugin.extended.conditions.camapign.disable',
            'attr'       => [
                'class'   => 'form-control',
            ],
            'multiple'   => true,
            'required' => false,
        ]);



    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'extendedconditionsnevent_campaign_activation';
    }
}
