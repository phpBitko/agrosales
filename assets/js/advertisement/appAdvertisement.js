$(function () {

    $(function () {
        $('[data-toggle="popover"]').popover()
    });
    $('[data-toggle="tooltip"]').tooltip({
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