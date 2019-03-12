$(document).ready(function () {
    //-------------------------------------------- ініціалізуєм bootstrap tooltip і змінюєм налаштування
/*    $('[data-toggle="tooltip"]').tooltip({
        trigger: 'hover',
        container: 'body',
        placement: 'bottom',
    });*/
/*    $('.advertisement-filter').tooltip({
        trigger: 'hover',
        selector: '[data-toggle="tooltip"]',
        placement: 'bottom',
    });*/

    $('.map-details-info, .advertisement-filter').tooltip({
        trigger: 'hover',
        selector: '[data-toggle="tooltip"]',
        placement: 'bottom',
    });

    $('.control-panel, .zoom-layer').tooltip({
        trigger: 'hover',
        selector: '[data-toggle="tooltip"]',
        placement: 'left',
    });

    //Перемикаємо шари
    $('.choose-layer li:not(#pubMap)').on('click', function () {
        $('.choose-layer li').removeClass('active');
        $(this).addClass('active');
        var selected = $(this).attr('data-val');
        mapSales.getLayers().forEach(function (l) {
            if(!(l instanceof VectorLayer) && l.get('name') != 'pub'){
                l.setVisible(false);
                if (l.get('name') === selected) {
                    l.setVisible(true);
                }
            }
        });
    });

    //Перемикаємо ПКК
    $('#checkPub').on('click', function (e) {
        var layer = getLayerByName('pub', mapSales);
        if($('#checkPub').prop('checked') === false){
            layer.setVisible(false);
        }else{
            layer.setVisible(true);
        }
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

    $('.map-filter').removeClass('hidden');
    $('.map-filter').addClass('fixed');


    //-----------------------------------звернення до об'єтка якого не було в DOM

    $('body').on('click', '.map-details-info .close', function () {
        $('.map-details-info').addClass('hidden');
        $('.map-details-info').removeAttr("style");

    });

    /*    $('body').on('mouseover', function () {

            $('[data-toggle="tooltip"]').tooltip({
                trigger: 'hover',
            });
        });*/
    /*
        $('body').on('load', '.map-details-info', function () {
            $('[data-toggle="tooltip"]').tooltip("show");

        });*/

    $('body').on('click', '.map-details-info .move-right', function () {
        $('.map-details-info').removeAttr("style");
        $('.move-right').addClass('hidden-ico');
        $('.map-details-info').removeClass('transition-non');

    });

    $('.carousel').carousel({
        interval: false,
    });

})