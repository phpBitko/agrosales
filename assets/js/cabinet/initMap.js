import OSM from "ol/source/OSM";
import TileLayer from "ol/layer/Tile";
import View from "ol/View";
import {defaults as defaultControls} from "ol/control";
import Map from "ol/Map";
import Overlay from "ol/Overlay";
import {fromLonLat} from "ol/proj";
import {transform} from "ol/proj";
import WKT from 'ol/format/WKT.js';
import Point from 'ol/geom/Point.js';
import TileWMS from "ol/source/TileWMS";

$(function () {


    var osmLayer = new TileLayer({
        source: new OSM(),
        name: 'osm'
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
        },

    });

    // Земельні ділянки
    var kievPublichka = new TileLayer({
        source: kievPublichkaSource,
        name: 'pub',
        visible: 1
    });

    var centerUkraine = fromLonLat([30.582233, 50.382778]);


    var mapCabinet = new Map({
        target: 'map-create-container',
        layers: [
            osmLayer,
            kievPublichka
        ],
        view: new View({
            center: centerUkraine,
            zoom: 8
        }),
        controls: defaultControls({
            attribution: false,
            zoom: false,
        })
    });

    var advertMarker = new Overlay({
        element: document.getElementById('advertisement-map-marker'),
        positioning: 'bottom-center'
    });


    if($('#advertisement_geom').val() !== ''){
        var wkt = new WKT();
        var features = wkt.readFeature($('#advertisement_geom').val());
        advertMarker.setPosition(features.getGeometry().getCoordinates());
    }

    mapCabinet.on('singleclick', function (event) {
        $('#map-create-container').preloader();
        advertMarker.setPosition(event.coordinate);
        $('#advertisement-map-marker').show();
        //var coord = transform([event.coordinate[0], event.coordinate[1]], 'EPSG:900913', 'EPSG:4326');
        /*$('#advertisement_coordB').val(Math.round(coord[0] * 100000) / 100000);
        $('#advertisement_coordL').val(Math.round(coord[1] * 100000) / 100000);*/
        var geom = new WKT().writeGeometry(new Point(event.coordinate));

        $.ajax({
            url: Routing.generate('cabinet_get_position'),
            type: 'POST',
            data: {geom: geom},
            error: function (jqXHR, textStatus, errorThrown) {
                $('#map-create-container').preloader('remove');
                if (jqXHR.responseJSON) {
                    bootbox.alert({
                        title: 'Виникла помилка!',
                        message: jqXHR.responseJSON.error
                    });
                } else {
                    bootbox.alert({
                        title: 'Виникла помилка!',
                        message: jqXHR.responseText
                    });
                }
            },
            success: function (data) {
                $('#map-create-container').preloader('remove');
                $('#advertisement_address').val(data.address.region + ' область, ' + data.address.district + ' район')
                //console.log(data)
                //console.log(data.region)
            }
        });



        $('#advertisement_geom').val(geom);
    });
    mapCabinet.addOverlay(advertMarker);
});