;var registerValidators = function () {

    jQuery.validator.addMethod("maximum", function (value, element, param) {
            var re = new RegExp("^[\\s\\S]{1," + param + "}$", "g");

            return this.optional(element) || re.test(value.replace(/\s/g, ''));
        }, "Please enter maximum {0} alphabetic characters"
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



var isPhoneNoRequired = function() {
    return isNotBlank(getValueBySelector("#phoneNumber .phone-block"));
};

var isPrimaryPhoneNoRequired = function() {
    return isNotBlank(getValueBySelector("#primaryContactNo .phone-block"));
};

function changedNameForValidation() {

    var relation = 'mci_bundle_patientBundle_patients[relation]';
    jQuery('.relation-id').attr('name', relation + '[id][]');
    jQuery('.relation-nid').attr('name', relation + '[nid][]');
    jQuery('.relation-brn').attr('name', relation + '[bin_brn][]');
    jQuery('.relation-uid').attr('name', relation + '[uid][]');
    jQuery('.relation-type').attr('name', relation + '[type][]');
    jQuery('.relation-name-bangla').attr('name', relation + '[name_bangla][]');
    jQuery('.relation-given-name').attr('name', relation + '[given_name][]');
    jQuery('.relation-sur-name').attr('name', relation + '[sur_name][]');
    jQuery('.relation-marriage-id').attr('name', relation + '[marriage_id][]');
    jQuery('.relation-relational-status').attr('name', relation + '[relational_status][]');
}

jQuery(document).ready(function () {
    registerValidators();
    changedNameForValidation();

    jQuery("#patientEditForm").validate(
        {
            ignore: [],
            rules: {
                'mci_bundle_patientBundle_patients[nid]': {
                    regex: '^([0-9]{13}|[0-9]{17})$'
                },
                'mci_bundle_patientBundle_patients[relation][nid][]': {
                    regex: '^([0-9]{13}|[0-9]{17})$'
                },
                'mci_bundle_patientBundle_patients[bin_brn]': {
                    regex: '^[0-9]{17}$'
                },
                'mci_bundle_patientBundle_patients[relation][bin_brn][]': {
                    regex: '^[0-9]{17}$'
                },
                'mci_bundle_patientBundle_patients[uid]': {
                    regex: '^[a-zA-Z0-9]{11}$'
                },
                'mci_bundle_patientBundle_patients[relation][uid][]': {
                    regex: '^[a-zA-Z0-9]{11}$'
                },
                'mci_bundle_patientBundle_patients[given_name]': {
                    regex: '^[\\s\\S^0-9]{1,100}$'
                },
                'mci_bundle_patientBundle_patients[relation][given_name][]': {
                    regex: '^[\\s\\S^0-9]{1,100}$'
                },
                'mci_bundle_patientBundle_patients[sur_name]': {
                    regex: '^(\\s*)([A-Za-z^0-9]{1,25})(\\b\\s*)$'
                },
                'mci_bundle_patientBundle_patients[relation][sur_name][]': {
                    regex: '^(\\s*)([A-Za-z^0-9]{1,25})(\\b\\s*)$'
                },
                'mci_bundle_patientBundle_patients[phone_number][number]': {
                    regex: '^[0-9]{1,12}$',
                    required : isPhoneNoRequired
                },
                'mci_bundle_patientBundle_patients[phone_number][area_code]': {
                    regex: '^\\s*[0-9]*\\s*$'
                },
                'mci_bundle_patientBundle_patients[phone_number][country_code]': {
                    regex: '^\\s*[0-9]*\\s*$'

                },
                'mci_bundle_patientBundle_patients[phone_number][extension]': {
                    regex: '^\\s*[0-9]*\\s*$'
                },

                'mci_bundle_patientBundle_patients[primary_contact_number][number]': {
                    regex: '^[0-9]{1,12}$',
                    required : isPrimaryPhoneNoRequired
                },
                'mci_bundle_patientBundle_patients[primary_contact_number][area_code]': {
                    regex: '^\\s*[0-9]*\\s*$'
                },
                'mci_bundle_patientBundle_patients[primary_contact_number][country_code]': {
                    regex: '^\\s*[0-9]*\\s*$'

                },
                'mci_bundle_patientBundle_patients[primary_contact_number][extension]': {
                    regex: '^\\s*[0-9]*\\s*$'
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


    var $collectionHolder;
    var $addRelation = $('#addRelation');

    $collectionHolder = $('tbody.relation-body');
    $collectionHolder.data('index', $collectionHolder.find('tr').length);

    $addRelation.on('click', function(e) {
    e.preventDefault();
     addRelationForm($collectionHolder);

     changedNameForValidation();

   });

   $('table tbody ').delegate('.remove','click',function(){
        $(this).parent().parent().remove();
   });


});

    function addRelationForm($collectionHolder) {

        var prototype = $collectionHolder.data('prototype');
        var index = $collectionHolder.data('index');
        var newForm = prototype.replace(/__name__/g, index);
        $collectionHolder.data('index', index + 1);
        $($collectionHolder).append(newForm);

 }





