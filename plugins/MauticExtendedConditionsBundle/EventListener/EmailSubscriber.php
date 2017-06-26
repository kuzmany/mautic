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


use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event as Events;
use Mautic\CoreBundle\Exception as MauticException;
use Joomla\Http\Http;

/**
 * Class EmailSubscriber.
 */
class EmailSubscriber extends CommonSubscriber
{
    /**
     * @var Http $connector ;
     */
    protected $connector;


    /**
     * EmailSubscriber constructor.
     *
     * @param Http $connector
     */
    public function __construct(Http $connector)
    {
        $this->connector = $connector;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            EmailEvents::EMAIL_ON_SEND => ['onEmailGenerate', 0],
            EmailEvents::EMAIL_ON_DISPLAY => ['onEmailGenerate', 0],
        ];
    }

    /**
     * Search and replace tokens with content
     *
     * @param EmailSendEvent $event
     */
    public function onEmailGenerate(Events\EmailSendEvent $event)
    {
        // Get content
        $content = $event->getContent();
        $content = $this->findFormTokens($content);
        // Set updated content
        $event->setContent($content);
    }


    private function findFormTokens($content)
    {
        $tokens = [];

        preg_match_all('/{getremoteurl=(.*?)}/', $content, $matches);
        if (count($matches[0])) {
            foreach ($matches[1] as $k => $id) {
                $token = $matches[0][$k];

                if (isset($tokens[$token])) {
                    continue;
                }
                try {
                    $data = $this->connector->get(
                        $id,
                        [],
                        10
                    );
                    $tokens[$token] = $data->body;
                } catch (\Exception $e) {
                    $tokens[$token] = '';
                }

            }
        }
        $content = str_replace(array_keys($tokens), $tokens, $content);

        return $content;
    }

}
