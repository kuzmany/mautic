<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendedConditionsBundle\EventListener;

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
                ['onBuildJsTop', 1500],
                ['onBuildJs', 0],
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
        if (typeof window[window.MadeSimpleShop] !== 'undefined') {
            window.MauticTrackingObject = window.MadeSimpleShop;
       }
       if (typeof window.MauticTrackingObject === 'undefined') {
       var w=window;var n='mt';w['MauticTrackingObject']=n;w[n]=w[n]||function(){(w[n].q=w[n].q||[]).push(arguments)};mt('send', 'pageview');
       }
       document.body.innerHTML += '<div class="dynamic-slot" data-slot-name="madesimpleshop-carts"></div>';
JS;
      //  $event->appendJs($js, 'Extended');
    }


    /**
     * @param BuildJsEvent $event
     */
    public function onBuildJs(BuildJsEvent $event)
    {

        $dwcUrl = $this->router->generate('mautic_api_dynamic_action', ['objectAlias' => 'slotNamePlaceholder'], UrlGeneratorInterface::ABSOLUTE_URL);

        $js = <<<JS
           // call variable if doesnt exist
            if (typeof MauticDomain == 'undefined') {
                var MauticDomain = '{$this->request->getSchemeAndHttpHost()}';
            }            
            if (typeof MauticLang == 'undefined') {
                var MauticLang = {
                     'submittingMessage': "{$this->translator->trans('mautic.form.submission.pleasewait')}"
        };
            }
MauticJS.replaceDynamicContent = function () {
    var dynamicContentSlots = document.querySelectorAll('.dynamic-slot');
    if (dynamicContentSlots.length) {
        MauticJS.iterateCollection(dynamicContentSlots)(function(node, i) {
            var slotName = node.dataset.slotName;
            var url = '{$dwcUrl}'.replace('slotNamePlaceholder', slotName);
            MauticJS.makeCORSRequest('GET', url, {}, function(response, xhr) {
                if (response.length) {
                    node.innerHTML = response;
                    // form load library
                    if (response.search("mauticform_wrapper") > 0) {
                        // if doesn't exist
                        if (typeof MauticSDK == 'undefined') {
                            MauticJS.insertScript('{$this->assetsHelper->getUrl('media/js/mautic-form.js', null, null, true)}');
                            
                            // check initialize form library
                            var fileInterval = setInterval(function() {
                                if (typeof MauticSDK != 'undefined') {
                                    MauticSDK.onLoad(); 
                                    clearInterval(fileInterval); // clear interval
                                 }
                             }, 100); // check every 100ms
                        } else {
                            MauticSDK.onLoad();
                         }
                    }

                    var m;
                    var regEx = /<script[^>]+src="?([^"\s]+)"?\s/g;                    
                    
                    while (m = regEx.exec(response)) {
                        if ((m[1]).search("/focus/") > 0) {
                            MauticJS.insertScript(m[1]);
                        }
                    }
                }
            });
        });
    }
};

MauticJS.onFirstEventDelivery(MauticJS.replaceDynamicContent);
JS;

        $event->appendJs($js, 'Extended');
    }


}
