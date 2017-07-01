<?php

/*
 * @copyright   2016 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticAddressValidatorBundle\Helper;

use Mautic\CoreBundle\Exception as MauticException;
use Joomla\Http\Http;
use Symfony\Component\HttpFoundation\RequestStack;
use Mautic\PluginBundle\Helper\IntegrationHelper;

class AddressValidatorHelper
{
    /**
     * @var Http $connector ;
     */
    protected $connector;

    /**
     * @var RequestStack $request ;
     */
    protected $request;

    /**
     * @var IntegrationHelper $integrationHelper
     */
    protected $integrationHelper;


    public function __construct(
        Http $connector,
        RequestStack $request,
        IntegrationHelper $integrationHelper
    ) {
        $this->connector = $connector;
        $this->request = $request->getCurrentRequest();
        $this->integrationHelper = $integrationHelper;
    }


    public function validation($check = false, $value = null)
    {


        $integration = $this->integrationHelper->getIntegrationObject('AddressValidator');

        if (!$integration || $integration->getIntegrationSettings()->getIsPublished() === false) {
            return;
        }

        $featureSettings = $integration->getDecryptedApiKeys();

        try {
            $data = $this->connector->post(
                $featureSettings['apiUrl'],
                    $this->request->request->all(),
                array(
                    'Authorization' => 'Token '.($value ? $value : $featureSettings['validatorApiKey']

                        ).'',
                ),
                10
            );
        } catch (\Exception $e) {
            return json_encode(['address_validated'=>false]);
        }

       if ($check) {
            if (trim($data->body) == 'HTTP Token: Access denied.') {
                return false;
            } else {
                return true;
            }
        } else {
            return $data->body;
        }
    }
}
