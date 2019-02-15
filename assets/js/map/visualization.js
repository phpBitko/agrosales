$(document).ready(function()  {
    //-------------------------------------------- ініціалізуєм bootstrap tooltip і змінюєм налаштування
    $('[data-toggle="tooltip"]').tooltip({
        trigger : 'hover'
    });

    $('.choose-layer li').on('click', function () {
        $('.choose-layer li').removeClass('active');
        $(this).addClass('active');
        var selected = $(this).attr('data-val');

        var artbaz = ['pub', 'osm', 'OpenCycleMap', 'google', 'googlehybrid', 'kiev2006', 'emptyRelief', 'emptyLayer', 'topoUA', 'tileLayer', 'bing'];
        mapSales.getLayers().forEach(function (l) {

            if (($.inArray(l.get('name'), artbaz)) > -1) {

                if (l.get('name') !== selected) {
                    l.setVisible(false);

                } else {
                    l.setVisible(true);
                }
            }
        });
    });


    $('#control-panel-layer').on('click', function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $('.choose-layer').removeClass('choose-layer-move-in');
            $('.choose-layer').addClass('choose-layer-move-out');
            $('.map-details-info').removeClass('move-position');


        } else {
            $(this).addClass('active');
            $('.choose-layer').addClass('choose-layer-move-in');
            $('.choose-layer').removeClass('choose-layer-move-out');
            $('.map-details-info').addClass('move-position');
        }

    });


    $('.choose-head .close').on('click', function () {
        $('.choose-layer').removeClass('choose-layer-move-in');
        $('.choose-layer').addClass('choose-layer-move-out');
        $('#control-panel-layer').removeClass('active');
        $('.map-details-info').removeClass('move-position');
    });


        $('.map-filter').addClass('fixed');


    //-----------------------------------звернення до об'єтка якого не було в DOM

    $('body').on('click', '.map-details-info .close', function () {
        $('.map-details-info').addClass('hidden');


    });

    $('body').on('click', '.map-details-info .move-right', function () {
        $('.map-details-info').removeAttr("style");
        $('.move-right').addClass('hidden');
     });

    $('.carousel').carousel({
        interval: false,
    });

})