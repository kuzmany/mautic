<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendedConditionsBundle\EventListener;

use Mautic\ConfigBundle\ConfigEvents;
use Mautic\ConfigBundle\Event\ConfigBuilderEvent;
use Mautic\ConfigBundle\Event\ConfigEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Helper\CoreParametersHelper;

/**
 * Class ConfigSubscriber.
 */
class ConfigSubscriber extends CommonSubscriber
{
    /**
     * @var CoreParametersHelper
     */
    protected $coreParametersHelper;

    /**
     * ConfigSubscriber constructor.
     *
     * @param CoreParametersHelper $coreParametersHelper
     */
    public function __construct(CoreParametersHelper $coreParametersHelper)
    {
        $this->coreParametersHelper = $coreParametersHelper;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            ConfigEvents::CONFIG_ON_GENERATE => ['onConfigGenerate', 0],
        ];
    }

    /**
     * @param ConfigBuilderEvent $event
     */
    public function onConfigGenerate(ConfigBuilderEvent $event)
    {
        $event->addForm([
            'bundle'     => 'MauticExtendedConditionsBundle',
            'formAlias'  => 'extendedconditionsnevent_config',
            'formTheme'  => 'MauticExtendedConditionsBundle:FormTheme\Config',
            'parameters' => $event->getParametersFromConfig('MauticExtendedConditionsBundle'),
        ]);
    }
}
