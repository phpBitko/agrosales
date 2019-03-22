import {createStringXY} from "ol/coordinate";
import MousePosition from "ol/control/MousePosition";
import {Vector as VectorLayer} from "ol/layer";
//import {Circle as CircleStyle, Fill, Stroke, Style} from "ol/style";
import {Vector as VectorSource} from "ol/source";
import {LineString, Polygon} from "ol/geom";
import Draw from "ol/interaction/Draw";
import Overlay from "ol/Overlay";
import {fromLonLat} from "ol/proj";
import {unByKey} from 'ol/Observable.js';
//import {getArea, getLength} from 'ol/sphere.js';
import WKT from 'ol/format/WKT';
import sourceVector from "ol/source/Vector";
import layerVector from "ol/layer/Vector";

global.objWkt = new WKT;
global.sourceVectorGlobal = sourceVector;
global.layerVectorGlobal = layerVector;
global.layerDrawGlobal = Draw;


$(function () {

    var centerUkraine = fromLonLat([31.182233, 48.382778]);//------------------координати центру України

    //---------------------------------------------------mouse position
    var mousePositionControl = new MousePosition({
        coordinateFormat: createStringXY(5),
        projection: 'EPSG:4326',
        className: 'custom-mouse-position',
        target: document.getElementById('mouse-position'),
        undefinedHTML: '&nbsp;'
    });

    mapSales.controls.extend([mousePositionControl]);//--------Додаємо новий control до карти

    //-------------------------------------------------- alternative zoom
    $('.zoom-button').on('click', function () {
        var view = mapSales.getView();
        var zoom = view.getZoom();
        var zoomChange = ($(this).attr('id') == 'zoom-in') ? +1 : -1;
        view.animate({
            zoom: zoom + zoomChange,
            duration: 200,
        })
    });

    $('#zoom-full').on('click', function () {
        var view = mapSales.getView();
        view.animate({
            center: centerUkraine,
            zoom: 7,
            duration: 200,
        })
    });

    //------------------------------vector layer for measure
    var source = new VectorSource();

    var vectorDraw = new VectorLayer({
        source: source,
        name: 'draw',
        style: [StyleGlobalMeasureAdd, StyleGlobalMeasure]
    });

    //vectorDraw.setStyle(StyleGlobalMeasure);

    //-------------------------------------- for measure

    /**
     * Currently drawn feature.
     * @type {module:ol/Feature~Feature}
     */
    var sketch;

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
     * Handle pointer move.
     * @param {module:ol/MapBrowserEvent~MapBrowserEvent} evt The event.
     */
    var pointerMoveHandler = function (evt) {
        if (evt.dragging) {
            return;
        }
    };
    mapSales.addLayer(vectorDraw);

    var key;

    key = mapSales.on('pointermove', pointerMoveHandler);

    var draw; // global so we can remove it later

    var type = 'LineString';

    function addInteraction() {
        draw = new Draw({
            source: source,
            type: type,
            style: [StyleGlobalMeasureInteractAdd, StyleGlobalMeasureInteract],
        });
        mapSales.addInteraction(draw);

        var listener;
        draw.on('drawstart', function (evt) {
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

        draw.on('drawend', function () {
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
     * Creates a new measure tooltip
     */
    function createMeasureTooltip() {
        if (measureTooltipElement) {
            measureTooltipElement.parentNode.removeChild(measureTooltipElement);
        }
        measureTooltipElement = document.createElement('div');
        measureTooltipElement.className = 'tooltip tooltip-measure';
        measureTooltip = new Overlay({
            id: 'measure',
            element: measureTooltipElement,
            offset: [0, -15],
            positioning: 'bottom-center'
        });
        mapSales.addOverlay(measureTooltip);
    }

    /*    createMeasureTooltip();
        unByKey(key);*/

    function startMeasure(measureObj) {

        unByKey(key);
        mapSales.removeInteraction(draw);

        if (measureObj.hasClass('active')) {
            type = (measureObj.attr('id') == 'control-panel-ruler') ? 'LineString' : 'Polygon';
            key = mapSales.on('pointermove', pointerMoveHandler);
            createMeasureTooltip();
            addInteraction();
        }
    }

    //----------------------------починає вимірювання

    $('#control-panel-area').on('click', function () {
        $(this).toggleClass('active');
        $('#control-panel-ruler').removeClass('active');
        startMeasure($(this));
    });

    $('#control-panel-ruler').on('click', function () {
        $(this).toggleClass('active');
        $('#control-panel-area').removeClass('active');
        startMeasure($(this));
    });


    $('#control-panel-closest').on('click', function () {
        clearInteraction();
        $('.measure-button').removeClass('active');
        $(this).toggleClass('active');
    });

    function clearInteraction() {
/*        var collection = mapSales.getOverlays();
        collection.clear();*/
        featureMapControl.clearOverlays('measure');
        vectorDraw.getSource().clear();
        mapSales.removeInteraction(draw);
        unByKey(key);
        sourceVectorClosest.clear();
    }

    $('#control-panel-erase').on('click', function () {
        clearInteraction();
        $('.measure-button').removeClass('active');

    });

//--------------------------------------------------------- Потрібне зєднання по протоколу https
    $('#control-panel-placeholder').on('click', function () {

    });


    $('#control-panel-print').on('click', function () {
        window.print();
    });

});