;var PatientSearch = function ($) {
    var errors_el;

    var isEmptySearchParameter = function() {
        return isBlank(getValueBySelector("input,select"));
    };

    var isSingleSearchableBlockGiven = function() {
        return isNotBlank(getValueBySelector(".single-searchable"));
    }

    var atLeastOneGiven = function() {

        if(isEmptySearchParameter()) {
            errors_el.html("No search parameter selected").show();
            return false;
        }

        return true;
    };

    function isBlank(str) {
        console.log(str);
        return "" == str.replace(/\s/g, '');
    }

    function isNotBlank(str) {
        return !isBlank(str);
    }

    function getValueBySelector(selector) {
        return $(selector).map(function () {
            return this.value;
        }).get().join('');
    }

    var isPhoneNoRequired = function() {
        return isNotBlank(getValueBySelector(".phone-block"));
    };

    var isGivenNameRequired = function() {
        return (isNotBlank($("#sur_name").val()) && !isSingleSearchableBlockGiven());
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

    function isNameAndAddressGiven() {
        return isNotBlank($("#given_name").val()) && $("#division").val() != ""
    }

    function validateAgainstBusinessRules() {

        if(!atLeastOneGiven()) {
            return false;
        }

        if(isSingleSearchableBlockGiven()) {
            return true;
        }

        if(isNameAndAddressGiven()) {
            return true;
        }

        errors_el.html("Incomplete search criteria!").show();

        return false;
    }

    function initValidator() {

        var form = $('#searchPatientForm');

        form.validate({
            focusInvalid: false,
            ignore: "",
            rules: {
                nid: {
                    regex: '^([0-9]{13}|[0-9]{17})$'
                },
                brn: {
                    regex: '^[0-9]{17}$'
                },
                uid: {
                    regex: '^[a-zA-Z0-9]{11}$'
                },
                given_name: {
                    required: isGivenNameRequired,
                    regex: '^[\\s\\S^0-9]{1,100}$'
                },
                sur_name: {
                    regex: '^(\\s*)([A-Za-z^0-9]{1,25})(\\b\\s*)$'
                },
                phone_no: {
                    regex: '^[0-9]{1,12}$',
                    required : isPhoneNoRequired
                },
                district_id : {
                    required : function() {
                        return $("#division").val() != ""
                    }
                },
                upazilla_id : {
                    required : function() {
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