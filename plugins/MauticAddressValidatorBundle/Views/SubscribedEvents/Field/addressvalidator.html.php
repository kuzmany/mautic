<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$containerType = (isset($type)) ? $type : 'text';
$defaultInputClass = (isset($inputClass)) ? $inputClass : 'input';
include __DIR__.'/../../../../../app/bundles/FormBundle/Views/Field/field_helper.php';

$props = [];
foreach ($field['properties'] as $key => $property) {
    if (strpos($key, 'label') !== false || strpos($key, 'leadField') !== false) {
        $newKey = strtolower(str_ireplace(['label', 'leadField'], ['', ''], $key));
        if ($newKey) {
            $props[$newKey][str_ireplace($newKey, '', $key)] = $property;
        }
    }
}
$inputs = '';
foreach ($props as $key => $field2) {
    $inputAttr = 'class="mauticform-input " type="text"';
    if (empty($inForm)) {
        $inputAttr .= 'name="mauticform['.$field['alias'].']['.$key.']"';
    }
    $idBcKey = str_replace(
        ['address1', 'address2', 'city', 'zip', 'state', 'addressvalidated'],
        ['address_line_1', 'address_line_2', 'town_or_city', 'zip_or_postal_code', 'state_or_province', 'address_validated'],
        $key
    );
    $idAttr = 'mauticform_input'.$formName.'_'.$idBcKey;
    $placeholderAttr = "";
    if (isset($field['properties']['placeholderAddress']) && $field['properties']['placeholderAddress']) {
        $placeholderAttr = $view->escape($field2['label']);
    }

    if ($field2['label']) {
        $inputs .= <<<HTML
<div class="mauticform-row mauticform-required">
HTML;
        if ($field['showLabel']) {
            $inputs .= <<<HTML
<label class="mauticform-label" for="{$idAttr}" >{$view->escape($field2['label'])}</label>
HTML;
        }

        if ($idBcKey == "country" && !empty($field['properties']['optionsCountry'])) {
            $countryOptions = explode(chr(10), $field['properties']['optionsCountry']);
            $inputs .= <<<HTML
            <select id="{$idAttr}"  {$inputAttr}>
<option>{$field2['label']}</option>
HTML;
            foreach($countryOptions as $option){
                if($option) {
                    $inputs .= <<<HTML
                    <option value="$option">$option</option>
HTML;
                }
            }
            $inputs .= <<<HTML
                    </select>
HTML;

        } else {
            $inputs .= <<<HTML

           <input placeholder="{$placeholderAttr}" id="{$idAttr}"  {$inputAttr} type="$containerType" />
HTML;
        }
        $inputs .= <<<HTML
        </div>
HTML;
    }
    if( $key=='addressvalidated') {
        $inputAttr = str_replace('"text"', '"hidden"', $inputAttr);
        $inputs .= <<<HTML
           <input  id="{$idAttr}"  {$inputAttr}  value="Yes" />
HTML;
    }
}

$formNameWithout_ = str_replace('_', '', $formName);

if (!empty($inForm)):
    $html = <<<HTML
    
    <div {$containerAttr}>
    <div class="row">{$inputs}</div>
    </div>

HTML;
else:
    $html = <<<HTML
<div class="mauticform-row">{$inputs}</div>
<div id="mauticformmessage-wrap"><div class="mauticform-error" id="mauticform{$formName}_error"></div><div class="mauticform-message" id="mauticform{$formName}_message"></div></div>
 <input  class="addressvalidatorid" name="addressvalidatorid" value="{$field['form']->getId()}" type="hidden" /> 
 <input  class="addressvalidatorname" name="addressvalidatorname" value="{$formNameWithout_}" type="hidden" />
HTML;
    ?>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.1/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo $view['assets']->getBaseUrl() ?>/plugins/MauticAddressValidatorBundle/Assets/js/generate.js"></script>
    <?php
endif;
echo $html;