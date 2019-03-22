import {Vector as VectorSource} from "ol/source";
import {Vector as VectorLayer} from "ol/layer";
import Draw from "ol/interaction/Draw";

//import {Circle as CircleStyle, Fill, Stroke, Style} from "ol/style";
import {LineString, Circle, Point, Polygon} from "ol/geom";
import Feature from 'ol/Feature';
import {unByKey} from "ol/Observable";
import {getArea, getLength} from "ol/sphere";
import Overlay from "ol/Overlay";
import WKT from "ol/format/WKT";

$(function () {

    $('.datepicker').datepicker({
        clearBtn: true,
        language: 'uk',
    });

    $('.advertisement-filter').removeClass('col-3');
    $('.box').removeClass('hidden');

    //-------------------------------маска Inputmask

    serviceFunction.checkForInputTypeNumberBug($('.filter-number-range input[type=number]'));

    $('#item_filter_area_left_number, #item_filter_area_right_number').inputmask("9{1,10}[.]9{1,4}", {
        placeholder: "",
        allowMinus: false,

    });

    $('#item_filter_price_left_number, #item_filter_price_right_number').inputmask("9{1,10}", {
        numericInput: true,
        placeholder: "",
        rightAlign: false,
    });

    //------------------------------vector layer for measure

    var format = new WKT();
    /**
     * Currently drawn feature.
     * @type {module:ol/show-phone~Feature}
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
     * Вираховує довжину лінії по координатам
     *
     * Length output.
     * @param {module:ol/geom/LineString~LineString} line The line.
     * @return {string} The length.
     */

    function lengthFromCoordinate(line) {

        let lengthCoordSum = 0;
        line.forEachSegment(function (start, end) {

            let dx = Math.pow((end[0] - start[0]), 2);
            let dy = Math.pow((end[1] - start[1]), 2);
            lengthCoordSum += Math.sqrt(dx + dy);

        });
        return lengthCoordSum;
    };

    var sourceFilter = new VectorSource();

    /**
     * Створюєм новий шар для фільтра
     */

    var layerFilter = new VectorLayer({
        source: sourceFilter,
        style: [StyleGlobalMeasureAdd, StyleGlobalMeasure],
        name: 'filter',
    });

/*    var draw; // global so we can remove it later
    var drawLine; // global so we can remove it later*/


    function addInteractionLine() {
        DrowLineGlobal = new Draw({
            source: sourceFilter,
            type: 'LineString',
            maxPoints: 2,
        });
        mapSales.addInteraction(DrowLineGlobal);

        var listener;
        var geomLine;
        DrowLineGlobal.on('drawstart', function (evt) {
            sketch = evt.feature;
            listener = sketch.getGeometry().on('change', function (evt) {
                geomLine = evt.target;
                $('#geomRadius').val(Math.round(getLength(geomLine)));
                measureTooltipElement.innerHTML = formatLength(geomLine);
                measureTooltip.setPosition(geomLine.getLastCoordinate());
            });
        }, this);

        DrowLineGlobal.on('drawend', function () {

            measureTooltipElement.className = 'tooltip tooltip-static';
            measureTooltip.setOffset([0, -7]);

            var geometry = new WKT().writeGeometry(new Point(sketch.getGeometry().getFirstCoordinate()));
            $('#featureGeom').val(geometry);
            $('#geomRadius').val(Math.round(getLength(geomLine)));
            let featurePoint = format.readFeature($('#featureGeom').val());
            let h = extentRatio(featurePoint);
            $('#circleRadius').val($('#geomRadius').val() * h);

            sketch = null;
            measureTooltipElement = null;

            createMeasureTooltip();
            unByKey(listener);
        }, this);
    }

    /**
     * Тип вибраної геометрії
     */
    var typeSelect;

    /**
     *
     */

    function addInteraction() {
        typeSelect = $('#typeGeometry')[0].value;
        if (typeSelect !== 'None') {
            DrowGlobal = new Draw({
                source: sourceFilter,
                type: typeSelect
            });
            mapSales.addInteraction(DrowGlobal);

            if (typeSelect == 'Circle') {
                addInteractionLine();
            }
            var listener;
            var geom;
            DrowGlobal.on('drawstart', function (evt) {
                sourceFilter.clear();
                featureMapControl.clearOverlays('filterOverlay');
                createMeasureTooltip();
                sketch = evt.feature;
                listener = sketch.getGeometry().on('change', function (evt) {
                    geom = evt.target;
                    if (geom instanceof Polygon) {
                        measureTooltipElement.innerHTML = formatArea(geom);
                        measureTooltip.setPosition(geom.getInteriorPoint().getCoordinates());
                    }
                });
            }, this);

            DrowGlobal.on('drawend', function () {
                measureTooltipElement.className = 'tooltip tooltip-static';
                measureTooltip.setOffset([0, -7]);
                if (geom instanceof Polygon) {
                    var geometry = new WKT().writeGeometry(new Polygon(sketch.getGeometry().getCoordinates()));
                    $('#featureGeom').val(geometry);
                }
                sketch = null;
                measureTooltipElement = null;
                $('#geomRadius').prop('disabled', geomRadiusChecked());
                createMeasureTooltip();
                unByKey(listener);
            }, this);
        }
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
            id: 'filterOverlay',
            element: measureTooltipElement,
            offset: [0, -15],
            positioning: 'bottom-center'
        });
        mapSales.addOverlay(measureTooltip);
    }

    /**
     * Handle change event.
     */
    $('#typeGeometry')[0].onchange = function () {
        if ($('#typeGeometry option:selected').val() !== 'None') {
            $('.measure-button').removeClass('active');
            mapSales.removeInteraction(DrowControlGlobal);
            clearFilter();
            $('#geomRadius').prop('disabled', geomRadiusChecked());
            addInteraction();
        } else {
            mapSales.removeInteraction(DrowGlobal);
            mapSales.removeInteraction(DrowLineGlobal);
        }
    };


    $('#checkboxGeometry').change(function () {

        if (featureMapControl.ifLayerExist('filter') == '') {
            mapSales.addLayer(layerFilter);
        }

        if ($('#checkboxGeometry').prop('checked')) {
            $('.measure-button').removeClass('active');
            mapSales.removeInteraction(DrowControlGlobal);
            $('#typeGeometry').val('Circle');
            $('#typeGeometry').prop('disabled', false);
            $('#geomRadius').prop('disabled', geomRadiusChecked());
            createMeasureTooltip();
            addInteraction();

        } else {

            $('#typeGeometry').prop('disabled', true);
            $('#geomRadius').prop('disabled', true);

            clearFilter();
        }
    });

    /**
     * Перевіряє стан в різних полях фільру для визначення чи дозволяти розблокувати поле для ведення радіусу
     *
     * @returns {boolean}
     */
    function geomRadiusChecked() {
        if ($('#typeGeometry option:selected').val() == 'Circle') {

            let feature = sourceFilter.getFeatures();
            if (sourceFilter.getFeatures().length > 0) {
                let geom = feature[0].getGeometry();

                if (geom.getType() == 'LineString') {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Вираховує коефіцієнт масштабування(трансформації) для обьєктів
     *
     * Length output.
     * @param {module:ol/geom/Point~Point} feature.
     * @return {Number} The ratio
     */

    var extentRatio = function (feature) {
        let ratio;
        if (feature.getGeometry() instanceof Point) {
            let pointCoordinates = feature.getGeometry().getCoordinates();
            let geomPoint = new Point(pointCoordinates);
            let geomPoint4326 = geomPoint.clone().transform('EPSG:3857', 'EPSG:4326');

            let featurePoint4326 = new Feature({
                geometry: geomPoint4326,
            });

            let latlong = featurePoint4326.getGeometry().getCoordinates();
            let latRadians = latlong[1] * Math.PI / 180;

            ratio = Number(1 / (Math.cos(latRadians)));
        }
        return ratio;
    };

    /**
     * Вираховує і будує коло з врахуванням трансформації при введенні в поле input
     */

    $('#geomRadius').on('change', function () {
        sourceFilter.clear();
        featureMapControl.clearOverlays('filterOverlay');

        var feature = format.readFeature($('#featureGeom').val());
        let ratio = extentRatio(feature);
        let centerCircle = feature.getGeometry().getCoordinates();
        let inputRadius = $('#geomRadius').val();
        let radius4326 = (inputRadius * ratio);

        $('#circleRadius').val(radius4326);
        var geomCircle4326 = new Circle(centerCircle, Number(radius4326));
        var points = [[centerCircle[0], centerCircle[1]], [centerCircle[0] + Number(radius4326), centerCircle[1]]];
        var geomLine = new LineString(points);

        var featureLine = new Feature({
            geometry: geomLine,
        });

        var featureCircle = new Feature({
            geometry: geomCircle4326,
        });
        createMeasureTooltip();

        measureTooltipElement.innerHTML = inputRadius > 1000 ? Math.round(inputRadius / 1000 * 100) / 100 + ' км' : inputRadius + ' м';
        measureTooltip.setPosition(geomLine.getLastCoordinate());
        measureTooltipElement.className = 'tooltip tooltip-static';
        measureTooltip.setOffset([0, -7]);

        sourceFilter.addFeature(featureCircle);
        sourceFilter.addFeature(featureLine);

        layerFilter.setSource(sourceFilter);

    });

    /**
     *  Очищення карти від даних шару фільтра
     */

    function clearFilter() {
        layerFilter.getSource().clear();
        mapSales.removeInteraction(DrowGlobal);
        mapSales.removeInteraction(DrowLineGlobal);
        $('#featureGeom').val('');
        $('#circleRadius, #geomRadius').val('');
        featureMapControl.clearOverlays('filterOverlay');
    }


});