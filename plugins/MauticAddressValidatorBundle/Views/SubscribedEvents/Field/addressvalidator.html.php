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
        ['address1', 'address2', 'city', 'zip', 'state'],
        ['address_line_1', 'address_line_2', 'town_or_city', 'zip_or_postal_code', 'state_or_province'],
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
}

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
<input id="mauticform_input{$formName}_address_validated" name="mauticform[address_validated]" value="Yes" class="mauticform-hidden" type="hidden">
HTML;
    ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.1/jquery.min.js" charset="utf-8"></script>

    <script>
        //
        //  UPDATE form name and number below (and also in script above)
        //
        //  It waits till the document is completely loaded to access all the DOM in the form
        //  It calls .validateAddress function first,
        //  THEN submit the form on VALID status back,
        //  OR display formattedAddress on the form with checkbox checked on SUSPECT status back
        //  OR display "Invalid Address" on the form on INVALID status back
        $(document).ready(function () {

            var baseUrl = "http://av.ballistix.com/validators";
            var formName = '<?php echo str_replace('_', '', $formName); ?>';
            var formNumber = <?php echo $field['form']->getId(); ?>;

            $.validateAddress = function () {
                $.fn.validateAddress = function (cb) {

                    var address = this.val();
                    var streetaddress = $("#mauticform_input_" + formName + "_address_line_1").val() + " " + $("#mauticform_input_" + formName + "_address_line_2").val();
                    var res = $.post(baseUrl,
                        {
                            "StreetAddress": streetaddress,
                            "City": $("#mauticform_input_" + formName + "_town_or_city").val(),
                            "PostalCode": $("#mauticform_input_" + formName + "_zip_or_postal_code").val(),
                            "State": $("#mauticform_input_" + formName + "_state_or_province").val(),
                            "CountryCode": $("#mauticform_input_" + formName + "_country").val(),
                            "AddressValidated": $("#mauticform_input_" + formName + "_address_validated").val()
                        },
                        'json');

                    res.done(function (data) {
                        cb(JSON.stringify(res));
                    });

                    return this;
                };
            }

            $.validateAddress();
            $("#mauticform_" + formName + "_country").after('<div id="mauticformmessage-wrap" />');
            $("#mauticform_" + formName + "_error").appendTo('#mauticformmessage-wrap');
            $("#mauticform_" + formName + "_message").appendTo('#mauticformmessage-wrap');

            // OnClick
            $("#mauticform_input_" + formName + "_submit").click(function (e) {
                e.preventDefault();

                var selected = $("#mauticform_" + formName).find('#addressCheckbox');

                $("#mauticform_" + formName + "_error").text("");
                $("#mauticform_" + formName + "_message").text("");

                if (selected.prop("checked")) {
                    //WHEN the form is submitted with the formattedAddress checkbox checked,
                    //The address needs to be parsed to be assigned to the proper input fields
                    input_normalizer();
                    $("#mauticform_" + formName).submit();
                }

                else {
                    $("#mauticform_" + formName).validateAddress(function (response_full) {
                        response = JSON.parse(response_full).responseJSON;

                        //if address_validated is false, submit as is.
                        if (response.address_validated == false) {
                            $("#mauticform_input_" + formName + "_address_validated").val("No");
                            $("#mauticform_" + formName).submit();
                        } else if (response.status == "VALID") {
                            add_formatted_address();
                            input_normalizer();
                            $("#mauticform_" + formName).submit();
                        } else {
                            if (response.status == "INVALID" && response.formattedaddress == null) {
                                $("#mauticform_" + formName + "_error").text("The address you submitted was not recognized. Please edit your submission and try again.");
                                $("#mauticformmessage-wrap").addClass("error");
                            } else if (response.status == "SUSPECT" || response.formattedaddress != null) {
                                $("#mauticform_" + formName + "_error").text("Corrected Address:");
                                $("#mauticformmessage-wrap").addClass("info");
                                if (response.formattedaddress != null) {
                                    addCheckbox(response.formattedaddress);//show css box above submit button with a checkbox
                                    add_formatted_address();
                                }
                            }

                        }
                    });
                }

                function addCheckbox(address) {
                    var container = $("#mauticform_" + formName + "_message")

                    if (container.find('#addressCheckbox').length != 0) {
                        $('#addressCheckbox_label').text(address);
                        $('#addressCheckbox').prop('checked', true);
                    }
                    else {
                        $('<input />', {type: 'checkbox', id: 'addressCheckbox', value: address}).appendTo(container);
                        $('<label />', {
                            'for': 'addressCheckbox',
                            id: 'addressCheckbox_label',
                            text: address
                        }).appendTo(container);
                    }
                    $("#addressCheckbox").attr("checked", true);
                }

                function add_formatted_address() {
                    $('<input id="response_address_line_1">').attr('type', 'hidden').appendTo("#mauticform_" + formName);
                    $('<input id="response_town_or_city">').attr('type', 'hidden').appendTo("#mauticform_" + formName);
                    $('<input id="response_state_or_province">').attr('type', 'hidden').appendTo("#mauticform_" + formName);
                    $('<input id="response_zip_or_postal_code">').attr('type', 'hidden').appendTo("#mauticform_" + formName);
                    $('<input id="response_country">').attr('type', 'hidden').appendTo("#mauticform_" + formName);

                    $('#response_address_line_1').val(response.addressline1);
                    $('#response_town_or_city').val(response.city);
                    $('#response_state_or_province').val(response.state);
                    $('#response_zip_or_postal_code').val(response.postalcode);
                    $('#response_country').val(response.country);
                }

                function input_normalizer() {
                    var addressLine = $('#response_address_line_1').val();
                    var city = $('#response_town_or_city').val();
                    var state = $('#response_state_or_province').val();
                    var postalcode = $('#response_zip_or_postal_code').val();
                    var country = $('#response_country').val();

                    $("#mauticform_input_" + formName + "_address_line_1").val(addressLine);
                    $("#mauticform_input_" + formName + "_address_line_2").val("");
                    $("#mauticform_input_" + formName + "_town_or_city").val(city);
                    $("#mauticform_input_" + formName + "_state_or_province").val(state);
                    $("#mauticform_input_" + formName + "_zip_or_postal_code").val(postalcode);
                    $("#mauticform_input_" + formName + "_country").val(country);

                    //	    $("#mauticformmessage-wrap").remove();
                }
            });
        })

    </script><!-- ///////////////   END of Address Validator  /////////////////// -->
    <?php

endif;
echo $html;