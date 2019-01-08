//data повинна містити поля message і status {'message':'text', 'status': int}
module.exports = function (message, status = 2) {
    try {
        var classHeader = 'errorBootboxHead';
        var titleHeader = 'Виникла помилка!';

        if (status == 0) {
            classHeader = 'okBootboxHead';
            titleHeader = 'Повідомлення!';

        } else if (status == 1) {
            classHeader = 'warningBootboxHead';
            titleHeader = 'Попередження!';

        } else if (status == 2) {
            classHeader = 'errorBootboxHead';
            titleHeader = 'Виникла помилка!';
        }

        bootbox.alert({
            title: titleHeader,
            message: message
        });

        $('.bootbox').find('.modal-header').addClass(classHeader);

    } catch (e) {
        alert(e.message);
    }
};