import WKT from 'ol/format/WKT';
import {Vector as VectorSource} from 'ol/source.js';
import Icon from "ol/style/Icon";
import {Circle as CircleStyle, Fill, Stroke} from "ol/style";
import {Vector as VectorLayer} from "ol/layer";
import {fromLonLat} from "ol/proj";
import View from "ol/View";
import Style from "ol/style/Style";

// import {Circle as CircleStyle, Fill, Stroke, Style} from 'ol/style.js';

$(document).ready(function () {

    //-------------------------------------------for points from advertisement
    var features;
    var format = new WKT();
    var vectorSourcePoints = new VectorSource;


    var centerUkraine = fromLonLat([31.182233, 48.382778]);//------------------координати центру України

    var view = new View({
        center: centerUkraine,
        zoom: 5
    });

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

    $('.map-details-info').draggable({
        stop: function () {
            $('.move-left').removeClass('hidden');
        }
    });


    //-----------------------------------відображаєм форму з детальною інформацією

    function addMapDetails(data) {
        $('.map-details-info').html(data.table.details);
        if ($('.map-details-info').hasClass('hidden')) {
            $('.map-details-info').removeClass('hidden')
        }
    }

    //--- запит для отримання даних про координати точок, використувується бандл FOSjsroutingbundle
    function getAllAdvertisement() {
        $.ajax({
            url: Routing.generate('get_advertisement_by_status'),
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
        if (feature) {
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

    if ($('.map-details-head').length) {
        if ($('.map-details-info').hasClass('hidden')) {
            $('.map-details-info').removeClass('hidden')
        }
        if ($('#map-properties-geom').text() !== undefined && $('#map-properties-geom').text() !== '') {
            // var wkt = new WKT();
            // var view = new View;
            var style = new Style({
                fill: new Fill({
                    color: 'rgba(255, 255, 255, 0.2)'
                }),
                stroke: new Stroke({
                    color: '#f4820b',
                    width: 2
                }),
                image: new CircleStyle({
                    radius: 5,
                    fill: new Fill({
                        color: '#f4820b'
                    })
                })
            });
            feature.zoomToFeature($('#map-properties-geom').text());
        }
    }

})

