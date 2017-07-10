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

use Mautic\CampaignBundle\Model\CampaignModel;
use Mautic\CoreBundle\Factory\MauticFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CampaignListType.
 */
class AllCampaignListType extends AbstractType
{
    /**
     * @var string
     */
    protected $thisString;

    /**
     * @var CampaignModel
     */
    private $model;

    /**
     * @param MauticFactory $factory
     */
    public function __construct(MauticFactory $factory)
    {
        $this->model      = $factory->getModel('campaign');
        $this->thisString = $factory->getTranslator()->trans('mautic.campaign.form.thiscampaign');
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $model = $this->model;
        $msg   = $this->thisString;
        $resolver->setDefaults([
            'choices' => function (Options $options) use ($model, $msg) {
                $choices = [];
                $campaigns = $model->getRepository()->getEntities();
                foreach ($campaigns as $campaign) {
                    $choices[$campaign->getId()] = $campaign->getName();
                }

                //sort by language
                asort($choices);

                if ($options['include_this']) {
                    $choices = ['this' => $msg] + $choices;
                }

                return $choices;
            },
            'empty_value'  => false,
            'expanded'     => false,
            'multiple'     => true,
            'required'     => false,
            'include_this' => false,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'all_campaign_list';
    }

    public function getParent()
    {
        return 'choice';
    }
}
