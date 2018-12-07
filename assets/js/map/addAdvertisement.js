import WKT from 'ol/format/WKT.js';
import {Vector as VectorSource} from 'ol/source.js';
import Icon from "ol/style/Icon";
import {Style} from "ol/style";
import {Vector as VectorLayer} from "ol/layer";
// import {Circle as CircleStyle, Fill, Stroke, Style} from 'ol/style.js';

$(document).ready(function () {


    //-------------------------------------------for points from advertisement
    var features;
    var format = new WKT();
    var vectorSourcePoints = new VectorSource;


    //-------------------------------------------створюєм як буде виглядать іконка для відобреження ділянок(оголошень)
    var svg = $('#icon_advertisement');
    var styleAdvert = new Style({
        image: new Icon({
            src: 'data:image/svg+xml;utf8,' + svg[0].outerHTML,
        })
    });


    //----------------переберає отримані дані з контролєра і додає в обєкт Sourse
    function addAdvertLayers(data) {
        $.each(data.data, function (i, value) {
            features = format.readFeature(value.geom, {
                dataProjection: 'EPSG:3857',
                featureProjection: 'EPSG:3857'
            });

            features.set('id', value.id);
            vectorSourcePoints.addFeature(features);
        });
    }


    /*
        $('body').load(function () {
            $('body').preloader('remove');
        });
    */


    $('.map-details-info').draggable({
        stop: function() {
            $('.move-left').removeClass('hidden');
        }

    });


    //-----------------------------------заповнює значеннями форму

    function addMapDetails(data) {
        // $('body').preloader('remove');
        $('.map-details-info').html(data.table.details);
        if ($('.map-details-info').hasClass('hidden')) {
            $('.map-details-info').removeClass('hidden')
        }


        /*        var item = data.data[0];
                if (item.area) {
                    $('.map-main-area').html(item.area + ' соток');
                } else {
                    $('.map-main-area').html('площа не вказана')
                }
                if (item.price) {
                    $('.map-main-price').html(item.price + '$');
                } else {
                    $('.map-main-price').html('')
                }
                if (item.area != 0 && item.price != 0) {
                    var value = (Math.round((item.price / item.area) * 100) / 100);
                    $('.map-main-price-to-area').html('| ' + value + '$/сотку');

                } else {
                    $('.map-main-price-to-area').html('')
                }

                var itemFirst = $('.details-road').children().first();
                var itemLast = $('.details-road').children().last();


                if (item.isRoad == false) {

                    if (itemLast.hasClass('hidden')) {
                        itemLast.removeClass('hidden');
                        itemFirst.addClass('hidden');
                        $('.details-road').removeClass('active')
                    }
                } else {

                    if (itemFirst.hasClass('hidden')) {
                        itemFirst.removeClass('hidden');
                        itemLast.addClass('hidden');
                        $('.details-road').addClass('active')
                    }

                }*/

    }

    //--- запит для отримання даних про координати точок, використувується бандл FOSjsroutingbundle
    function getAllAdvertisement() {
        $.ajax({
            url: Routing.generate('get_all_advertisement'),
            dataType: 'json',
            method: 'POST',
            success: function (data) {
                $('body').preloader('remove');
                addAdvertLayers(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('body').preloader('remove');
                $('html, body').css("cursor", "auto");
                if (jqXHR.responseJSON) {
                    bootbox.alert({
                        title: 'Виникла помилка',
                        message: jqXHR.responseJSON.error
                    });
                } else {
                    bootbox.alert({
                        title: 'Виникла помилка',
                        message: jqXHR.responseText
                    });
                }
            },
        });
    }

    //--------------------------------------------при клікі на карті вираховує чи є іконка і віддає id

    mapSales.on('click', function (evt) {

        var feature = mapSales.forEachFeatureAtPixel(evt.pixel,
            function (feature) {
                return feature;
            });
        console.log(evt.pixel);
        if (feature) {
            //$('body').preloader();
            var idAdvertisement = feature.getProperties();
            getDetailsAdvertisement(idAdvertisement.id);
        }
    });

    //-----------------------------------------------змінює курсор на pointer при наведенні на іконку

    mapSales.on("pointermove", function (evt) {
        var hit = this.forEachFeatureAtPixel(evt.pixel, function (feature, layer) {
            return true;
        });
        //console.log(hit);
        if (hit) {
            $('#map').addClass('cursor-pointer');

        } else {
            $('#map').removeClass('cursor-pointer');
        }
    });


    //--- запит для отримання даних про детальну інформацію про ділянку бандл FOSjsroutingbundle
    function getDetailsAdvertisement(id) {
        $.ajax({
            url: Routing.generate('map_details_advertisement'),
            dataType: 'json',
            data: {id: id},
            method: 'POST',
            success: function (data) {
                addMapDetails(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('body').preloader('remove');
                $('html, body').css("cursor", "auto");
                if (jqXHR.responseJSON) {
                    bootbox.alert({
                        title: 'Виникла помилка',
                        message: jqXHR.responseJSON.error
                    });
                } else {
                    bootbox.alert({
                        title: 'Виникла помилка',
                        message: jqXHR.responseText
                    });
                }
            },
        });
    }

    getAllAdvertisement();

    var vectorPoints = new VectorLayer({
        source: vectorSourcePoints,
        style: styleAdvert
    });
    mapSales.addLayer(vectorPoints);

})

