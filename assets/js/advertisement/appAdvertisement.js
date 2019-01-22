$(function () {

    $(function () {
        $('[data-toggle="popover"]').popover()
    });
    $('[data-toggle="tooltip"]').tooltip({
        //Краще убрать ці затримки
        delay: {show: 100, hide: 100},
    });
    $('#myModal').on('shown.bs.modal', function () {
        $('#myInput').trigger('focus')
    });
    $('.carousel').carousel({
        interval: false,
    });
    $('.datepicker').datepicker({
        clearBtn: true,
        language: 'uk',
    });
});