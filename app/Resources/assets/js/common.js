;var registerValidators = function () {

    jQuery.validator.addMethod(
        "regex",
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(jQuery.trim(value));
        },
        "Invalid input pattern"
    );

    jQuery.validator.addMethod("birthRegistration", function (value, element) {
        return this.optional(element) || /^[0-9]{17}$/.test(value.replace(/\s/g, ''));
    }, $.validator.format("Please enter valid BRN"));

    jQuery.validator.addMethod("unifiedIdentification", function (value, element) {
        return this.optional(element) || /^[a-zA-Z0-9]{11}$/.test(value.replace(/\s/g, ''));
    }, $.validator.format("Please enter valid UID"));

    jQuery.validator.addMethod("givenName", function (value, element) {
        return this.optional(element) || /^[\s\S]{1,100}$/.test(value.replace(/\s/g, ''));
    }, $.validator.format("Please enter maximum 100 alphabetic characters"));

    jQuery.validator.addMethod("phoneNumber", function (value, element) {
        return this.optional(element) || /^[0-9]{1,12}$/.test(value.replace(/\s/g, ''));
    }, $.validator.format("Please enter maximum 12 digits"));

    jQuery.validator.addMethod("surName", function (value, element) {
        return this.optional(element) || /^(\s*)([A-Za-z0-9]*)(\b\s*$)/.test(value);
    }, $.validator.format("Surname must be single word except special characters"));

    jQuery.validator.addMethod("maximum", function (value, element, param) {
            var re = new RegExp("^[\\s\\S]{1," + param + "}$", "g");

            return this.optional(element) || re.test(value.replace(/\s/g, ''));
        }, "Please enter maximum {0} alphabetic characters"
    );

    jQuery.validator.addMethod(
        "onlyNumber",
        function (number, element) {
            return this.optional(element) || /^\s*[0-9]*\s*$/.test(number);
        },
        "Only digits are allowed"
    );

    jQuery.validator.addMethod(
        "onlyCharacter",
        function (number, element) {
            return this.optional(element) || /^\s*[^0-9]*\s*$/.test(number);
        },
        "Digits are not allowed"
    );


    jQuery.validator.addMethod("maximumAlphaneumeric", function (value, element,param) {
        var re = new RegExp("^[a-zA-Z0-9]{1," + param + "}$", "g");
        return this.optional(element) || re.test(value.replace(/\s/g, ''));

    },"Please enter maximum {0} alphanumeric characters"

    );

    jQuery.validator.addMethod("postcode", function (value, element) {
        return this.optional(element) || /^[\d]{4}$/.test(value.replace(/\s/g, ''));
    }, $.validator.format("Please enter valid post code"));



};


