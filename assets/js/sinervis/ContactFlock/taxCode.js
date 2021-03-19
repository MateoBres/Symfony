import $ from 'jquery';
import 'autocomplete.js/dist/autocomplete.jquery';
import swal from 'sweetalert2';
import Routing from '../components/sv_router';


let cityField = 'input.cf-city';
let provinceField = 'input.cf-province';
let taxCodeField = 'input.tax-code';

$(document).ready(function () {
    /* check if tax code field is present. */

    $(taxCodeField+':not(.processed)').each(function() {
        $(this).addClass('processed');
        var $wrapper = $(this).closest('div.panel-body');
        var params = getParamsForTaxCodeCalculation($wrapper);
        areAllParamsProvided($wrapper, params);

        listenToTaxcodeRelatedFieldChanges();
        applyAutocompleteToCityField();
    });
});

export default function applyAutocompleteToCityField() {
    var $cfCityField = $(cityField);
    let routeName = $cfCityField.data('autocomplete-route');

    if (routeName) {
        var autocompleteUrl = Routing.generate(routeName)

        $cfCityField.autocomplete({hint: false, debug: true}, [
            {
                source: function (query, cb) {
                    $.ajax({
                        dataType: 'json',
                        url: autocompleteUrl + '?term=' + query,
                        beforeSend: function () {
                            $cfCityField.addClass('ui-autocomplete-loading')
                        }
                    }).then(function (data) {
                        $cfCityField.removeClass('ui-autocomplete-loading')

                        cb(data)
                    })
                },
                debounce: 500,
                // displayKey: 'value',
            }
        ]).on('autocomplete:selected', function (event, suggestion, dataset, context) {
            $(provinceField).val('');
            $(provinceField).closest('label').removeClass('state-error');

            if (suggestion.country === 'Italia') {
                $cfCityField.val(suggestion.city)
            } else {
                $cfCityField.val('')
            }
            _normalizeCityAndProvince(suggestion.value);
        });
    }
}


function _normalizeCityAndProvince(cityName) {
    $.ajax({
        url: Routing.generate('utility_geocode'),
        dataType: 'json',
        data: {'fullAddress': cityName},
        success: function (address) {
            if (address) {
                $(cityField).trigger('blur')
                if (address.country == 'Italia') {
                    $(cityField).val(address.locality);
                    $(provinceField).val(address.administrative_area_level_2);
                }
                else {
                    $(cityField).val(address.country);
                    $(provinceField).val('estero');
                }

                $(cityField).trigger('change');
            }
        }
    });
}


function listenToTaxcodeRelatedFieldChanges() {
    $(document).on('change', 'input.cfiscale, div.cf-gender', function (e) {
        e.stopImmediatePropagation();
        var $wrapper = $(this).closest('div.panel-body');
        var params = getParamsForTaxCodeCalculation($wrapper);
        var areParamsReady = areAllParamsProvided($wrapper, params);

        if (areParamsReady) {
            $.ajax({
                type: "POST",
                url: Routing.generate('utility_get_tax_code'),
                data: {'tax_code_params': params},
                dataType: 'json',
                success: function (tax_code) {
                    if (tax_code.errors === undefined) {
                        $wrapper.find(taxCodeField).val(tax_code);
                        $wrapper.find(provinceField).closest('label').removeClass('state-error');
                    }
                    else {
                        $.each(tax_code.errors, function (index, value) {
                            swal.fire({
                                title: value,
                                text: "Per Italia aggiungere solo due lettere e per altri paesi aggiungere 'estero'!!",
                                type: "warning",
                            });

                            if (value == 'La sigla della provincia di nascita è invalida') {
                                $(provinceField).closest('label').addClass('state-error');
                            }
                        });
                    }
                },
            });
        }
    });
}

function getParamsForTaxCodeCalculation($wrapper) {
    return {
        'name': $wrapper.find('input.cf-firstname').val(),
        'surname': $wrapper.find('input.cf-lastname').val(),
        'dob': $wrapper.find('input.cf-dob').val(),
        'gender': $wrapper.find('div.cf-gender input:checked').val(),
        'city': $wrapper.find(cityField).val(),
        'province': $wrapper.find(provinceField).val(),
    };
}

function areAllParamsProvided($wrapper, params) {
    var paramsReady = true;
    var progress = 0;

    $.each(params, function (index, value) {
        if (value == undefined || value.length == 0) {
            paramsReady = false;
        }
        else {
            progress += 1;
        }
    });

    $wrapper.find('div.cf.progress div.progress-bar').css({
        'width': (100 / 6) * progress + '%',
        'background-color': progress == 6 ? '#45D845' : '#FF3D3D'
    });

    return paramsReady;
}
