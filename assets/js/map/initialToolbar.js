import {createStringXY} from "ol/coordinate";
import MousePosition from "ol/control/MousePosition";
import {Vector as VectorLayer} from "ol/layer";
import {Circle as CircleStyle, Fill, Stroke, Style} from "ol/style";
import {Vector as VectorSource} from "ol/source";
import {LineString, Polygon} from "ol/geom";
import Draw from "ol/interaction/Draw";
import Overlay from "ol/Overlay";
import {fromLonLat} from "ol/proj";
import {unByKey} from 'ol/Observable.js';
import {getArea, getLength} from 'ol/sphere.js';

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
            duration: 200
        })
    });
    $('#zoom-full').on('click', function () {
        var view = mapSales.getView();
        view.animate({
            center: centerUkraine,
            zoom: 7,
            duration: 200
        })
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
    mapSales.addLayer(vector);
    var key;

    key = mapSales.on('pointermove', pointerMoveHandler);




    mapSales.getViewport().addEventListener('mouseout', function () {
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
        mapSales.addOverlay(helpTooltip);
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
        mapSales.addOverlay(measureTooltip);
    }

    /**
     * Let user change the geometry type.
     */
    // typeSelect.onchange = function() {
    //     mapSales.removeInteraction(draw);
    //     addInteraction();
    // };

    createMeasureTooltip();
    createHelpTooltip();
    unByKey(key);

    $('#control-panel-area').on('click', function () {
        //----------------------------починає вимірювання
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            mapSales.removeInteraction(draw);
            unByKey(key);
            addInteraction();

        } else {
            $(this).addClass('active');
            helpTooltipElement.classList.remove('hidden');
            key = mapSales.on('pointermove', pointerMoveHandler);
            mapSales.addInteraction(draw);
            // createMeasureTooltip();
            // createHelpTooltip();
        }
    });

    addInteraction();
});