;var PatientSearch = function ($) {
    var errors_el;
    var hide_time_out = null;

    function isInvalidName() {
        return isBlank($("#given_name").val()) && isGivenNameRequired();
    }

    function resolveErrorMessage() {
        switch (true) {
            case isInvalidName() : return "Please enter a valid name"
            case onlyNameGiven(): return "Please provide a valid ID, Address or Phone number"
            case onlyAddressGiven(): return "Please provide a valid ID, Name or Phone number"
            default : return "Incomplete search criteria!"
        }
    }

    var showErrorMessage =function (message) {
        errors_el.html(message).show(200);

        if(hide_time_out) {
            clearTimeout(hide_time_out);
        }

        hide_time_out = setTimeout(function(){
            errors_el.hide(500);
        }, 3000);
    };

    var isEmptySearchParameter = function() {
        return isBlank(getValueBySelector("input,select"));
    };

    var isSingleSearchableBlockGiven = function() {
        return isNotBlank(getValueBySelector(".single-searchable"));
    }

    var atLeastOneGiven = function() {

        if(isEmptySearchParameter()) {
            showErrorMessage("No search parameter selected");
            return false;
        }

        return true;
    };



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

    function onlyNameGiven() {
        return isNotBlank($("#given_name").val()) || isNotBlank($("#sur_name").val())
    }

    function onlyAddressGiven() {
        return $("#division").val() != ""
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

        showErrorMessage(resolveErrorMessage());

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
                bin_brn: {
                    regex: '^[0-9]{17}$'
                },
                uid: {
                    regex: '^[a-zA-Z0-9]{11}$'
                },
                given_name: {
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