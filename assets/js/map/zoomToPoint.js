
import {Circle as CircleStyle, Fill, Stroke, Style} from "ol/style";

$(function () {

    $('body').on('click', '.map-details-info .advertisement-text-position', function () {
        if ($('#map-properties-geom').text() !== undefined && $('#map-properties-geom').text() !== '') {

            feature.zoomToFeature($('#map-properties-geom').text());
        }
    });

})

