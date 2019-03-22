import Map from "ol/Map";
import {Circle as CircleStyle, Fill, Stroke, Style} from "ol/style";
import {getArea, getLength} from 'ol/sphere.js';
import Collection from 'ol/Collection';

global.Map = Map;
global.MapGlobal = Map;
global.CollectionGlobal = Collection;
global.StyleGlobalMeasure = Style;
global.StyleGlobalMeasureAdd = Style;
global.StyleGlobalMeasureInteract = Style;
global.StyleGlobalMeasureInteractAdd = Style;
global.formatLength = '';
global.formatArea = '';

$(function () {

    /**
     * Стиль для відображення вимирів панелі управління
     */
    StyleGlobalMeasure = new Style({
        fill: new Fill({
            color: 'rgba(255, 255, 255, 0.2)',
        }),
        stroke: new Stroke({
            color: '#755C48',
            width: 2,
            /*lineDash: [10, 10],*/
        }),
        image: new CircleStyle({
            radius: 5,
            fill: new Fill({
                color: '#755C48',
            })
        })
    });

    StyleGlobalMeasureAdd = new Style({
        stroke: new Stroke({
            color: 'white',
            width: 3,
          /*  lineDash: [10, 10]*/
        }),
    });

    StyleGlobalMeasureInteractAdd = new Style({
        stroke: new Stroke({
            color: 'white',
            width: 3,
            lineDash: [10, 10],
        }),
    });

    /**
     * Стиль для відображення вимирів панелі управління під час рисування
     */
    StyleGlobalMeasureInteract = new Style({
        fill: new Fill({
            color: 'rgba(255, 255, 255, 0.2)'
        }),
        stroke: new Stroke({
            color: 'rgba(0, 0, 0, 0.7)',
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
    });

    /**
     * Format length output.
     * @param {module:ol/geom/LineString~LineString} line The line.
     * @return {string} The formatted length.
     */
    formatLength = function (line) {
        var length = getLength(line);
        var output;
        if (length > 1000) {
            output = (Math.round(length / 1000 * 100) / 100) +
                ' ' + 'км';
        } else {
            output = (Math.round(length * 100) / 100) +
                ' ' + 'м';
        }
        return output;
    };
    /**
     * Format area output.
     * @param {module:ol/geom/Polygon~Polygon} polygon The polygon.
     * @return {string} Formatted area.
     */
    formatArea = function (polygon) {
        var area = getArea(polygon);
        var output;
        if (area > 10000) {
            output = (Math.round(area / 1000000 * 100) / 100) +
                ' ' + 'км<sup>2</sup>';
        } else {
            output = (Math.round(area * 100) / 100) +
                ' ' + 'м<sup>2</sup>';
        }
        return output;
    };


});