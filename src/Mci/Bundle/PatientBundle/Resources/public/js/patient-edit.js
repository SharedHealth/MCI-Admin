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

function validateAgainstBusinessRules(){

    var maritalRelation = maritalrelation();
        if(maritalRelation == 'unmatched') {
            $error = "Please change either marital status or spouse as relation";
            $('.dependencyError').html($error);
            return true;
        }
        if(unionsOfCityCorporation()){
            $error = "Please select Union/Urban Ward";
            $('.dependencyError').html($error);
            return true;
        }
        if($msg = countryCode()){
            $error = $msg;
            $('.dependencyError').html($error);
            return true;
        }

}

function maritalrelation(){
    var marital_status =  $('#mci_bundle_patientBundle_patients_marital_status').val();
    var relation_type = 'matched';
    $(".relation-type").each(function () {
        if($(this).val() == 'SPS' && marital_status == 1){
            relation_type = 'unmatched';
            return false;
        }
    });
    return relation_type;

}

function unionsOfCityCorporation(){
    $cityCorporation_id = $("#mci_bundle_patientBundle_patients_present_address_city_corporation_id").val();
    $urban_word_id = $("#mci_bundle_patientBundle_patients_present_address_union_or_urban_ward_id").val();

    $cityCorporation_id_permanent_address = $("#mci_bundle_patientBundle_patients_permanent_address_city_corporation_id").val();
    $urban_word_id_permanent_address = $("#mci_bundle_patientBundle_patients_permanent_address_union_or_urban_ward_id").val();
    $permanentCountryCode = $('#mci_bundle_patientBundle_patients_permanent_address_country_code').val();

    if($cityCorporation_id == 99 && $urban_word_id == ""){
        return true;
    }

    if($permanentCountryCode == '050' && $cityCorporation_id_permanent_address == 99 && $urban_word_id_permanent_address == ""){
        return true;
    }

}

function countryCode(){
    $permanentCountryCode = $('#mci_bundle_patientBundle_patients_permanent_address_country_code').val();
    $addressLine =  $('#mci_bundle_patientBundle_patients_permanent_address_address_line').val();
    $division = $("#mci_bundle_patientBundle_patients_permanent_address_division_id").val();
    $district = $("#mci_bundle_patientBundle_patients_permanent_address_district_id").val();
    $upazila = $("#mci_bundle_patientBundle_patients_permanent_address_upazila_id").val();

    if($permanentCountryCode !="" && $permanentCountryCode == '050' && ($division == "" || $district == "" || $upazila == "" || $addressLine =="")){
        return "Please select Address line, Division, District and Upazila";
    }

    if($permanentCountryCode !="" && ($permanentCountryCode != '050' && $addressLine =="")){
        return "Please select Address line";
    }
}

