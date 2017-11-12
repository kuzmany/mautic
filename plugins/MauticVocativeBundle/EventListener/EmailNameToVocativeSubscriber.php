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
            EmailEvents::EMAIL_ON_SEND => ['onEmailGenerate', -999 /* lowest priority */],
            EmailEvents::EMAIL_ON_DISPLAY => ['onEmailGenerate', -999  /* lowest priority */],
        ];
    }

    public function onEmailGenerate(EmailSendEvent $event)
    {
        // Combine all possible content to find tokens across them
        $content = $event->getSubject();
        $content .= $event->getContent(true);
        $content .= $event->getPlainText();
        $tokenList = $this->getConverter()->findAndReplace($content);
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
