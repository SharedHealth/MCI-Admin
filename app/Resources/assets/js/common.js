var registerValidators = function () {

    jQuery.validator.addMethod(
        "nationalId",
        function (number, element) {
            return this.optional(element) || /^([\d]{13}|[\d]{17})$/.test(number.replace(/\s/g, ''));
        },
        "Please enter valid NID"
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

    jQuery.validator.addMethod(
        "dateFormat",
        function(value, element) {
            var re = /^\d{4}-\d{2}-\d{2}$/;
            return  re.test(value);
        },
        "Please enter a valid date format yyyy-mm-dd"
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
   jQuery('.relation-nid').attr('name','relation[nid][]');
   jQuery('.relation-brn').attr('name','relation[bin_brn][]');
   jQuery('.relation-uid').attr('name','relation[uid][]');
   jQuery('.relation-type').attr('name','relation[type][]');
   jQuery('.relation-name-bangla').attr('name','relation[name_bangla][]');
   jQuery('.relation-given-name').attr('name','relation[given_name][]');
    jQuery('.relation-sur_name').attr('name','relation[sur_name][]');
    jQuery('.relation-relational-status').attr('name','relation[relational-status][]');

   jQuery("#searchPatientForm").validate(
        {
            rules: {
                nid: {
                    nationalId: true
                },
                brn: {
                    birthRegistration: true
                },
                uid: {
                    unifiedIdentification: true
                },
                given_name: {
                    onlyCharacter: true,
                    givenName: true
                },
                sur_name: {
                    onlyCharacter: true,
                    maximum: 25,
                    surName: true
                },
                phone_no: {
                    onlyNumber: true,
                    phoneNumber: 12
                },
                area_code: {
                    onlyNumber: true
                },
                country_code: {
                    onlyNumber: true

                },
                extension: {
                    onlyNumber: true

                }
            },
            messages: {


            },
            focusInvalid: false
        }
    );


 jQuery("#patientEditForm").validate(
        {
            rules: {
                'mci_bundle_patientBundle_patients[nid]': {
                    nationalId: true
                },
                'relation[nid][]': {
                    nationalId: true
                },
                'mci_bundle_patientBundle_patients[bin_brn]': {
                    birthRegistration: true
                },
                'relation[bin_brn][]': {
                    birthRegistration: true
                },
                'mci_bundle_patientBundle_patients[uid]': {
                    unifiedIdentification: true
                },
                'relation[uid][]': {
                    unifiedIdentification: true
                },
                'mci_bundle_patientBundle_patients[given_name]': {
                    onlyCharacter: true,
                    givenName: true
                },
                'relation[given_name][]': {
                    onlyCharacter: true,
                    givenName: true
                },
                'mci_bundle_patientBundle_patients[sur_name]': {
                    onlyCharacter: true,
                    maximum: 25,
                    surName: true
                },
                'relation[sur_name][]': {
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

});


