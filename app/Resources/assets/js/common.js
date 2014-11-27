
jQuery(document).ready(function () {
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



