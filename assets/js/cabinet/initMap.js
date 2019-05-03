import Style from "ol/style/Style";

require('jquery-inputmask');

import TileLayer from "ol/layer/Tile";
import View from "ol/View";
import {defaults as defaultControls} from "ol/control";
import Map from "ol/Map";
import Overlay from "ol/Overlay";
import {fromLonLat} from "ol/proj";
import WKT from 'ol/format/WKT.js';
import Point from 'ol/geom/Point.js';
import Polygon from 'ol/geom/Polygon.js';
import TileWMS from "ol/source/TileWMS";
import {Vector as VectorLayer} from 'ol/layer.js';
import Draw from 'ol/interaction/Draw.js';
import {OSM, Vector as VectorSource} from 'ol/source.js';
import {Circle as CircleStyle, Fill, Stroke} from "ol/style";



$(function () {

    $('[data-toggle="tooltip"]').tooltip();
    $("#text_search_cad_num").mask("9999999999:99:999:9999");
    $('#advertisement_declarantPhoneNum').mask("(999)999-99-99");

    let osmLayer = new TileLayer({
        source: new OSM(),
        name: 'osm'
    });

    let kievPublichkaSource = new TileWMS({
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
    let kievPublichka = new TileLayer({
        source: kievPublichkaSource,
        name: 'pub',
        visible: 1
    });

    let centerUkraine = fromLonLat([30.582233, 50.382778]);

    let sourceParcel = new VectorSource({wrapX: false});

    let styleParcel = new Style({
        fill: new Fill({
            color: '#7cff47'
        }),
        stroke: new Stroke({
            color: '#484848',
            width: 2,
        }),
        image: new CircleStyle({
            radius: 6,
            stroke: new Stroke({
                color: '#ff0800',
                width: 2,
            }),
            fill: new Fill({
                color: '#fffcf3'
            })
        }),
    });

    let vectorParcel = new VectorLayer({
        source: sourceParcel,
        style: styleParcel,
        opacity: 0.9
    });

    let mapCabinet = new Map({
        target: 'map-create-container',
        layers: [
            osmLayer,
            kievPublichka,
            vectorParcel
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
        let geom ;
        let typeGeom;

        if($('#advertisement_geomPolygon').val() !== ''){
            geom = $('#advertisement_geomPolygon').val();
            typeGeom = 'polygon';
        }else{
            geom = $('#advertisement_geom').val();
            typeGeom = 'point';
        }

        let wkt = new WKT();
        let features = wkt.readFeature(geom);
        console.log(features);
        sourceParcel.addFeature(features);

        let extent = sourceParcel.getExtent();
        mapCabinet.getView().fit(extent);
    }

    let draw;
    function addInteraction(typeSelect) {
        if (typeSelect !== 'None') {
            draw = new Draw({
                source: sourceParcel,
                type: typeSelect
            });
            mapCabinet.addInteraction(draw);
        }
    }

    let typeGeom = 'Point';

    $('.toggle_geom_group button').on('click', function () {
        mapCabinet.removeInteraction(draw);
        sourceParcel.clear();

        $.each($('.toggle_geom_group button'), function () {
            $(this).toggleClass('hide').toggleClass('active');
            if($(this).hasClass('active')){
                typeGeom = $(this).attr('data-val');
                addInteraction($(this).attr('data-val'));
                draw.on('drawstart', function (evt) {
                    sourceParcel.clear();
                });
            }
        });
    });

    addInteraction(typeGeom);
    sourceParcel.on('addfeature', function(evt){
        let feature = evt.feature;
        let coords = feature.getGeometry().getCoordinates();
        let geomObject;
        let geom;
        if(typeGeom === 'Point'){
            geomObject = new Point(coords);
            geom = new WKT().writeGeometry(geomObject);
            $('#advertisement_geomPolygon').val('');
            $('#advertisement_geom').val(geom);
        }else{
            geomObject = new Polygon(coords);
            geom = new WKT().writeGeometry(geomObject);
            $('#advertisement_geom').val('');
            $('#advertisement_geomPolygon').val(geom);
        }
        getAddress(geom);
    });

    draw.on('drawstart', function (evt) {
        sourceParcel.clear();
    });

    /*Пошук по кадастрововму номеру*/
    var coatuuRegExp = new RegExp('^([0-9]{10}:[0-9]{2}:[0-9]{3}:[0-9]{4})$');
    $('#btn_search_cad_num').on('click', function () {
        if (mapCabinet !== undefined) {
            var view = mapCabinet.getView();
            var searchval = $('#text_search_cad_num').val();

            if (coatuuRegExp.test(searchval)) {
                $('.for-preloader').preloader();
                $.ajax({
                    url: 'https://map.land.gov.ua/kadastrova-karta/find-Parcel',
                    type: 'GET',
                    data: {
                        'cadnum': searchval
                    },
                    success: function (data) {
                        $('.for-preloader').preloader('remove');
                        if (data.data[0].st_xmin == null) {
                            $('.for-preloader').preloader('remove');
                            if (advertMarker !== undefined) {
                                $('#advert-map-marker').hide();
                                bootboxMessage('Нічого не знайдено!');
                            }
                        } else {
                            sourceParcel.clear();
                            var box = [data.data[0].st_xmin, data.data[0].st_ymin, data.data[0].st_xmax, data.data[0].st_ymax];
                            view.fit(box, mapCabinet.getSize());
                            view.setZoom(18);

                            let wkt = new WKT();
                            let coord = mapCabinet.getView().getCenter();
                            let geom = new WKT().writeGeometry(new Point(coord));
                            let features = wkt.readFeature(geom);
                            sourceParcel.addFeature(features);

                            $('#advertisement_geom').val(geom);
                            getAddress(geom);
                        }

                    },
                    error: function () {
                        $('.for-preloader').preloader('remove');
                        $('html, body').css("cursor", "auto");
                        if (searchMarker !== undefined) {
                            //map.removeOverlay(searchMarker);
                            $('#advert-map-marker').hide();
                        }
                    }
                });
            } else {
                bootboxMessage('Кадастровий номер не вірний!')
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