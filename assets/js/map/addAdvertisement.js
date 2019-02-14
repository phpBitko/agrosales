import WKT from 'ol/format/WKT';
import {Cluster, Vector as VectorSource} from 'ol/source.js';
import Icon from "ol/style/Icon";
import {Circle as CircleStyle, Fill, Stroke, Text} from "ol/style";
import {Vector as VectorLayer} from "ol/layer";
import {fromLonLat} from "ol/proj";
import Feature from 'ol/Feature';

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

    var styleAdvert = new Style({
        image: new Icon({
            src: '/bundles/app/img/maps-and-flags.png',
        })
    });
    var styleAdvertBlue = new Style({
        image: new Icon({
            src: '/bundles/app/img/maps-and-flags-blue.png',
        })
    });

    //--------------------------------стиль для відображення ділянок на карті
    var stylePoints = new Style({
        fill: new Fill({
            color: 'rgba(255, 255, 255, 0.2)'
        }),
        stroke: new Stroke({
            color: '#f4820b',
            width: 2
        }),
        image: new CircleStyle({
            radius: 7,
            fill: new Fill({
                color: '#f4820b'
            })
        })
    });


    function setStyleCluster(size, colorFill, colorStroke) {

        //--------------------------------стиль для відображення кластерів на карті
        var styleCluster = new Style({
            image: new CircleStyle({
                radius: 14,
                stroke: new Stroke({
                    color: colorStroke,
                    width: 8
                }),
                fill: new Fill({
                    color: colorFill
                })
            }),
            text: new Text({
                text: size.toString(),
                font: '14px "Lato"',
                fill: new Fill({
                    color: '#fff'
                })
            })
        });

        return styleCluster;

    };

//------------------------------- обробка кнопок фільтра
    $('#filter-submit').on('click', function () {
        var formData = new FormData($('[name = "item_filter"]')[0]);
        getFilterAdvertisement(formData);
    });

    $('.filter-clear').on('click', function () {
        getFilterAdvertisement();
        $('[name = "item_filter"]')[0].reset();
    });


    /**
     * перевіряє чи існує шар з іменем layerName на мапі
     *
     * @param {string} layerName, name of Layer.
     * @return {module:ol/layer/Vector~VectorLayer} | {undefined}
     */

    function ifLayerExist(layerName) {

        var objLayer;
        mapSales.getLayers().forEach(function (layer) {

            if (layer.get('name') == layerName) {
                objLayer = layer;
                return objLayer;
            }
        });
        return objLayer;
    }


    /**
     * додає дані в векторний шар на карту
     *
     * @param {module:ol/source/Vector~VectorSource} vectorSourcePoints.
     *
     */

    function addVectorLayer(vectorSourcePoints) {

        var vectorPoints;
        //-----------------------------------додаємо кластирізацію
        var clusterSource = new Cluster({
            distance: 50,
            source: vectorSourcePoints,
        });
        if (typeof(ifLayerExist('pointAdvertisement')) === "undefined") {

            var styleCache = {};

            var colorFill;
            var colorStroke;
            vectorPoints = new VectorLayer({

                source: clusterSource,
                name: 'pointAdvertisement',
                style: function (feature) {

                    var size = feature.get('features').length;
                    var style = styleCache[size];

                    if (!style) {
                        if (size > 1) {
                            if (size <= 10) {
                                colorFill = 'rgba(23, 146, 140, 0.9)';
                                colorStroke = 'rgba(23, 146, 140, 0.3)'
                            } else if (size > 10 && size < 30) {
                                colorFill = 'rgba(210, 163, 0, 0.9)';
                                colorStroke = 'rgba(210, 163, 0, 0.3)';
                            } else {
                                colorFill = 'rgba(241, 128, 23, 0.9)';
                                colorStroke = 'rgba(241, 128, 23, 0.3)';
                            }
                            style = setStyleCluster(size, colorFill, colorStroke);
                            styleCache[size] = style;
                        } else {
                            style = styleAdvert;
                            styleCache[size] = style;
                        }
                    }
                    return style;
                },

            });
            mapSales.addLayer(vectorPoints);

        } else {

            vectorPoints = ifLayerExist('pointAdvertisement');
            vectorPoints.setSource(clusterSource);

            if (vectorSourcePoints.getFeatures().length) {
                feature.zoomToFeature(vectorSourcePoints);
            }
        }
    }

    //----------------переберає отримані дані з контролєра і додає в обєкт Sourse
    function addAdvertLayers(data) {
        vectorSourcePoints.clear();
        $.each(data.data, function (i, value) {
            features = format.readFeature(value.geom, {
                dataProjection: 'EPSG:3857',
                featureProjection: 'EPSG:3857'
            });
            features.set('id', value.id);
            features.setId(value.id);
            vectorSourcePoints.addFeature(features);

        });
        addVectorLayer(vectorSourcePoints);
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

        var featureIcon = mapSales.forEachFeatureAtPixel(evt.pixel, function (featureIcon) {
            return featureIcon;

        });

        if (featureIcon && (!$('#control-panel-area').hasClass('active')) && (!$('#control-panel-ruler').hasClass('active'))) {

            var idAdvertisement = featureIcon.getProperties();

            if (idAdvertisement.hasOwnProperty('id')) {
                getDetailsAdvertisement(idAdvertisement.id);
            } else if (idAdvertisement.hasOwnProperty('features')) {

                var arrayClaster = idAdvertisement.features;

                if (arrayClaster.length === 1) {
                    idAdvertisement = arrayClaster[0].getProperties();

                    if (idAdvertisement.hasOwnProperty('id')) {
                        getDetailsAdvertisement(idAdvertisement.id);
                    }
                } else {
                    var view = mapSales.getView();

                    view.animate({
                        zoom: view.getZoom() + 1,
                        duration: 200,
                        center: evt.coordinate,

                    })
                }

            }

        }
    });

    //-----------------------------------------------змінює курсор на pointer при наведенні на іконку

    mapSales.on("pointermove", function (evt) {
        var hit = this.forEachFeatureAtPixel(evt.pixel, function (feature, layer) {
            return true;
        });

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

    //--- запит для отримання даних фільтру, бандл FOSjsroutingbundle

    function getFilterAdvertisement(formData = []) {

        $.ajax({
            url: Routing.generate('map_filter_advertisement'),
            method: 'POST',
            dataType: 'json',
            data: formData,
            async: false,
            processData: false,
            contentType: false,
            success: function (data) {
                console.log(data);
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

    getAllAdvertisement();

    if ($('.map-details-head').length) {
        if ($('.map-details-info').hasClass('hidden')) {
            $('.map-details-info').removeClass('hidden')
        }
        if ($('#map-properties-geom').text() !== undefined && $('#map-properties-geom').text() !== '') {
            feature.zoomToFeatureWKT($('#map-properties-geom').text());
        }
    }

})

