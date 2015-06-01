
jQuery(document).ready(function () {

    jQuery.validator.addMethod(
        "regex",
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(jQuery.trim(value));
        },
        "Please enter data in valid format"
    );
    var divisionSelector = '#mci_bundle_patientBundle_patients_present_address_division_id';
    var districtSelector = '#mci_bundle_patientBundle_patients_present_address_district_id';
    var upazilaSelector = '#mci_bundle_patientBundle_patients_present_address_upazila_id';
    var citycorporationSelector = '#mci_bundle_patientBundle_patients_present_address_city_corporation_id';
    var unionSelector = '#mci_bundle_patientBundle_patients_present_address_union_or_urban_ward_id';
    var wardSelector = '#mci_bundle_patientBundle_patients_present_address_rural_ward_id';
    var $divisionEl = $(divisionSelector);
    var $districtEl = $(districtSelector);
    var $upazilaEl = $(upazilaSelector);
    var $citycorporationEl = $(citycorporationSelector);
    var $unionEl =  $(unionSelector);
    var $wardEl =  $(wardSelector);

    var emptyOptionDistrict = "<option value=''>Select District</option>";
    var emptyOptionUpazila = "<option value=''>Select Upazila</option>";
    var emptyOptionCityCorporation = "<option value=''>Select City Corporation</option>";
    var emptyOptionUnion = "<option value=''>Select Union Or Urban Ward</option>";
    var emptyOptionWard = "<option value=''>Select Rural Ward</option>";

    $divisionEl.on('change', function () {
        var divisionId = $('option:selected', this).val();
        $upazilaEl.html(emptyOptionUpazila);
        $citycorporationEl.html(emptyOptionCityCorporation);
        $unionEl.html(emptyOptionUnion);
        $wardEl.html(emptyOptionWard);
        if(!divisionId){
            $districtEl.html(emptyOptionDistrict);
        }
        populatedDropdown($districtEl, divisionId);
    });

    $districtEl.on('change', function () {
        var districtId = $('option:selected', this).val();
        $citycorporationEl.html(emptyOptionCityCorporation);
        $unionEl.html(emptyOptionUnion);
        $wardEl.html(emptyOptionWard);
        var $divisionPeresentSelectEl = $('option:selected', divisionSelector);
        var divisionId = $divisionPeresentSelectEl.val();
        if(!districtId){
            $upazilaEl.html(emptyOptionUpazila);
        }
        populatedDropdown($upazilaEl,divisionId+districtId);
    });

    $upazilaEl.on('change', function () {
        var upazilaId = $('option:selected', this).val();
        $unionEl.html(emptyOptionUnion);
        $wardEl.html(emptyOptionWard);
        var $districtPresentSelectEl = $('option:selected', districtSelector);
        var $divisionPeresentSelectEl = $('option:selected', divisionSelector);
        var divisionId = $divisionPeresentSelectEl.val();
        var districtId = $districtPresentSelectEl.val();
        if(!upazilaId){
            $citycorporationEl.html(emptyOptionCityCorporation);
        }
        populatedDropdown($citycorporationEl,divisionId+districtId+upazilaId);
    });

    $citycorporationEl.on('change', function () {
        var cityCorportationId = $('option:selected', this).val();
        $wardEl.html(emptyOptionWard);
        var $districtPresentSelectEl = $('option:selected', districtSelector);
        var $upazilaPresentSelectEl = $('option:selected', upazilaSelector);
        var $divisionPeresentSelectEl = $('option:selected', divisionSelector);
        var divisionId = $divisionPeresentSelectEl.val();
        var districtId = $districtPresentSelectEl.val();
        var upazilaId = $upazilaPresentSelectEl.val();
        if(!cityCorportationId){
            $unionEl.html(emptyOptionUnion);
        }

        if(cityCorportationId && divisionId && districtId && upazilaId ){
            populatedDropdown($unionEl,divisionId+districtId+upazilaId+cityCorportationId);
        }
    });

    $unionEl.on('change', function () {
        var unionId = $('option:selected', this).val();
        var $districtPresentSelectEl = $('option:selected', districtSelector);
        var $upazilaPresentSelectEl = $('option:selected', upazilaSelector);
        var $cityCorpPresentSelectEl = $('option:selected', citycorporationSelector);
        var $divisionPeresentSelectEl = $('option:selected', divisionSelector);
        var divisionId = $divisionPeresentSelectEl.val();
        var districtId = $districtPresentSelectEl.val();
        var upazilaId = $upazilaPresentSelectEl.val();
        var cityCorportationId = $cityCorpPresentSelectEl.val();
        if(unionId && cityCorportationId && upazilaId && districtId && divisionId ){
            populatedDropdown($wardEl,divisionId+districtId+upazilaId+cityCorportationId+unionId);
        }
    });

    var divisionPermaSelector = '#mci_bundle_patientBundle_patients_permanent_address_division_id';
    var districtPermaSelector = '#mci_bundle_patientBundle_patients_permanent_address_district_id';
    var upazilaPermaSelector = '#mci_bundle_patientBundle_patients_permanent_address_upazila_id';
    var cityCorpPermaSelector = '#mci_bundle_patientBundle_patients_permanent_address_city_corporation_id';
    var unionPermaSelector = '#mci_bundle_patientBundle_patients_permanent_address_union_or_urban_ward_id';
    var wardPermaSelector = '#mci_bundle_patientBundle_patients_permanent_address_rural_ward_id';
    var $divisionPemanentEl = $(divisionPermaSelector);
    var $districtPermanentEl = $(districtPermaSelector);
    var $upazilaPermanentEl = $(upazilaPermaSelector);
    var $cityCorpPermanentEl =  $(cityCorpPermaSelector);
    var $unionPermanentEl =  $(unionPermaSelector);
    var $wardPermanentEl =  $(wardPermaSelector);

    $divisionPemanentEl.on('change', function () {
        var divisionId = $('option:selected', this).val();
        $upazilaPermanentEl.html(emptyOptionUpazila);
        $cityCorpPermanentEl.html(emptyOptionCityCorporation);
        $unionPermanentEl.html(emptyOptionUnion);
        $wardPermanentEl.html(emptyOptionWard);
        if(!divisionId){
            $districtPermanentEl.html(emptyOptionDistrict);
        }
        populatedDropdown($districtPermanentEl, divisionId);
    });

    $districtPermanentEl.on('change', function () {
        var districtId = $('option:selected', this).val();
        $cityCorpPermanentEl.html(emptyOptionCityCorporation);
        $unionPermanentEl.html(emptyOptionUnion);
        $wardPermanentEl.html(emptyOptionWard);
        var $divisionPermanetSelectEl = $('option:selected', divisionPermaSelector);
        var divisionId = $divisionPermanetSelectEl.val();
        if(!districtId){
            $upazilaPermanentEl.html(emptyOptionUpazila);
        }
        populatedDropdown($upazilaPermanentEl,divisionId+districtId);
    });

    $upazilaPermanentEl.on('change', function () {
        var upazilaId = $('option:selected', this).val();
        $unionPermanentEl.html(emptyOptionUnion);
        $wardPermanentEl.html(emptyOptionWard);
        var $divisionPermanetSelectEl = $('option:selected', divisionPermaSelector);
        var $districtPermanentSelectEl = $('option:selected', districtPermaSelector);
        var divisionId = $divisionPermanetSelectEl.val();
        var districtId = $districtPermanentSelectEl.val();
        if(!upazilaId){
            $cityCorpPermanentEl.html(emptyOptionCityCorporation);
        }
        populatedDropdown($cityCorpPermanentEl,divisionId+districtId+upazilaId);
    });

    $cityCorpPermanentEl.on('change', function () {
        var cityCorportationId = $('option:selected', this).val();
        $wardPermanentEl.html(emptyOptionWard);
        var $divisionPermanetSelectEl = $('option:selected', divisionPermaSelector);
        var $districtPermanentSelectEl = $('option:selected', districtPermaSelector);
        var $upazilaPermanentSelectEl = $('option:selected', upazilaPermaSelector);
        var divisionId = $divisionPermanetSelectEl.val();
        var districtId = $districtPermanentSelectEl.val();
        var upazilaId = $upazilaPermanentSelectEl.val();
        if(!cityCorportationId){
            $unionPermanentEl.html(emptyOptionUnion);
        }
        if(cityCorportationId && divisionId && districtId && upazilaId  ){
            populatedDropdown($unionPermanentEl,divisionId+districtId+upazilaId+cityCorportationId);
        }
    });

    $unionPermanentEl.on('change', function () {
        var $divisionPermanetSelectEl = $('option:selected', divisionPermaSelector);
        var $districtPermanentSelectEl = $('option:selected', districtPermaSelector);
        var $upazilaPermanentSelectEl = $('option:selected', upazilaPermaSelector);
        var $cityCorpPermaSelectEl = $('option:selected', cityCorpPermaSelector);
        var divisionId = $divisionPermanetSelectEl.val();
        var districtId = $districtPermanentSelectEl.val();
        var upazilaId = $upazilaPermanentSelectEl.val();
        var cityCorportationId = $cityCorpPermaSelectEl.val();
        var unionId = $('option:selected', this).val();
        if(unionId && cityCorportationId && upazilaId && districtId && divisionId){
            populatedDropdown($wardPermanentEl,divisionId+districtId+upazilaId+cityCorportationId+unionId);
        }
    });

    $('#division').on('change', function () {
        var x = $('#district');
        $('#upazila').html(emptyOptionUpazila);
        $('#union').html(emptyOptionUnion);
        $('#citycorporation').html(emptyOptionCityCorporation);
        $('#ward').html(emptyOptionWard);
        var locationCode = $('option:selected', this).val();
        if(!locationCode){
            x.html(emptyOptionDistrict);
        }
        populatedDropdown(x,locationCode,'divisionloader');
    });

    $('#district').on('change', function () {
        var x = $('#upazila');
        $('#union').html(emptyOptionUnion);
        $('#citycorporation').html(emptyOptionCityCorporation);
        $('#ward').html(emptyOptionWard);
        var districtId = $('option:selected', this).val();
        var divisionId = $('option:selected', '#division').val();
        if(!districtId){
            x.html(emptyOptionUpazila);
        }
        if(districtId && divisionId){
            populatedDropdown(x,divisionId+districtId,'districtloader');
        }
    });

    $('#upazila').on('change', function () {
        var x = $('#citycorporation');
        $('#union').html(emptyOptionUnion);
        $('#ward').html(emptyOptionWard);
        var upazilaId = $('option:selected', this).val();
        var divisionId = $('option:selected', '#division').val();
        var districtId = $('option:selected', '#district').val();
        if(!upazilaId){
            x.html(emptyOptionCityCorporation);
        }

        if(upazilaId && divisionId && districtId ) {
            populatedDropdown(x, divisionId + districtId + upazilaId,'upazilaloader');
        }
    });

    $('#citycorporation').on('change', function () {
        var x = $('#union');
        $('#ward').html(emptyOptionWard);
        var citycorporationId= $('option:selected', this).val();
        var divisionId = $('option:selected', '#division').val();
        var districtId = $('option:selected', '#district').val();
        var upazilaId = $('option:selected', '#upazila').val();
        if(!citycorporationId){
            x.html(emptyOptionUnion);
        }
        if(citycorporationId && divisionId && districtId && upazilaId ) {
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
        if(unionId && divisionId && districtId && upazilaId && citycorporationId ){
            populatedDropdown(x,divisionId+districtId+upazilaId+citycorporationId+unionId,'unionloader');
        }
    });
});

 function populatedDropdown(selectcontainer,locationCode,loader) {

        if (locationCode) {
            selectcontainer.empty();
            $.ajax({
                type: "POST",
                dataType: 'json',
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
                },
                error: function(jqXHR, exception) {
                    if (jqXHR.status === 0) {
                        alert('Not connect.\n Verify Network.');
                    } else if (jqXHR.status == 404) {
                        alert('Requested page not found. [404]');
                    } else if (jqXHR.status == 500) {
                        alert('Internal Server Error [500].');
                    } else if (exception === 'parsererror') {
                        alert('Requested JSON parse failed.');
                    } else if (exception === 'timeout') {
                        alert('Time out error.');
                    } else if (exception === 'abort') {
                        alert('Ajax request aborted.');
                    } else {
                        alert('Uncaught Error.\n' + jqXHR.responseText);
                    }
                }
            });
        }
    }

  function generatedOptions(obj) {
        var options = "<option value=''>-Please Select-</option>";
        for (var i in obj) {
            options += '<option   value="' + obj[i]['code'] + '">' + obj[i]['name'].toLowerCase().capitalize() + '</option>';
        }
        return options;
    }

String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
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
