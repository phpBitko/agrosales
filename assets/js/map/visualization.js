$(document).ready(function()  {
    //-------------------------------------------- ініціалізуєм bootstrap tooltip і змінюєм налаштування
    $('[data-toggle="tooltip"]').tooltip({
        delay: {show: 100, hide: 100},
    });

    $('.choose-layer li').on('click', function () {
        $('.choose-layer li').removeClass('active');
        $(this).addClass('active');
        var selected = $(this).attr('data-val');
        //alert(selected);
        var artbaz = ['pub', 'osm', 'OpenCycleMap', 'google', 'googlehybrid', 'kiev2006', 'emptyRelief', 'emptyLayer', 'topoUA', 'tileLayer', 'bing'];
        mapSales.getLayers().forEach(function (l) {
            //alert(l.get('name'))

            if (($.inArray(l.get('name'), artbaz)) > -1) {

                if (l.get('name') !== selected) {
                    l.setVisible(false);

                } else {
                    //     if (l.get('name') == 'OpenCycleMap' || l.get('name') == 'osm') {
                    //         $('.osm-copyright').show();
                    //     } else {
                    //         $('.osm-copyright').hide();
                    //     }
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

        } else {
            $(this).addClass('active');
            $('.choose-layer').addClass('choose-layer-move-in');
            $('.choose-layer').removeClass('choose-layer-move-out');
        }
    });

    $('.choose-head .close').on('click', function () {
        $('.choose-layer').removeClass('choose-layer-move-in');
        $('.choose-layer').addClass('choose-layer-move-out');

        $('#control-panel-layer').removeClass('active');
    });

    //-----------------------------------звернення до об'єтка якого не було в DOM

    $('body').on('click', '.map-details-info .close', function () {
        $('.map-details-info').addClass('hidden');

    });
    $('body').on('click', '.map-details-info .move-left', function () {
        $('.map-details-info').removeAttr("style");
        $('.move-left').addClass('hidden');
     });

    $('.carousel').carousel({
        interval: false,
    });
/*

    $('.map-provision').load(function () {
        $('[data-toggle="tooltip"]').tooltip({
            delay: {show: 100, hide: 100},
        });

    });*/




})