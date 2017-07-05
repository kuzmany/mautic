<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticCustomEsetBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\BuildJsEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Templating\Helper\AssetsHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class BuildJsSubscriber.
 */
class BuildJsSubscriber extends CommonSubscriber
{
    /**
     * @var AssetsHelper
     */
    protected $assetsHelper;

    /**
     * BuildJsSubscriber constructor.
     *
     * @param AssetsHelper $assetsHelper
     */
    public function __construct(AssetsHelper $assetsHelper)
    {
        $this->assetsHelper = $assetsHelper;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::BUILD_MAUTIC_JS => [
                ['onBuildJsTop', 2000],
            ],
        ];
    }

    /**
     * @param BuildJsEvent $event
     */
    public function onBuildJsTop(BuildJsEvent $event)
    {
        //basic js
        $js = <<<JS
function getQuery(q) {
   return (window.location.search.match(new RegExp('[?&]' + q + '=([^&]+)')) || [, null])[1];
}
var utmCampaign =   getQuery('utm_campaign');
var utmSource =   getQuery('utm_source');
var utmMedium =   getQuery('utm_medium');

if(utmMedium == 'email' && utmSource != null){
      if (typeof parms.resellerid === 'undefined') {
            var  parms = {'resellerid': utmSource};
       }else{
            parms.push('resellerid', utmSource);
        }    
}

/*
if(utmCampaign != null){
  if (typeof parms.tags === 'undefined') {
            var  parms = {tags: ['utm_campaign:'+utmCampaign]};
       }else{
            parms.tags.push('utm_campaign:'+utmCampaign);
        }    
}


if(utmCampaign != null){
  if (typeof parms.tags === 'undefined') {
            var  parms = {tags: ['utm_source:'+utmCampaign]};
       }else{
            parms.tags.push('utm_source:'+utmCampaign);
        }    
}*/

JS;
        $event->appendJs($js, 'CustomEset');
    }


}
