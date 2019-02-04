import WKT from 'ol/format/WKT';
import {Cluster, Vector as VectorSource} from 'ol/source.js';
import Icon from "ol/style/Icon";
import {Circle as CircleStyle, Fill, Stroke, Text} from "ol/style";
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

    function setStyleCluster(size) {

        //--------------------------------стиль для відображення кластерів на карті
        var styleCluster = new Style({
            image: new CircleStyle({
                radius: 8,
                stroke: new Stroke({
                    color: 'rgba(244, 130, 11, 0.6)',
                    width: 6
                }),
                fill: new Fill({
                    color: '#f4820b'
                })
            }),
            text: new Text({
                text: size.toString(),
                fill: new Fill({
                    color: '#fff'
                })
            })
        });

        return styleCluster;

    };



    $('#filter-submit').on('click', function () {

        var formData = new FormData($('[name = "item_filter"]')[0]);

        getFilterAdvertisement(formData);

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
            distance: 20,
            source: vectorSourcePoints,
        });
        if (typeof(ifLayerExist('pointAdvertisement')) === "undefined") {

            var styleCache = {};

            vectorPoints = new VectorLayer({

                source: clusterSource,
                name: 'pointAdvertisement',
                style: function (feature) {

                    var size = feature.get('features').length;
                    var style = styleCache[size];

                    if (!style) {

                        if (size > 1) {
                            style = setStyleCluster(size);
                            styleCache[size] = style;
                        } else {
                            style = stylePoints;
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

    function getFilterAdvertisement(formData) {

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

