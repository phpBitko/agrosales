$(function () {

    $(function () {
        $('[data-toggle="popover"]').popover()
    });
    $('[data-toggle="tooltip"]').tooltip({
        placement: 'bottom',
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

    //------------------------------ ініціалізуєм слайдер Ціна

    $('#slider-price').slider({
        range: true,
        min: 0,
        max: 1000000,
        ticks: [0, 5000, 20000, 100000, 1000000],
        ticks_positions: [0, 25, 50, 75, 100],
        ticks_snap_bounds: 500,
        ticks_labels: ['0', '5000', '20 000', '100 тис.', '1 млн.'],
        step: 100,
        lock_to_ticks: false,
        reversed: false,
        tooltip: 'hide',
    });

    //------------------------------ ініціалізуєм слайдер Площа

    $('#slider-area').slider({
        range: true,
        min: 0,
        max: 1000,
        ticks: [0, 10, 100, 1000],
        ticks_positions: [0, 33, 66, 100],
        ticks_snap_bounds: 1,
        ticks_labels: ['0', '10 соток', '1 га', '10 га'],
        step: 1,
        lock_to_ticks: false,
        reversed: false,
        tooltip: 'hide',
    });
});