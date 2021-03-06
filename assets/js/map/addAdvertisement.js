import WKT from 'ol/format/WKT';
import {Cluster, Vector as VectorSource} from 'ol/source.js';
import Icon from "ol/style/Icon";
import {Circle as CircleStyle, Fill, Stroke, Text} from "ol/style";
import {Vector as VectorLayer} from "ol/layer";
import {fromLonLat} from "ol/proj";
import Feature from 'ol/Feature';
import Point from 'ol/geom/Point';
import Polygon from 'ol/geom/Polygon';
import View from "ol/View";
import Style from "ol/style/Style";
import LineString from 'ol/geom/LineString';
import Select from "ol/interaction/Select";
import {pointerMove} from 'ol/events/condition.js';
import Overlay from "ol/Overlay";
import {unByKey} from "ol/Observable";

global.VectorLayer = VectorLayer;
global.sourceVectorClosest = '';


$(function () {

    //-------------------------------------------for points from advertisement
    var features;
    var format = new WKT();
    var vectorSourcePoints = new VectorSource;


    var centerUkraine = fromLonLat([31.182233, 48.382778]);//------------------координати центру України

    var view = new View({
        center: centerUkraine,
        zoom: 5
    });


    //--------------------------------стиль для відображення ділянок на карті
    /*var stylePoints = new Style({
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
    });*/

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
        mapSales.removeInteraction(DrowGlobal);
        mapSales.removeInteraction(DrowLineGlobal);
    });

    /**
     *  Очищення карти від даних шару фільтра
     */

    function clearFilter() {

        var filterLayer = featureMapControl.ifLayerExist('filter');
        if (filterLayer) {
            filterLayer.getSource().clear();
        }

        featureMapControl.clearOverlays('filterOverlay');
        mapSales.removeInteraction(DrowGlobal);
        mapSales.removeInteraction(DrowLineGlobal);

        $('#featureGeom').val('');
        $('#circleRadius, #geomRadius').val('');
    }

    /**
     * ---------------------------------------------------очищає форму
     */

    $('.filter-clear').on('click', function () {

        $('.filter-price input').val('');
        $('.filter-area input').val('');
        $('.filter-date input').val('');
        $('.filter-provision input').prop('checked', false);
        $('.filter-purpose option').prop("selected", false);
        $('#checkboxGeometry').prop('checked', false);
        $('#typeGeometry').prop('disabled', true);
        $('#geomRadius').prop('disabled', true);
        clearFilter();

        getFilterAdvertisement();
    });


    /**
     * додає дані в векторний шар на карту
     *
     * @param {module:ol/source/Vector~VectorSource} vectorSourcePoints.
     *
     */
    var vectorPoints;

    function addVectorLayer(vectorSourcePoints) {

        //var vectorPoints;
        //-----------------------------------додаємо кластерізацію
        var clusterSource = new Cluster({
            distance: 50,
            source: vectorSourcePoints,
        });

        if ((featureMapControl.ifLayerExist('pointAdvertisement')) == "") {

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
                            style = styleGlobalAdvert;
                            styleCache[size] = style;
                        }
                    }
                    return style;
                },

            });
            mapSales.addLayer(vectorPoints);

        } else {
            vectorPoints = featureMapControl.ifLayerExist('pointAdvertisement');
            vectorPoints.setSource(clusterSource);

            if (vectorSourcePoints.getFeatures().length) {
                featureMapControl.zoomToFeature(vectorSourcePoints);
            }
        }
    }

    //----------------переберає отримані дані з контролєра і додає в обєкт Sourse
    function addAdvertLayers(data) {
        $('.filter-result').html('');
        $('.filter-result').removeClass('alert alert-danger');

        if (data.errors.length > 0) {
            addErrors(data.errors);
        }
        vectorSourcePoints.clear();
        $.each(data.data, function (i, value) {

            features = format.readFeature(value.geom, {
                dataProjection: 'EPSG:3857',
                featureProjection: 'EPSG:3857'
            });
            features.set('id', value.id);
            features.set('type', 'advertisement');
            features.setId(value.id);
            vectorSourcePoints.addFeature(features);

        });
        addVectorLayer(vectorSourcePoints);
    }

    function addErrors(errors) {
        errors.forEach(function (item) {
            $('.filter-result').prepend('<div>' + item + '</div>');
            $('.filter-result').addClass('alert alert-danger');
        });
    }

    //-----------------------------------дозволяє переміщать форму з детальною інформацією

    $('.map-details-info').draggable({
        start: function () {
            $('.map-details-info').addClass('transition-non');
        },
        stop: function () {
            $('.move-right').removeClass('hidden-ico');
        }
    });


    //-----------------------------------відображаєм форму з детальною інформацією

    function addMapDetails(data) {
        $('.map-details-info').html(data.table.details);
        if ($('.map-details-info').hasClass('hidden')) {
            $('.map-details-info').removeClass('hidden')
        }
        //setTooltip;
    }

    //--- запит для отримання даних про координати точок, використувується бандл FOSjsroutingbundle
    function getAllAdvertisement() {
        $.ajax({
            url: Routing.generate('map_filter_advertisement'),
            dataType: 'json',
            method: 'POST',
            success: function (data) {
                $('body').preloader('remove');
                addAdvertLayers(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('body').preloader('remove');
                bootboxAlertMessage(jqXHR);
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
        } else if ($('#control-panel-closest').hasClass('active')) {
            var coord = evt.coordinate;
            $.ajax({
                url: Routing.generate('map.get_closest_object'),
                dataType: 'json',
                data: {coord: coord},
                method: 'POST',
                success: function (data) {
                    addClosestObject(data.closestObject, coord);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    bootboxAlertMessage(jqXHR);
                },
            });
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
                if (data.data.closestObject !== undefined && $('#control-panel-closest').hasClass('active')) {
                    addClosestObject(data.data.closestObject, data.data.coord);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                bootboxAlertMessage(jqXHR);
            },
        });
    }

    //******************Додаємо шар найближчих об'єктів*****************************/
    sourceVectorClosest = new VectorSource({wrapX: false});

    var pointText = new Text({
        font: 'bold 24px "Lato"',
        fill: new Fill({
            color: '#5b5e65'
        }),
        backgroundFill: new Fill({
            color: '#fffcf3'
        }),
        textAlign: 'right',
        offsetX: -10
    });

    var lineText = new Text({
        font: '16px "Lato"',
        fill: new Fill({
            color: '#fffcf3'
        }),
        stroke: new Stroke({
            color: '#484848',
            width: 4
        }),
        placement: 'line',
        textAlign: 'center',
        offsetY: -10
    });

    var style = new Style({
        fill: new Fill({
            color: '#7cff47'
        }),
        stroke: new Stroke({
            color: '#484848',
            width: 2,
            //  miterLimit: 50,
            // lineDashOffset: [5,6],
            lineDash: [5, 10]
        }),

        image: new CircleStyle({
            radius: 6,
            stroke: new Stroke({
                color: '#ff0800',
                width: 2,
                //  miterLimit: 50,
                // lineDashOffset: [5,6],
            }),
            fill: new Fill({
                color: '#fffcf3'
            })
        }),
    });

    var vectorClosestLayer = new VectorLayer({
        source: sourceVectorClosest,
        style: function (feature) {
            var text;
            if (feature.getGeometry() instanceof Point) {
                text = pointText;
            } else {
                text = lineText;
            }
            style.setText(text);
            style.getText().setText(feature.get('name'));
            return [new Style({
                stroke: new Stroke({
                    color: 'white',
                    width: 5,
                    lineDash: [5, 10]
                })
            }), style];
        }
    });
    mapSales.addLayer(vectorClosestLayer);

    function addClosestObject(data, coord) {
        sourceVectorClosest.clear();
        data.forEach(function (element) {
            var cooordData = new WKT().readGeometry(element.geom);

            var lineString = new LineString([coord, cooordData.getCoordinates()]);
            var featurePoint = new Feature({
                geometry: cooordData,
                name: element.t.trim()
            });
            var featureLine = new Feature({
                geometry: lineString,
                name: element.dist + 'м'
            });
            sourceVectorClosest.addFeature(featurePoint);
            sourceVectorClosest.addFeature(featureLine);
        })
        var view = mapSales.getView();
        view.fit(sourceVectorClosest.getExtent(), {duration: 300})

    }

    //*****END*************Додаємо шар найближчих об'єктів***************END**************/


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
                $('body').preloader('remove');
                addAdvertLayers(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('body').preloader('remove');
                bootboxAlertMessage(jqXHR);
            },
        });
    }

    getAllAdvertisement();

    if ($('.map-details-head').length) {
        if ($('.map-details-info').hasClass('hidden')) {
            $('.map-details-info').removeClass('hidden')
        }
        if ($('#map-properties-geom').text() !== undefined && $('#map-properties-geom').text() !== '') {
            featureMapControl.zoomToFeatureWKT($('#map-properties-geom').text());
        }
    }


    /*     SelectFeatureGlobal = new Select({
            condition: pointerMove,
            layers: [vectorPoints],

        });
        mapSales.addInteraction(SelectFeatureGlobal);*/


    //------------------------------vector layer for select


    var featureOverlay = new VectorLayer({
        name: 'select',
        map: mapSales,
        source: new VectorSource,
        style: styleGlobalAdvertBlue,
        updateWhileAnimating: true, // optional, for instant visual feedback
        updateWhileInteracting: true // optional, for instant visual feedback

    });

    mapSales.addLayer(featureOverlay);

    /*function geometryStyle(feature) {
        var
            style = [],
            geometry_type = feature.getGeometry().getType(),
            white = [255, 255, 255, 1],
            blue = [0, 153, 255, 1],
            width = 3;

        style['LineString'] = [
            new Style({
                stroke: new Stroke({
                    color: white, width: width + 2
                })
            }),
            new Style({
                stroke: new Stroke({
                    color: blue, width: width
                })
            })
        ],
            style['Polygon'] = [
                new Style({
                    fill: new Fill({color: [255, 255, 255, 0.5]})
                }),
                new Style({
                    stroke: new Stroke({
                        color: white, width: 3.5
                    })
                }),
                new Style({
                    stroke: new Stroke({
                        color: blue, width: 2.5
                    })
                })
            ],
            style['Point'] = [
                new Style({
                    image: new CircleStyle({
                        radius: width * 2,
                        fill: new Fill({color: blue}),
                        stroke: new Stroke({
                            color: white, width: width / 2
                        })
                    })
                })
            ];

        return style[geometry_type];
    }*/


    var highlight;

    /**
     * Відслідковує рух курсора мишки на корті і виділяє ділянки
     */

    mapSales.on('pointermove', function (evt) {
        if (evt.dragging) {
            return;
        }

        var pixel = mapSales.getEventPixel(evt.originalEvent);
        var feature = mapSales.forEachFeatureAtPixel(pixel, function (feature) {
            return feature;
        });

        if (feature !== highlight) {
            if (highlight) {
                $('#map').css('cursor', '');
                featureOverlay.getSource().clear();
            }
            if (feature) {
                if (isPointBelongAdvertisement(feature)) {
                    $('#map').css('cursor', 'pointer');
                    featureOverlay.getSource().addFeature(feature);
                }
            }
            highlight = feature;
        }
    });

    /**
     * Перевіряє чи є обьект Feature точкою що належить до ділянок (advertisement)
     *
     * @param feature ol/Feature~Feature
     * @returns {boolean}
     */

    function isPointBelongAdvertisement(feature) {
        if (feature.getGeometry().getType() === 'Point') {
            if (feature.getProperties().hasOwnProperty('features')) {
                if (feature.get('features').length == 1) {
                    let pointFeatures = feature.get('features');
                    let pointFeature = pointFeatures[0].getProperties();
                    if (pointFeature.hasOwnProperty('type') && pointFeature.type == 'advertisement') {
                        return true;
                    }
                }
            }
        }
        return false;
    }

});

