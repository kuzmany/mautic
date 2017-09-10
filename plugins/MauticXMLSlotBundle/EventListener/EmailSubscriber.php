<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticXMLSlotBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailSendEvent;

/**
 * Class EmailSubscriber.
 */
class EmailSubscriber extends CommonSubscriber
{
    /**
     * @var string
     */
    private static $contactFieldRegex = '{xmlslot=(.*?)}';

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            EmailEvents::EMAIL_ON_SEND    => ['onEmailGenerate', 0],
            EmailEvents::EMAIL_ON_DISPLAY => ['onEmailDisplay', 0],
        ];
    }

    /**
     * @param EmailSendEvent $event
     */
    public function onEmailDisplay(EmailSendEvent $event)
    {
        $this->onEmailGenerate($event);
    }

    /**
     * @param EmailSendEvent $event
     */
    public function onEmailGenerate(EmailSendEvent $event)
    {
        // Combine all possible content to find tokens across them
        $content = $event->getSubject();
        $content .= $event->getContent();
        $content .= $event->getPlainText();
        $lead = $event->getLead();

        $properties = [
            'xmlfile' => 'https://mautic-last.madesimple.cloud/test.xml',
        ];

        $xml = simplexml_load_file($properties['xmlfile']);
        die(print_r((string) $xml->SHOPITEM[0]->ITEM_ID));

//        preg_match_all('/<div class=\"(.*?)xmlslot(.*?)\">(.*?)<\/div>/s',$content,$vv,PREG_SET_ORDER);
//        die(print_r($vv));
//        exit();

        $tokenList = ['{xmlslot=slotname}' => 'test'];
        if (count($tokenList)) {
            $event->addTokens($tokenList);
            unset($tokenList);
        }
    }
}
