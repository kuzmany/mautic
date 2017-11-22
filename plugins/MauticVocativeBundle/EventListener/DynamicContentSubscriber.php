<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticVocativeBundle\EventListener;

use Mautic\CoreBundle\Event as MauticEvents;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\DynamicContentBundle\DynamicContentEvents;

/**
 * Class DynamicContentSubscriber.
 */
class DynamicContentSubscriber extends CommonSubscriber
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            DynamicContentEvents::TOKEN_REPLACEMENT => ['onTokenReplacement', -10],
        ];
    }

    /**
     * @param MauticEvents\TokenReplacementEvent $event
     */
    public function onTokenReplacement(MauticEvents\TokenReplacementEvent $event)
    {
        $content   = $event->getContent();
        $tokenList = $this->getConverter()->findAndReplace($content);
        if (count($tokenList)) {
            $content = str_replace(array_keys($tokenList), array_values($tokenList), $content);
        }
        $event->setContent($content);
    }

    /**
     * @return NameToVocativeConverter
     */
    private function getConverter()
    {
        return $this->factory->getKernel()->getContainer()->get('plugin.vocative.name_converter');
    }
}
