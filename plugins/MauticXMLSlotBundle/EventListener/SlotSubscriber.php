<?php

/*
 * @copyright   Erasmus Student Network AISBL. 2017
 * @author      Gorka Guerrero Ruiz
 *
 * @link        http://esn.org
 *
 * @license     -
 */

namespace MauticPlugin\MauticXMLSlotBundle\EventListener;

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Form\Type\SlotTextType;
use Mautic\CoreBundle\CoreEvents;
use Mautic\PageBundle\Event\PageBuilderEvent;
use Mautic\PageBundle\Helper\TokenHelper;
use Mautic\PageBundle\Model\PageModel;
use Mautic\PageBundle\PageEvents;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailBuilderEvent;
use Mautic\EmailBundle\Event\EmailSendEvent;
use Mautic\EmailBundle\Model\EmailModel;
use Mautic\CoreBundle\Event\CustomAssetsEvent;
use Mautic\CoreBundle\Templating\Helper\AssetsHelper;
/**
 * Class SlotSubscriber
 */
class SlotSubscriber extends CommonSubscriber{

  /**
  * {@inheritdoc}
  */
  public static function getSubscribedEvents(){
    return [
      PageEvents::PAGE_ON_BUILD   => ['onPageBuild', 0],
      EmailEvents::EMAIL_ON_BUILD => ['onEmailBuild', 0],
    ];
  }

  /**
   * Add new slots in builder.
   *
   * @param Events\PageBuilderEvent $event
   */
  public function onPageBuild(PageBuilderEvent $event){

    if ($event->slotTypesRequested()) {
      $event->addSlotType(
        'xmlslot',
        'XML Slot',
        'cog',
        'MauticXMLSlotBundle:Slots:xmlslotplugin.html.php',
        'xmlslot_plugin',
        400
      );
    }
  }

 /**
   * @param EmailBuilderEvent $event
   */
  public function onEmailBuild(EmailBuilderEvent $event){

    if ($event->slotTypesRequested()) {
      $event->addSlotType(
        'xmlslot',
        'XML Slot',
        'cog',
        'MauticXMLSlotBundle:Slots:xmlslotplugin.html.php',
        'xmlslot_plugin',
        400
      );
    }
  }
}  