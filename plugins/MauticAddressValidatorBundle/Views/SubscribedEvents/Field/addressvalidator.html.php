<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

$containerType     = (isset($type)) ? $type : 'text';
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
    $addAttr   = '';
    $inputType = $containerType = 'text';
    $prefix    = '';
    if ($key == 'addressvalidated') {
        $inputType = $containerType = 'checkbox';
        $prefix    = 'checkboxgrp-';
        $addAttr   = ' data-disabled="1" data-correctedaddress="1" ';
    } else {
        $prefix    = '';
        $inputType = 'input';
        // just compatibility with validator repsonse
        $serviceReponseKey = str_replace(['address1', 'zip'], ['addressline1', 'postalcode'], $key);
        $addAttr           = 'data-validate-type="'.$serviceReponseKey.'" data data-disabled="1" ';
        if ($key == 'toogle') {
            if (empty($field['properties']['validatorToogle'])) {
                continue;
            }
            $inputType = $containerType = 'checkbox';
            $prefix    = 'checkboxgrp-';
            $addAttr   = 'data-validate-type="'.$serviceReponseKey.'" ';
        } elseif ($key == 'address4') {
            $addAttr = '';
        }
    }

    $inputAttr = 'class="mauticform-'.$prefix.$inputType.'" type="'.$containerType.'"'.$addAttr;
    if (empty($inForm)) {
        $inputAttr .= 'name="mauticform['.$field['alias'].']['.$key.']"';
    }
    $idBcKey = str_replace(
        ['address1', 'address2', 'city', 'zip', 'state', 'addressvalidated'],
        [
            'address_line_1',
            'address_line_2',
            'town_or_city',
            'zip_or_postal_code',
            'state_or_province',
            'address_validated',
        ],
        $key
    );
    $idAttr          = 'mauticform_input'.$formName.'_'.$idBcKey;
    $placeholderAttr = '';
    if (isset($field['properties']['placeholderAddress']) && $field['properties']['placeholderAddress']) {
        $placeholderAttr = $view->escape($field2['label']);
    }

    if ($field2['label'] || $key == 'addressvalidated') {
        $classContainer = 'mauticform-row';
        if (!empty($field['properties']['validatorRequired'])) {
            $classContainer .= ' mauticform-required';
        }
        if ($key == 'addressvalidated') {
            $classContainer .= ' mauticform-row-validated';
        }

        $inputs .= <<<HTML
<div class="{$classContainer}">
HTML;

        if ($field['showLabel'] || $key == 'toogle') {
            $inputs .= <<<HTML
<label class="mauticform-{$prefix}label" for="{$idAttr}" >{$view->escape($field2['label'])}</label>
HTML;
        }

        if ($idBcKey == 'country' && !empty($field['properties']['optionsCountry'])) {
            $countryOptions = explode(chr(10), $field['properties']['optionsCountry']);
            $inputs .= <<<HTML
            <select id="{$idAttr}"  {$inputAttr}>
HTML;
            if (empty($field['properties']['placeholderAddress'])) {
                $inputs .= <<<HTML
<option value="">{$field2['label']}</option>
HTML;
            }
            foreach ($countryOptions as $option) {
                if ($option) {
                    $inputs .= <<<HTML
                    <option value="$option">$option</option>
HTML;
                }
            }
            $inputs .= <<<HTML
                    </select>
HTML;
        } else {
            if ($key == 'addressvalidated') {
                $inputs .= <<<HTML
<label style="display:block" class="mauticform-{$prefix}label" for="{$idAttr}" >{$view['translator']->trans('plugin.addressvalidator.field.label.corrected.address')}</label><input placeholder="{$placeholderAttr}" id="{$idAttr}"  {$inputAttr} type="$containerType" data-old-value="Yes" data-empty-value="Yes" value="Yes" />
HTML;
            } else {
                $inputs .= <<<HTML
           <input placeholder="{$placeholderAttr}" id="{$idAttr}"  {$inputAttr} type="$containerType" />
HTML;
            }
        }
        $inputs .= <<<HTML
        </div>
HTML;
    }
    if ($key == 'address4') {
        $inputAttr = str_replace('"text"', '"hidden"', $inputAttr);
        $inputs .= <<<HTML
           <input  id="{$idAttr}"  {$inputAttr}  value="" />
HTML;
    }

    /* if( $key=='addressvalidated') {
         $inputs .= <<<HTML
           <input placeholder="{$placeholderAttr}" id="{$idAttr}"  {$inputAttr} type="$containerType" /> addressvalidated
HTML;
     }*/
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
<div class="mauticform-row"><div  data-validation-type="plugin.addressvalidator" data-validate-id="{$field['id']}"  data-validate-alias="{$field['alias']}" data-validate-form-id="{$field['form']->getId()}" data-validate-form-name="{$formNameWithout_}" {$containerAttr}>{$inputs}<div class="mauticform-errormsg" style="display: none;">$validationMessage</div></div></div>
<div id="mauticformmessage-wrap"><div class="mauticform-error" id="mauticform{$formName}_error"></div><div class="mauticform-message" id="mauticform{$formName}_message"></div></div>
 <input  class="addressvalidatorid" name="addressvalidatorid" value="{$field['form']->getId()}" type="hidden" /> 
 <input  class="addressvalidatorname" name="addressvalidatorname" value="{$formNameWithout_}" type="hidden" />
<script type="text/javascript" src="{$view['router']->url('mautic_addressvalidator_js')}"></script>
HTML;

endif;
echo $html;
