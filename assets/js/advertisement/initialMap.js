import TileLayer from "ol/layer/Tile";
import OSM from "ol/source/OSM";
import {fromLonLat} from "ol/proj";
import Map from "ol/Map";
import View from "ol/View";
import {defaults as defaultControls} from "ol/control";
import Icon from "ol/style/Icon";
import WKT from "ol/format/WKT";
import {Vector as VectorSource} from "ol/source";
import {Style} from "ol/style";
import {Vector as VectorLayer} from "ol/layer";
import TileWMS from "ol/source/TileWMS";
import Projection from "ol/proj/Projection";


$(function () {
    //-------------------------------------------for points from advertisement
    var vectorSourcePoints = new VectorSource;

    var projection900913 = new Projection({
        code: 'EPSG:900913',
        units: 'm'
    });

    //-------------------------------------------створюєм як буде виглядать іконка для відобреження ділянок(оголошень)
    if ($('#icon_advertisement').length > 0) {
        var svg = $('#icon_advertisement');
        var styleAdvert = new Style({
            image: new Icon({
                src: 'data:image/svg+xml;utf8,' + svg[0].outerHTML,
            })
        });
    }

    var point;

    if ($('#details-geom').text() !== undefined && $('#details-geom').text() !== '') {

        var wkt = new WKT();
        var features = wkt.readFeature($('#details-geom').text());
        point = (features.getGeometry());
        vectorSourcePoints.addFeature(features);
    }
    var coordinates = point.getCoordinates();

    var centerUkraine = fromLonLat([31.182233, 48.382778]);//------------------координати центру України
    console.log(centerUkraine);

    var view = new View({
        center: centerUkraine,
        zoom: 5
    });

    if (point != null) {
        view.fit(point, {minResolution: 5});
    }

    var osmLayer = new TileLayer({
        source: new OSM(),
        name: 'osm'
    });
    //--------------------------------------------------створюєм новий шар Ділянки з публічної кадастрової карти
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

    var kievPublichka = new TileLayer({
        source: kievPublichkaSource,
        name: 'pub',
        visible: 1
    });


    var mapDetail = new Map({
        target: 'details-map-container',
        layers: [
            osmLayer,
            kievPublichka,
        ],
        view: view,
        controls: defaultControls({
            attribution: false,
            zoom: false,
        })
    });
    var vectorPoints = new VectorLayer({
        source: vectorSourcePoints,
        style: styleAdvert
    });
    mapDetail.addLayer(vectorPoints);

    console.log(coordinates);
    $('#details-map-full').on('click', function () {
        view.fit(point, {minResolution: 5});
        //view = mapDetail.getView();
       /* view.animate({
            center: [0, 0],
            zoom: 5,
            duration: 200
        })*/
    });


})