import Map from "ol/Map";
import View from "ol/View";
import TileLayer from "ol/layer/Tile";

import Overlay from 'ol/Overlay.js';
import {Vector as VectorLayer} from 'ol/layer.js';
import {Vector as VectorSource} from 'ol/source.js';
//import VectorSource2 from 'ol/source/Vector.js';
import {Circle as CircleStyle, Fill, Stroke, Style} from 'ol/style.js';
import Draw from 'ol/interaction/Draw.js';
import {getArea, getLength} from 'ol/sphere.js';
import {unByKey} from 'ol/Observable.js';

import {LineString, Polygon} from 'ol/geom.js';

import OSM from 'ol/source/OSM';
import TileWMS from 'ol/source/TileWMS.js';
import Projection from 'ol/proj/Projection.js';
import BingMaps from 'ol/source/BingMaps';
import XYZ from 'ol/source/XYZ.js';
import {fromLonLat} from 'ol/proj.js';
import MousePosition from 'ol/control/MousePosition.js';
import {defaults as defaultControls} from 'ol/control.js';
import {createStringXY} from 'ol/coordinate.js';
import Feature from 'ol/Feature.js';
import WKT from 'ol/format/WKT.js';

$(function () {


    var projection900913 = new Projection({
        code: 'EPSG:900913',
        units: 'm'
    });

    var centerUkraine = fromLonLat([31.182233, 48.382778]);

    var raster = new TileLayer({
        source: new BingMaps({
            imagerySet: 'Aerial',
            key: 'ApbFLozMP36noGX2U163d9AeYt6muKAM3riU1H38CuuHT5tKNyNfq2VwAGJ2KQXd'
        }),
        visible: false,
        name: 'bing'

    });


    //
    var osmLayer = new TileLayer({
        source: new OSM(),
        name: 'osm'
    });

    var kiev2006LayerSource = new XYZ({
        url: 'http://map.land.gov.ua/map/ortho10k_all/{z}/{x}/{-y}.jpg',
    });

    var kiev2006Layer = new TileLayer({
        source: kiev2006LayerSource,
        name: 'kiev2006',
        visible: 0,
        isBaseLayer: true,
    });

    var kievPublichkaSource = new TileWMS({
        url: 'http://map.land.gov.ua/geowebcache/service/wms',
        params: {
            'LAYERS': 'kadastr',
            'ALIAS': 'Кадастровий поділ',
            'ALIAS_E': 'Cadastral Division',
            'VERSION': '1.1.1',
            'TILED': 'true',
            'FORMAT': 'image/png',
            'WIDTH': 256,
            'HEIGHT': 256,
            'CRS': 'EPSG:900913',
            serverType: 'geoserver',
            projection: projection900913,
        },

    });

    // Земельні ділянки
    var kievPublichka = new TileLayer({
        source: kievPublichkaSource,
        name: 'pub',
        visible: 0
    });

    //------------------------------vector layer for measure
    var source = new VectorSource();

    var vector = new VectorLayer({
        source: source,
        style: new Style({
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
        })
    });

    //-------------------------------------- for measure

    /**
     * Currently drawn feature.
     * @type {module:ol/Feature~Feature}
     */
    var sketch;


    /**
     * The help tooltip element.
     * @type {Element}
     */
    var helpTooltipElement;


    /**
     * Overlay to show the help messages.
     * @type {module:ol/Overlay}
     */
    var helpTooltip;


    /**
     * The measure tooltip element.
     * @type {Element}
     */
    var measureTooltipElement;


    /**
     * Overlay to show the measurement.
     * @type {module:ol/Overlay}
     */
    var measureTooltip;


    /**
     * Message to show when the user is drawing a polygon.
     * @type {string}
     */
    var continuePolygonMsg = 'Click to continue drawing the polygon';


    /**
     * Message to show when the user is drawing a line.
     * @type {string}
     */
    var continueLineMsg = 'Click to continue drawing the line';


    /**
     * Handle pointer move.
     * @param {module:ol/MapBrowserEvent~MapBrowserEvent} evt The event.
     */
    var pointerMoveHandler = function (evt) {
        if (evt.dragging) {
            return;
        }
        /** @type {string} */
        var helpMsg = 'Click to start drawing';
        //alert(sketch.getGeometry);

        if (sketch) {
            var geom = (sketch.getGeometry());
            if (geom instanceof Polygon) {
                helpMsg = continuePolygonMsg;
            } else if (geom instanceof LineString) {
                helpMsg = continueLineMsg;
            }
        }

        helpTooltipElement.innerHTML = helpMsg;
        helpTooltip.setPosition(evt.coordinate);

        helpTooltipElement.classList.remove('hidden');
    };


//---------------------------------------------------mouse position
    var mousePositionControl = new MousePosition({
        coordinateFormat: createStringXY(5),
        projection: 'EPSG:4326',
        // comment the following two lines to have the mouse position
        // be placed within the map.
        className: 'custom-mouse-position',
        target: document.getElementById('mouse-position'),
        undefinedHTML: '&nbsp;'
    });


//------------------------------- створення об'єкта Map
    var map = new Map({
        target: 'map',
        layers: [
            osmLayer,
            raster,
            kiev2006Layer,
            kievPublichka,
            vector,
            //vectorPoints,
        ],
        view: new View({
            center: centerUkraine,
            zoom: 7
        }),
        controls: defaultControls({
            attribution: false,
            zoom: false,
        }).extend([mousePositionControl])
    });
    var key;

    key = map.on('pointermove', pointerMoveHandler);


    map.getViewport().addEventListener('mouseout', function () {
        helpTooltipElement.classList.add('hidden');
    });


    // var typeSelect = document.getElementById('type');

    var draw; // global so we can remove it later


    /**
     * Format length output.
     * @param {module:ol/geom/LineString~LineString} line The line.
     * @return {string} The formatted length.
     */
    var formatLength = function (line) {
        var length = getLength(line);
        var output;
        if (length > 100) {
            output = (Math.round(length / 1000 * 100) / 100) +
                ' ' + 'km';
        } else {
            output = (Math.round(length * 100) / 100) +
                ' ' + 'm';
        }
        return output;
    };


    /**
     * Format area output.
     * @param {module:ol/geom/Polygon~Polygon} polygon The polygon.
     * @return {string} Formatted area.
     */
    var formatArea = function (polygon) {
        var area = getArea(polygon);
        var output;
        if (area > 10000) {
            output = (Math.round(area / 1000000 * 100) / 100) +
                ' ' + 'km<sup>2</sup>';
        } else {
            output = (Math.round(area * 100) / 100) +
                ' ' + 'm<sup>2</sup>';
        }
        return output;
    };

    function addInteraction() {
        var type = 'Polygon';
        draw = new Draw({
            source: source,
            type: type,
            style: new Style({
                fill: new Fill({
                    color: 'rgba(255, 255, 255, 0.2)'
                }),
                stroke: new Stroke({
                    color: 'rgba(0, 0, 0, 0.5)',
                    lineDash: [10, 10],
                    width: 2
                }),
                image: new CircleStyle({
                    radius: 5,
                    stroke: new Stroke({
                        color: 'rgba(0, 0, 0, 0.7)'
                    }),
                    fill: new Fill({
                        color: 'rgba(255, 255, 255, 0.2)'
                    })
                })
            })
        });


        var listener;
        draw.on('drawstart',
            function (evt) {
                // set sketch
                sketch = evt.feature;

                /** @type {module:ol/coordinate~Coordinate|undefined} */
                var tooltipCoord = evt.coordinate;


                listener = sketch.getGeometry().on('change', function (evt) {
                    var geom = evt.target;
                    var output;
                    if (geom instanceof Polygon) {
                        output = formatArea(geom);
                        tooltipCoord = geom.getInteriorPoint().getCoordinates();
                    } else if (geom instanceof LineString) {
                        output = formatLength(geom);
                        tooltipCoord = geom.getLastCoordinate();
                    }
                    measureTooltipElement.innerHTML = output;
                    measureTooltip.setPosition(tooltipCoord);
                });
            }, this);

        draw.on('drawend',
            function () {
                measureTooltipElement.className = 'tooltip tooltip-static';
                measureTooltip.setOffset([0, -7]);
                // unset sketch
                sketch = null;
                // unset tooltip so that a new one can be created
                measureTooltipElement = null;
                createMeasureTooltip();
                unByKey(listener);
            }, this);
    }


    /**
     * Creates a new help tooltip
     */
    function createHelpTooltip() {
        if (helpTooltipElement) {
            helpTooltipElement.parentNode.removeChild(helpTooltipElement);
        }
        helpTooltipElement = document.createElement('div');
        helpTooltipElement.className = 'tooltip hidden';
        helpTooltip = new Overlay({
            element: helpTooltipElement,
            offset: [15, 0],
            positioning: 'center-left'
        });
        map.addOverlay(helpTooltip);
    }


    /**
     * Creates a new measure tooltip
     */
    function createMeasureTooltip() {
        if (measureTooltipElement) {
            measureTooltipElement.parentNode.removeChild(measureTooltipElement);
        }
        measureTooltipElement = document.createElement('div');
        measureTooltipElement.className = 'tooltip tooltip-measure';
        measureTooltip = new Overlay({
            element: measureTooltipElement,
            offset: [0, -15],
            positioning: 'bottom-center'
        });
        map.addOverlay(measureTooltip);
    }


    /**
     * Let user change the geometry type.
     */
    // typeSelect.onchange = function() {
    //     map.removeInteraction(draw);
    //     addInteraction();
    // };

    createMeasureTooltip();
    createHelpTooltip();
    unByKey(key);


    $('#control-panel-area').on('click', function () {
        //----------------------------починає вимірювання
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            map.removeInteraction(draw);
            unByKey(key);
            addInteraction();

        } else {
            $(this).addClass('active');
            helpTooltipElement.classList.remove('hidden');
            key = map.on('pointermove', pointerMoveHandler);
            map.addInteraction(draw);
            // createMeasureTooltip();
            // createHelpTooltip();
        }
    });

    addInteraction();
    // var v = map.getControls();
    // //alert(v.getLength());
    // v.forEach(function (l) {
    //     alert(l[1])
    //
    // })
    // alert(v.item[0].name);


    // for (var i = 0; i < v.length; i++) {
    //     alert(v[i])
    // }


    $('.choose-layer li').on('click', function () {
        $('.choose-layer li').removeClass('active');
        $(this).addClass('active');
        var selected = $(this).attr('data-val');
        //alert(selected);
        var artbaz = ['pub', 'osm', 'OpenCycleMap', 'google', 'googlehybrid', 'kiev2006', 'emptyRelief', 'emptyLayer', 'topoUA', 'tileLayer', 'bing'];
        map.getLayers().forEach(function (l) {
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

    //----------------------------------------- alternative zoom
    $('.zoom-button').on('click', function () {
        var view = map.getView();
        var zoom = view.getZoom();
        var zoomChange = ($(this).attr('id') == 'zoom-in') ? +1 : -1;
        view.animate({
            zoom: zoom + zoomChange,
            duration: 200
        })
    });

    $('#zoom-full').on('click', function () {
        var view = map.getView();
        view.animate({
            center: centerUkraine,
            zoom: 7,
            duration: 200
        })
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

    $('[data-toggle="tooltip"]').tooltip({
        delay: {show: 700, hide: 100},
    });
    //-------------------------------------------for points from advertisement-----

    var features;
    var format = new WKT();
    var vectorSourcePoints = new VectorSource;
    //-------------------------------------------- style icons for point
    var stylesPoints = new Style({
        image: new CircleStyle({
            radius: 5,
            fill: new Fill({color: '#bada55'}),
            stroke: new Stroke({color: '#0000F0', width: 1})
        })
    });
//----------------переберає отримані дані з контролєра і додає в обєкт Sourse
    function addAdvertLayers(data) {
        $.each(data.data, function (i, value) {

            features = format.readFeature(value.geom, {
                dataProjection: 'EPSG:4326',
                featureProjection: 'EPSG:3857'
            });
            features.set('id', value.id);
            vectorSourcePoints.addFeature(features);
        });
    }
//--- запит для отримання даних про координати точок, використувується бандл FOSjsroutingbundle
    function getAllAdvertisement() {
        $.ajax({
            url: Routing.generate('get_all_advertisement'),
            dataType: 'json',
            method: 'POST',
            async: true,
            success: function (data) {
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

    var vectorPoints = new VectorLayer({
        source: vectorSourcePoints,
        style: stylesPoints
    });
    map.addLayer(vectorPoints);


//---------------------------------------------------------------------------




});