function changedNameForValidation() {

    var relation = 'mci_bundle_patientBundle_patients[relation]';
    jQuery('.relation-id').attr('name', relation + '[id][]');
    jQuery('.relation-nid').attr('name', relation + '[nid][]');
    jQuery('.relation-hid').attr('name', relation + '[hid][]');
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
    $value =  $('#mci_bundle_patientBundle_patients_permanent_address_country_code').val();
    permanentAddressBlock($value);

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
                    required: true,
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
                'mci_bundle_patientBundle_patients[date_of_birth]': {
                    required: true
                },
                'mci_bundle_patientBundle_patients[gender]': {
                    required: true
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
                'mci_bundle_patientBundle_patients[present_address][address_line]': {
                    required : true,
                    minlength: 3,
                    maxlength: 255
                },
                'mci_bundle_patientBundle_patients[present_address][holding_number]': {
                    maximum: 50
                },
                'mci_bundle_patientBundle_patients[present_address][division_id]': {
                    required: true
                },
                'mci_bundle_patientBundle_patients[present_address][district_id]': {
                    required: true
                },
                'mci_bundle_patientBundle_patients[present_address][upazila_id]': {
                    required: true
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
                'mci_bundle_patientBundle_patients[permanent_address][address_line]': {
                    minlength: 3,
                    maxlength: 255
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
                },
                'mci_bundle_patientBundle_patients[relation][marriage_id][]': {
                    regex: '^[0-9]{8}$'
                },
                'mci_bundle_patientBundle_patients[relation][hid][]': {
                    regex: '^[0-9]{11}$'
                },
                'mci_bundle_patientBundle_patients[household_code]': {
                    regex: '^[0-9]*$'
                }
            },
            messages: {
            },
            submitHandler: function (form) {
                if(!validateAgainstBusinessRules()) {
                    $('.dependencyError').hide();
                    $('.dependencyError').html("");
                    return form.submit();
                }
                $('.dependencyError').show();
                return false;
            },
            focusInvalid: false
        }
    );

    $('#mci_bundle_patientBundle_patients_present_address_division_id').on('change', function() {
        $(this).valid();
    });

    $('#mci_bundle_patientBundle_patients_present_address_district_id').on('change', function() {
        $(this).valid();
    });

    $('#mci_bundle_patientBundle_patients_present_address_upazila_id').on('change', function() {
        $(this).valid();
    });

    $('#mci_bundle_patientBundle_patients_permanent_address_country_code').on('change', function() {
        $value = $(this).val();
        permanentAddressBlock($value);

    });

    $('#mci_bundle_patientBundle_patients_status_type').on('change', function() {
        if($(this).val() !='2'){
            $('#mci_bundle_patientBundle_patients_status_date_of_death').val('');
            $('#mci_bundle_patientBundle_patients_status_date_of_death').attr('disabled','disabled').css("cursor", "default");
        }else{
            $('#mci_bundle_patientBundle_patients_status_date_of_death').removeAttr('disabled','disabled').css("cursor", "pointer");
        }
    });

    if($('#mci_bundle_patientBundle_patients_status_type').val() !='2'){
        $('#mci_bundle_patientBundle_patients_status_date_of_death').attr('disabled','disabled').css("cursor", "default");
    }

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
       if (!confirm('Are you sure?')) return false;
        $(this).parent().parent().remove();
        var hid = $(this).attr('data-hid');
        var marital_status = $(this).attr('data-marital-status');
        var rid = $(this).parent().parent().find('.relation-id').val();
        var rtype = $(this).parent().parent().find('.relation-type').val();
    if(rid){
        $.ajax({
            type: "POST",
            url: "/patients/"+hid+"/relation/remove/",
            data:{ realtionId: rid, relationType: rtype, maritalStatus: marital_status },
            success: function (result) {
                if(result != 'ok'){
                    window.location.href = "/patients/"+hid+"/edit/";
                }
            }
        });
    }
        return true;
   });

    $('.datepicker_common').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss'
    });


    $(".clearBtn").on('click',function(){
        $(this).parent().find('#mci_bundle_patientBundle_patients_status_date_of_death').val("")
    });

    $('table tbody tr').delegate('.relation-type','change',function(){
       var relationType =  $(this).val();
        if(relationType == 'SPS'){
            $(this).parent().parent().find('.relation-marriage-id').removeAttr('readonly');
            $(this).parent().parent().find('.relation-relational-status').removeAttr('readonly');
            $(this).parent().parent().find('.relation-relational-status').show();
            $(this).parent().parent().find('.dumyTextBoxHide').hide();
        }else{
            $(this).parent().parent().find('.relation-marriage-id').attr('readonly', 'readonly');
            $(this).parent().parent().find('.relation-relational-status').attr('readonly', 'readonly');
            $(this).parent().parent().find('.relation-relational-status').hide();
            $(this).parent().parent().find('.dumyTextBoxHide').show();
        }

    });

   $('.alert-success').fadeOut(2000);
    tabError();
});

function addRelationForm($collectionHolder) {
        var prototype = $collectionHolder.data('prototype');
        var index = $collectionHolder.data('index');
        var newForm = prototype.replace(/__name__/g, index);
        $collectionHolder.data('index', index + 1);
        $($collectionHolder).append(newForm);

 }

 function tabError () {
    $('#mci_bundle_patientBundle_patients_save').click(function(){

        $("#patient-update-tabs li").each(function () {
            var aEl = $(this).find('a');
            if($(aEl.attr('href')).find('input.error,select.error').length > 0) {
                aEl.addClass('tabError')
            }else{
                aEl.removeClass('tabError')
            }
        });

    });
}


function permanentAddressBlock($value){

    $division = $("#mci_bundle_patientBundle_patients_permanent_address_division_id");
    $district = $("#mci_bundle_patientBundle_patients_permanent_address_district_id");
    $upazila = $("#mci_bundle_patientBundle_patients_permanent_address_upazila_id");
    $citycorporation = $("#mci_bundle_patientBundle_patients_permanent_address_city_corporation_id");
    $urbanword = $("#mci_bundle_patientBundle_patients_permanent_address_union_or_urban_ward_id");
    $ruralword = $("#mci_bundle_patientBundle_patients_permanent_address_rural_ward_id");

    if($value != '050'){
        $division.prop('selectedIndex',0);
        $district.prop('selectedIndex',0);
        $upazila.prop('selectedIndex',0);
        $citycorporation.prop('selectedIndex',0);
        $urbanword.prop('selectedIndex',0);
        $ruralword.prop('selectedIndex',0);

        $division.attr('disabled','disabled');
        $district.attr('disabled','disabled');
        $upazila.attr('disabled','disabled');
        $citycorporation.attr('disabled','disabled');
        $urbanword.attr('disabled','disabled');
        $ruralword.attr('disabled','disabled');
    }else{
        $division.removeAttr('disabled');
        $district.removeAttr('disabled');
        $upazila.removeAttr('disabled');
        $citycorporation.removeAttr('disabled');
        $urbanword.removeAttr('disabled');
        $ruralword.removeAttr('disabled');
    }

}



