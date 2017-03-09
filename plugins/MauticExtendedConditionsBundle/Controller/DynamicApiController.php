<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticExtendedConditionsBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Mautic\DynamicContentBundle\Entity\DynamicContent;
use Mautic\DynamicContentBundle\Model\DynamicContentModel;
use Mautic\LeadBundle\Model\LeadModel;

/**
 * Class DynamicContentApiController.
 */
class DynamicApiController extends CommonController
{

    /**
     * @var $dynamicContentModel
     */
    protected $dynamicContentModel;

    /**
     * @var LeadModel
     */
    protected $leadModel;


    /**
     * @param $objectAlias
     *
     * @return mixed
     */
    public function processAction($objectAlias)
    {
        $lead = $this->getModel('lead')->getCurrentLead();
        $content = $this->get('mautic.plugin.helper.dynamic')->getDynamicForLead($objectAlias, $lead);
        return !empty($content) ? new Response($content) : new Response($objectAlias);
    }

}
