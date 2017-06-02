//
//  UPDATE form name and number below (and also in script above)
//
//  It waits till the document is completely loaded to access all the DOM in the form
//  It calls .validateAddress function first,
//  THEN submit the form on VALID status back,
//  OR display formattedAddress on the form with checkbox checked on SUSPECT status back
//  OR display "Invalid Address" on the form on INVALID status back
$(document).ready(function () {

    var getCurrentScript = function () {
        if (document.currentScript) {
            return document.currentScript.src;
        } else {
            var scripts = document.getElementsByTagName('script');
            return scripts[scripts.length-1].src;

        }
    };

    var baseUrl = "http://av-test.ballistix.com/validators";
    var mauticUrl =   (getCurrentScript()).replace('plugins/MauticAddressValidatorBundle/Assets/js/generate.js','');;



    $('.addressvalidatorid').each(function () {


        var formName = $(this).next().val();
        var formNumber = $(this).val();
        addressValidatorDo(formName, formNumber);

    })

    function addressValidatorDo(formName, formNumber) {
        $.validateAddress = function () {
            $.fn.validateAddress = function (cb) {

                var address = this.val();
                var streetaddress = $("#mauticform_input_" + formName + "_address_line_1").val() + " " + $("#mauticform_input_" + formName + "_address_line_2").val();
                var request = {
                    'address': this.val(),
                    'streetaddress': streetaddress,
                };
                var res = $.ajax(
                    {
                        type: 'POST',
                        dataType: 'json',
                        url: mauticUrl+'addressvalidation',
                        data: {
                            "StreetAddress": streetaddress,
                            "City": $("#mauticform_input_" + formName + "_town_or_city").val(),
                            "PostalCode": $("#mauticform_input_" + formName + "_zip_or_postal_code").val(),
                            "State": $("#mauticform_input_" + formName + "_state_or_province").val(),
                            "CountryCode": $("#mauticform_input_" + formName + "_country").val(),
                            "AddressValidated": $("#mauticform_input_" + formName + "_address_validated").val()
                        },
                        success: function (data) {
                            cb(JSON.stringify(res));
                        }
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
    }
})
