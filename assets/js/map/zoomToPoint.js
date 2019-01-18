
import {Circle as CircleStyle, Fill, Stroke, Style} from "ol/style";

$(function () {

    $('body').on('click', '.map-details-info .advertisement-text-position', function () {
        if ($('#map-properties-geom').text() !== undefined && $('#map-properties-geom').text() !== '') {

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
                        color: '#f4375b'
                    })
                })
            });
            feature.zoomToFeature($('#map-properties-geom').text());
        }
    });

})

