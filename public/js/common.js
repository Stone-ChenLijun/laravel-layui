function getValidationMsg(errors) {
    let msg = '';
    for (let i in errors) {
        msg += errors[i].join('') + '</br>';
    }
    return msg;
}

function getWeekdayName(weekday) {
    switch (weekday) {
        case '0':
            return '周日';
        case '1':
            return '周一';
        case '2':
            return '周二';
        case '3':
            return '周三';
        case '4':
            return '周四';
        case '5':
            return '周五';
        case '6':
            return '周六';
    }
}
