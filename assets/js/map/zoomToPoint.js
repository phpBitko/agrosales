import WKT from "ol/format/WKT";
import View from "ol/View";
import {fromLonLat} from "ol/proj";


$(function () {

    var point; // -----------------обоєкт point
    var points; // ----------------- масив координат обєкта
    var centerUkraine = fromLonLat([31.182233, 48.382778]);//------------------координати центру України
    console.log(centerUkraine);

    var view = new View({
        center: centerUkraine,
        zoom: 5
    });
    $('body').on('click', '.map-details-info .advertisement-text-position', function () {

        if ($('#map-properties-geom').text() !== undefined && $('#map-properties-geom').text() !== '') {
            var wkt = new WKT();
            var features = wkt.readFeature($('#map-properties-geom').text());
            console.log(features);
            point = (features.getGeometry());
            points = point.getCoordinates();
            view = mapSales.getView();
            /*            view.fit(point, {minResolution: 5}, {
                            duration: 1000
                        });*/
            view.animate({
                center: points,
                duration: 300,
                zoom: 14
            });


            /*            viewer.olView.fit(feature.getGeometry(), {
                            duration: 1000
                        });*/

        }

    });

})

