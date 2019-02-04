require('jquery-inputmask');
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

$('[data-toggle="tooltip"]').tooltip();

$(function () {
    $("#text_search_cad_num").mask("9999999999:99:999:9999");

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
            zoom: 8,
            maxZoom: 16,
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


    if($('#advertisement_geom').val() !== undefined && $('#advertisement_geom').val() !== ''){
        var wkt = new WKT();
        var features = wkt.readFeature($('#advertisement_geom').val());
        advertMarker.setPosition(features.getGeometry().getCoordinates());
    }

    mapCabinet.on('singleclick', function (event) {
        $('#map-create-container').preloader();
        advertMarker.setPosition(event.coordinate);
        $('#advertisement-map-marker').show();
        var geom = new WKT().writeGeometry(new Point(event.coordinate));
        getAddress(geom);
        $('#advertisement_geom').val(geom);
    });
    mapCabinet.addOverlay(advertMarker);


    /*Пошук по кадастрововму номеру*/
    var coatuuRegExp = new RegExp('^([0-9]{10}:[0-9]{2}:[0-9]{3}:[0-9]{4})$');
    $('#btn_search_cad_num').on('click', function () {
        if (mapCabinet !== undefined) {
            var view = mapCabinet.getView();
            var searchval = $('#text_search_cad_num').val();

            if (coatuuRegExp.test(searchval)) {
                $('.for-preloader').preloader();
                $.ajax({
                    url: 'http://map.land.gov.ua/kadastrova-karta/find-Parcel',
                    type: 'GET',
                    data: {
                        'cadnum': searchval
                    },
                    success: function (data) {
                        if (data.data[0].st_xmin == null) {
                            $('.for-preloader').preloader('remove');
                            if (advertMarker !== undefined) {
                                $('#advert-map-marker').hide();
                                bootbox.alert('Нічого не знайдено!');
                            }
                        } else {
                            var box = [data.data[0].st_xmin, data.data[0].st_ymin, data.data[0].st_xmax, data.data[0].st_ymax];
                            view.fit(box, mapCabinet.getSize());
                            view.setZoom(18);
                            var coord = mapCabinet.getView().getCenter()
                            advertMarker.setPosition(coord);
                            $('#advert-map-marker').show();
                            var geom = new WKT().writeGeometry(new Point(coord));
                            $('#advertisement_geom').val(geom);
                            getAddress(geom);
                        }

                    },
                    error: function () {
                        $('for-preloader').preloader('remove');
                        $('html, body').css("cursor", "auto");
                        if (searchMarker !== undefined) {
                            //map.removeOverlay(searchMarker);
                            $('#advert-map-marker').hide();
                        }
                    }
                });
            } else {
                bootbox.alert('Кадастровий номер не вірний!')
            }
        }
    });


    //Получаємо адресу (область і район)
    function getAddress(geom) {
        $.ajax({
            url: Routing.generate('cabinet_get_position'),
            type: 'POST',
            data: {geom: geom},
            error: function (jqXHR, textStatus, errorThrown) {
                $('.for-preloader').preloader('remove');
                bootboxAlertMessage(jqXHR);
            },
            success: function (data) {
                $('.for-preloader').preloader('remove');
                $('#advertisement_address').val(data.address.region + ' область, ' + data.address.district + ' район');
            }
        });
    }

});