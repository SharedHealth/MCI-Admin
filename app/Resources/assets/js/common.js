
jQuery(document).ready(function () {

    jQuery.validator.addMethod(
        "regex",
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(jQuery.trim(value));
        },
        "Invalid input pattern"
    );

    $('#mci_bundle_patientBundle_patients_present_address_division_id').on('change', function () {
        var x = $('#mci_bundle_patientBundle_patients_present_address_district_id');
        var y = $('#mci_bundle_patientBundle_patients_present_address_upazila_id');
        var divisionId = $('option:selected', this).val();
        populatedDistrictDropdown(x, y,divisionId);
    });

    $('#mci_bundle_patientBundle_patients_permanent_address_division_id').on('change', function () {
        var x = $('#mci_bundle_patientBundle_patients_permanent_address_district_id');
        var y = $('#mci_bundle_patientBundle_patients_permanent_address_upazila_id');
        var divisionId = $('option:selected', this).val();
        populatedDistrictDropdown(x, y,divisionId);
    });

    $('#mci_bundle_patientBundle_patients_present_address_district_id').on('change', function () {
        var x = $('#mci_bundle_patientBundle_patients_present_address_upazila_id');
        var districtId = $('option:selected', this).val();
        upazilaDropdwon(x,districtId);
    });

    $('#mci_bundle_patientBundle_patients_permanent_address_district_id').on('change', function () {
        var x = $('#mci_bundle_patientBundle_patients_permanent_address_upazila_id');
        var districtId = $('option:selected', this).val();
        upazilaDropdwon(x,districtId);
    });

    $('#division').on('change', function () {
        var x = $('#district');
        var locationCode = $('option:selected', this).val();
        populatedDropdown(x,locationCode,'divisionloader');
    });

    $('#district').on('change', function () {
        var x = $('#upazila');
        var districtId = $('option:selected', this).val();
        var divisionId = $('option:selected', '#division').val();
        if(districtId){
            populatedDropdown(x,divisionId+districtId,'districtloader');
        }
    });

    $('#upazila').on('change', function () {
        var x = $('#citycorporation');
        var upazilaId = $('option:selected', this).val();
        var divisionId = $('option:selected', '#division').val();
        var districtId = $('option:selected', '#district').val();
        if(upazilaId) {
            populatedDropdown(x, divisionId + districtId + upazilaId,'upazilaloader');
        }
    });

    $('#citycorporation').on('change', function () {
        var x = $('#union');
        var citycorporationId= $('option:selected', this).val();
        var divisionId = $('option:selected', '#division').val();
        var districtId = $('option:selected', '#district').val();
        var upazilaId = $('option:selected', '#upazila').val();
        if(citycorporationId) {
            populatedDropdown(x, divisionId + districtId + upazilaId + citycorporationId,'citycorporationloader');
        }
    });

    $('#union').on('change', function () {
        var x = $('#ward');
        var unionId= $('option:selected', this).val();
        var divisionId = $('option:selected', '#division').val();
        var districtId = $('option:selected', '#district').val();
        var upazilaId = $('option:selected', '#upazila').val();
        var citycorporationId = $('option:selected', '#citycorporation').val();
        if(unionId){
            populatedDropdown(x,divisionId+districtId+upazilaId+citycorporationId+unionId,'unionloader');
        }
    });

});

 function populatedDropdown(selectcontainer,locationCode,loader) {

        if (locationCode) {
            selectcontainer.empty();
            $.ajax({
                type: "POST",
                url: "/location/" + locationCode,
                beforeSend: function () {
                    $('.'+loader).show();
                },
                success: function (result) {
                    var options = generatedOptions(result);
                    selectcontainer.append(options);
                },
                complete: function () {
                    $('.'+loader).hide();
                }
            });
        }
    }

  function generatedOptions(result) {
        var obj = $.parseJSON(result);
        var options = "<option value=''>-Please Select-</option>";
        for (var i in obj) {
            options += '<option   value="' + obj[i]['code'] + '">' + obj[i]['name'] + '</option>';
        }
        return options;
    }


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