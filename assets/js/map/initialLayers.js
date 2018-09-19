import Map from "ol/Map";
import View from "ol/View";
import TileLayer from "ol/layer/Tile";
import OSM from "ol/source/OSM.js";
import TileWMS from 'ol/source/TileWMS.js';
import Projection from 'ol/proj/Projection.js';
import BingMaps from 'ol/source/BingMaps';
import XYZ from 'ol/source/XYZ.js';
import {fromLonLat} from 'ol/proj.js';

import {defaults as defaultControls} from 'ol/control.js';


$(function () {

    var projection900913 = new Projection({
        code: 'EPSG:900913',
        units: 'm'
    });

    var centerUkraine = fromLonLat([31.182233, 48.382778]);

    var raster = new TileLayer({
        source: new BingMaps({
            imagerySet: 'Aerial',
            key: 'ApbFLozMP36noGX2U163d9AeYt6muKAM3riU1H38CuuHT5tKNyNfq2VwAGJ2KQXd'
        }),
        visible: false,
        name: 'bing'

    });


    //
    var osmLayer = new TileLayer({
        source: new OSM(),
        name: 'osm'
    });

    var kiev2006LayerSource = new XYZ({
        url: 'http://map.land.gov.ua/map/ortho10k_all/{z}/{x}/{-y}.jpg',
    });

    var kiev2006Layer = new TileLayer({
        source: kiev2006LayerSource,
        name: 'kiev2006',
        visible: 0,
        isBaseLayer: true,
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
            projection: projection900913,
        },

    });

    // Земельні ділянки
    var kievPublichka = new TileLayer({
        source: kievPublichkaSource,
        name: 'pub',
        visible: 0
    });

//------------------------------- створення об'єкта Map
    var map = new Map({
        target: 'map',
        layers: [
            osmLayer,
            raster,
            kiev2006Layer,
            kievPublichka
        ],
        view: new View({
            center: centerUkraine,
            zoom: 7
        }),
        controls: defaultControls({
            attribution: false,
            zoom: false,
        })
    });


    // var v = map.getControls();
    // //alert(v.getLength());
    // v.forEach(function (l) {
    //     alert(l[1])
    //
    // })
    // alert(v.item[0].name);


    // for (var i = 0; i < v.length; i++) {
    //     alert(v[i])
    // }


    $('.choose-layer li').on('click', function () {
        $('.choose-layer li').removeClass('active');
        $(this).addClass('active');
        var selected = $(this).attr('data-val');
        //alert(selected);
        var artbaz = ['pub', 'osm', 'OpenCycleMap', 'google', 'googlehybrid', 'kiev2006', 'emptyRelief', 'emptyLayer', 'topoUA', 'tileLayer', 'bing'];
        map.getLayers().forEach(function (l) {
            //alert(l.get('name'))

            if (($.inArray(l.get('name'), artbaz)) > -1) {

                if (l.get('name') !== selected) {
                    l.setVisible(false);
                } else {
                    //     if (l.get('name') == 'OpenCycleMap' || l.get('name') == 'osm') {
                    //         $('.osm-copyright').show();
                    //     } else {
                    //         $('.osm-copyright').hide();
                    //     }
                    l.setVisible(true);
                }
            }
        });
    });

    //----------------------------------------- alternative zoom
    $('.zoom-button').on('click', function () {
        var view = map.getView();
        var zoom = view.getZoom();
        var zoomChange = ($(this).attr('id') == 'zoom-in') ? +1 : -1;
        view.animate({
            zoom: zoom + zoomChange,
            duration: 200
        })
    });

    $('#zoom-full').on('click', function () {
        var view = map.getView();
        view.animate({
            center: centerUkraine,
            zoom: 7,
            duration: 200
        })
    });

    $('#control-panel-layer').on('click', function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $('.choose-layer').removeClass('choose-layer-move-in');
            $('.choose-layer').addClass('choose-layer-move-out');

        } else {
            $(this).addClass('active');
            $('.choose-layer').addClass('choose-layer-move-in');
            $('.choose-layer').removeClass('choose-layer-move-out');
        }

    });

    $('.choose-head .close').on('click', function () {
        $('.choose-layer').removeClass('choose-layer-move-in');
        $('.choose-layer').addClass('choose-layer-move-out');

        $('#control-panel-layer').removeClass('active');

    });

    $('[data-toggle="tooltip"]').tooltip({
        delay: {show: 700, hide: 100},
    });

});