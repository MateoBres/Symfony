import $ from "jquery";
import Routing from "./sv_router";

export default function _geoCodeAddressComponentCompleter(field, address_str) {
    $.ajax({
        url: Routing.generate('utility_geocode'),
        dataType: 'json',
        data: {'fullAddress': address_str},
        success: function (address) {
            field.closest('.form-group').siblings('.form-group').each(function (e) {
                let $input = $(this).find('input');
                if ($input.attr('geo-name') != undefined) {
                    $input.val(address[$input.attr('geo-name')]);
                }
            });
        }
    });
}