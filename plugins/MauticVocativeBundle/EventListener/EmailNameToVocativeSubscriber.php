<?php
namespace MauticPlugin\MauticVocativeBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailSendEvent;
use MauticPlugin\MauticVocativeBundle\Service\NameToVocativeConverter;

class EmailNameToVocativeSubscriber extends CommonSubscriber
{

    /**
     * @return array
     */
    static public function getSubscribedEvents()
    {
        return [
            EmailEvents::EMAIL_ON_SEND => ['onEmailGenerate', 1 /* lowest priority */],
            EmailEvents::EMAIL_ON_DISPLAY => ['onEmailGenerate', 1 /* lowest priority */],
        ];
    }

    public function onEmailGenerate(EmailSendEvent $event)
    {
        // to array and to string solves "sometimes string, sometimes array" event return value
//        $bodyToVocalize = implode((array)$event->getContent(true /* with tokens replaced (to get names) */));
//        $vocalizedBody = $this->getConverter()->findAndReplace($bodyToVocalize);
//        $event->setContent($vocalizedBody);
//
//        $subjectToVocalize = $event->getSubject();
//        $vocalizedSubject = $this->getConverter()->findAndReplace($subjectToVocalize);
//        $event->setSubject($vocalizedSubject);

        // Combine all possible content to find tokens across them
        $content = $event->getSubject();
        $content .= $event->getContent(true);
        $content .= $event->getPlainText();

        $content = '
        [Karel|vocative]
        [Cassandra|vocative]
        [android|vocative]
        [monika|vocative(,For gentlemen only!)]
[richard|vocative(,For gentlemen only!)] 
[ |vocative(Karel,Monika)]
[ |vocative(Karel,Monika,Batman)]

';
        $tokenList = $this->getConverter()->findAndReplace($content);
        die(print_r($tokenList));
        if (count($tokenList)) {
            $event->addTokens($tokenList);
            unset($tokenList);
        }

    }

    /**
     * @return NameToVocativeConverter
     */
    private function getConverter()
    {
        return $this->factory->getKernel()->getContainer()->get('plugin.vocative.name_converter');
    }

}