jQuery(document).ready(function () {
    registerValidators();
    jQuery('.relation-nid').attr('name','mci_bundle_patientBundle_patients[relation][nid][]');
    jQuery('.relation-brn').attr('name','mci_bundle_patientBundle_patients[relation][bin_brn][]');
    jQuery('.relation-uid').attr('name','mci_bundle_patientBundle_patients[relation][uid][]');
    jQuery('.relation-type').attr('name','mci_bundle_patientBundle_patients[relation][type][]');
    jQuery('.relation-name-bangla').attr('name','mci_bundle_patientBundle_patients[relation][name_bangla][]');
    jQuery('.relation-given-name').attr('name','mci_bundle_patientBundle_patients[relation][given_name][]');
    jQuery('.relation-sur-name').attr('name','mci_bundle_patientBundle_patients[relation][sur_name][]');
    jQuery('.relation-relational-status').attr('name','mci_bundle_patientBundle_patients[relation][relational-status][]');



 jQuery("#patientEditForm").validate(
        {
            rules: {
                'mci_bundle_patientBundle_patients[nid]': {
                    nationalId: true
                },
                'mci_bundle_patientBundle_patients[relation][nid][]': {
                    nationalId: true
                },
                'mci_bundle_patientBundle_patients[bin_brn]': {
                    birthRegistration: true
                },
                'mci_bundle_patientBundle_patients[relation][bin_brn][]': {
                    birthRegistration: true
                },
                'mci_bundle_patientBundle_patients[uid]': {
                    unifiedIdentification: true
                },
                'mci_bundle_patientBundle_patients[relation][uid][]': {
                    unifiedIdentification: true
                },
                'mci_bundle_patientBundle_patients[given_name]': {
                    onlyCharacter: true,
                    givenName: true
                },
                'mci_bundle_patientBundle_patients[relation][given_name][]': {
                    onlyCharacter: true,
                    givenName: true
                },
                'mci_bundle_patientBundle_patients[sur_name]': {
                    onlyCharacter: true,
                    maximum: 25,
                    surName: true
                },
                'mci_bundle_patientBundle_patients[relation][sur_name][]': {
                    onlyCharacter: true,
                    maximum: 25,
                    surName: true
                },
                'mci_bundle_patientBundle_patients[phone_number][number]': {
                    onlyNumber: true,
                    phoneNumber: 12
                },
                'mci_bundle_patientBundle_patients[phone_number][area_code]': {
                    onlyNumber: true
                },
                'mci_bundle_patientBundle_patients[phone_number][country_code]': {
                    onlyNumber: true

                },
                'mci_bundle_patientBundle_patients[phone_number][extension]': {
                    onlyNumber: true
                },

                'mci_bundle_patientBundle_patients[primary_contact_number][number]': {
                    onlyNumber: true,
                    phoneNumber: 12
                },
                'mci_bundle_patientBundle_patients[primary_contact_number][area_code]': {
                    onlyNumber: true
                },
                'mci_bundle_patientBundle_patients[primary_contact_number][country_code]': {
                    onlyNumber: true

                },
                'mci_bundle_patientBundle_patients[primary_contact_number][extension]': {
                    onlyNumber: true
                },

                'mci_bundle_patientBundle_patients[date_of_birth]': {
                    dateFormat: true
                },
                'mci_bundle_patientBundle_patients[nationality]': {
                    maximum: 50
                },
                'mci_bundle_patientBundle_patients[place_of_birth]': {
                    maximumAlphaneumeric: 20
                },
                'mci_bundle_patientBundle_patients[name_bangla]': {
                    maximum: 120
                },
                'mci_bundle_patientBundle_patients[primary_contact]': {
                    maximum: 100
                },
                'mci_bundle_patientBundle_patients[present_address][holding_number]': {
                    maximum: 50
                },
                'mci_bundle_patientBundle_patients[present_address][street]': {
                    maximum: 50
                },
                'mci_bundle_patientBundle_patients[present_address][area_mouja]': {
                    maximum: 50
                },
                'mci_bundle_patientBundle_patients[present_address][village]': {
                    maximum: 50
                },
                'mci_bundle_patientBundle_patients[present_address][post_office]': {
                    maximum: 50
                },

                'mci_bundle_patientBundle_patients[present_address][post_code]': {
                    postcode: true
                },

                'mci_bundle_patientBundle_patients[permanent_address][holding_number]': {
                    maximum: 50
                },
                'mci_bundle_patientBundle_patients[permanent_address][street]': {
                    maximum: 50
                },
                'mci_bundle_patientBundle_patients[permanent_address][area_mouja]': {
                    maximum: 50
                },
                'mci_bundle_patientBundle_patients[permanent_address][village]': {
                    maximum: 50
                },
                'mci_bundle_patientBundle_patients[permanent_address][post_office]': {
                    maximum: 50
                },
                'mci_bundle_patientBundle_patients[permanent_address][post_code]': {
                    postcode: true
                }
            },
            messages: {

            },
            focusInvalid: false
        }
    );


    $('#mci_bundle_patientBundle_patients_present_address_division_id').on('change', function () {
        var x = $('#mci_bundle_patientBundle_patients_present_address_district_id');
        var y = $('#mci_bundle_patientBundle_patients_present_address_upazilla_id');
        var divisionId = $('option:selected', this).attr('data-id');
        populatedDistrictDropdown(x, y,divisionId);
    });

    $('#mci_bundle_patientBundle_patients_permanent_address_division_id').on('change', function () {
        var x = $('#mci_bundle_patientBundle_patients_permanent_address_district_id');
        var y = $('#mci_bundle_patientBundle_patients_permanent_address_upazilla_id');
        var divisionId = $('option:selected', this).attr('data-id');
        populatedDistrictDropdown(x, y,divisionId);
    });

    $('#mci_bundle_patientBundle_patients_present_address_district_id').on('change', function () {
        var x = $('#mci_bundle_patientBundle_patients_present_address_upazilla_id');
        var districtId = $('option:selected', this).attr('data-id');
        upazillaDropdwon(x,districtId);
    });

    $('#mci_bundle_patientBundle_patients_permanent_address_district_id').on('change', function () {
        var x = $('#mci_bundle_patientBundle_patients_permanent_address_upazilla_id');
        var districtId = $('option:selected', this).attr('data-id');
        upazillaDropdwon(x,districtId);
    });

    $('#division').on('change', function () {

        var x = $('#district');
        var y = $('#upazilla');
        var divisionId = $('option:selected', this).attr('data-id');
        populatedDistrictDropdown(x, y,divisionId);

    });

    $('#district').on('change', function () {
        var x = $('#upazilla');
        var districtId = $('option:selected', this).attr('data-id');
        upazillaDropdwon(x,districtId);
    });

    function upazillaDropdwon(x,districtId) {

        if (districtId) {
            x.removeAttr('disabled');
        }
        if (districtId) {
            x.empty();
            $.ajax({
                type: "POST",
                url: "/location/upazilla/" + districtId,
                beforeSend: function () {
                    $('.upazillaloader').show();
                },
                success: function (result) {
                    var options = generatedOptions(result);
                    x.append(options);
                },
                complete: function () {
                    $('.upazillaloader').hide();
                }
            });
        }
    }

    function populatedDistrictDropdown(x, y, divisionId) {

        if (divisionId) {
            x.removeAttr('disabled');
        } else {
            x.val("");
            y.val("");
            x.attr('disabled', 'disabled');
            y.attr('disabled', 'disabled');
        }

        if (divisionId) {
            x.empty();
            $.ajax({
                type: "POST",
                url: "/location/district/" + divisionId,
                beforeSend: function () {
                    $('.districtloader').show();
                },

                success: function (result) {
                    var options = generatedOptions(result);
                    x.append(options);
                },
                complete: function () {
                    $('.districtloader').hide();
                }
            });
        }
    }


    function generatedOptions(result) {
        var obj = $.parseJSON(result);
        var options = "<option value=''>-Please Select-</option>";
        for (var i in obj) {
            options += '<option data-id = "' + obj[i]['id'] + '"  value="' + obj[i]['code'] + '">' + obj[i]['name'] + '</option>';
        }
        return options;
    }


});


