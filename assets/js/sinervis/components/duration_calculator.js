
export default function durationCalculator($items, attrib, returnType) {
    let tot_hours = 0;
    let tot_mins = 0;
    $items.each(function () {
        var value = attrib ? $(this).attr(attrib) : $(this).val();
        if (isDurationValid(value)) {
            let elements = value.split(':');

            tot_mins += parseInt(elements[0]) * 60;
            tot_mins += parseInt(elements[1]);
        }
    })

    return getFormattedHourMin(tot_mins, returnType)
}

export function getFormattedHourMin(mins, returnType) {
    let tot_hours = Math.floor(mins / 60);
    let tot_mins = mins - (tot_hours * 60);

    if (returnType == 'number') {
        return tot_hours + (tot_mins / 60);
    }
    else {
        tot_hours = tot_hours < 10 ? '0' + tot_hours : tot_hours;
        tot_mins = tot_mins < 10 ? '0' + tot_mins : tot_mins;
        return tot_hours + ':' + tot_mins;
    }
}

export function isDurationValid(duration, nonZero) {
    duration = $.trim(duration);
    if (duration) {
        if (duration.indexOf(':') < 0) {
            return false;
        }

        var elements = duration.split(':');
        if (!$.trim(elements[0]) || !$.trim(elements[1])) {
            return false;
        }

        if (parseInt(elements[0]) != $.trim(elements[0]) || parseInt(elements[1]) != $.trim(elements[1])) {
            return false;
        }

        var mins = parseInt(elements[1]);
        if (mins > 59 || elements[1].length != 2) {
            return false;
        }

        if (nonZero == true && parseInt(elements[0]) == 0 && parseInt(elements[1]) == 0) {
            return false;
        }

        return true;
    }

    return false;
}

export function getMinutes(value) {
    if ($.trim(value) && isDurationValid(value)) {
        var elements = value.split(':');
        var tot_mins = parseInt(elements[0]) * 60;
        tot_mins += parseInt(elements[1]);

        return tot_mins;
    }

    return 0;
}