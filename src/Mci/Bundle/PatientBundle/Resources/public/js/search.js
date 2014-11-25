;var PatientSearch = function ($) {
    var errors_el;

    var atLeastOneGiven = function() {
        var $selected = $("input,select").map(function() { return this.value; }).get().join('');

        if(isNotBlank($selected)) {
            console.log($selected)
            return true;
        }

        errors_el.html("Please fill at least one field").show();
        return false;
    };

    function isNotBlank($phoneBlockExtraValue) {
        return "" !== $phoneBlockExtraValue.replace(/\s/g, '');
    }

    var isPhoneNoRequired = function() {
        var $phoneBlockExtraValue = $(".phone-block").map(function() { return this.value; }).get().join('');

        return isNotBlank($phoneBlockExtraValue);
    };

    function initializeDataTable() {
        $('.search-patient').dataTable({
            "searching": false,
            "paging": false,
            "oLanguage": {
                "sEmptyTable":     "No patient found"
            }
        });
    }

    function validateAgainstBusinessRules() {

        if(!atLeastOneGiven()) {
            return false;
        }



        return true;
    }

    function initValidator() {

        console.log("initial");
        var form = $('#searchPatientForm');

        form.validate({
            focusInvalid: false,
            ignore: "",
            rules: {
                nid: {
                    regex: '^([\\d]{13}|[\\d]{17})$'
                },
                brn: {
                    regex: '^[0-9]{17}$'
                },
                uid: {
                    regex: '^[a-zA-Z0-9]{11}$'
                },
                given_name: {
                    regex: '^[\\s\\S^0-9]{1,100}'
                },
                sur_name: {
                    regex: '^(\\s*)([A-Za-z^0-9]*)(\\b\\s*)$'
                },
                phone_no: {
                    regex: '^[0-9]{1,12}',
                    required : isPhoneNoRequired
                },
                division_id : {
                    required : function(element) {
                        return isNotBlank($("#given_name").val())
                    }
                },
                district_id : {
                    required : function(element) {
                        return $("#division").val() != ""
                    }
                },
                upazilla_id : {
                    required : function(element) {
                        return $("#district").val() != ""
                    }
                },
                area_code: {
                    regex: '^\\s*[0-9]*\\s*$'
                },
                country_code: {
                    regex: '^\\s*[0-9]*\\s*$'
                },
                extension: {
                    regex: '^\\s*[0-9]*\\s*$'
                }
            },
            messages: {},

            submitHandler: function (form) {
                console.log("submit");

                if(validateAgainstBusinessRules()) {
                    errors_el.hide();
                    return form.submit();
                }

                errors_el.show();
                return false;
            }
        });
    }


    function init () {
        errors_el = $('#error_div');
        initValidator();
        initializeDataTable();
    }

    return {
        init: init
    };

}(jQuery);