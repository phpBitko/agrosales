import Map from "ol/Map";
import View from "ol/View";
import TileLayer from "ol/layer/Tile";
import OSM from 'ol/source/OSM';
import TileWMS from 'ol/source/TileWMS.js';
import Projection from 'ol/proj/Projection.js';
import BingMaps from 'ol/source/BingMaps';
import XYZ from 'ol/source/XYZ.js';
//import WKT from 'ol/format/WKT';
import {fromLonLat} from 'ol/proj.js';
import {defaults as defaultControls} from 'ol/control.js';
/*import sourceVector from 'ol/source/Vector';
import layerVector from 'ol/layer/Vector';
import Style from 'ol/style/Style';
import Fill from 'ol/style/Fill';
import Circle from 'ol/style/Circle';
import Stroke from 'ol/style/Stroke';

global.wktObj = new WKT();
global.sourceVector = sourceVector;
global.layerVector = layerVector;
global.Style = Style;*/

$(function () {
    $('body').preloader();//--------------------запускаємо прелоадер поки все загружаєтся

    var projection900913 = new Projection({
        code: 'EPSG:900913',
        units: 'm'
    });

    var centerUkraine = fromLonLat([31.182233, 48.382778]);//------------------координати центру України

    //--------------------------------------------------створюєм новий шар карта Bing
    var raster = new TileLayer({
        source: new BingMaps({
            imagerySet: 'Aerial',
            key: 'ApbFLozMP36noGX2U163d9AeYt6muKAM3riU1H38CuuHT5tKNyNfq2VwAGJ2KQXd'
        }),
        visible: false,
        name: 'bing'
    });

    //--------------------------------------------------створюєм новий шар OSM
    var osmLayer = new TileLayer({
        source: new OSM(),
        name: 'osm'
    });

    //--------------------------------------------------створюєм новий шар Орто з публічної кадастрової карти
    var kiev2006LayerSource = new XYZ({
        url: 'http://map.land.gov.ua/map/ortho10k_all/{z}/{x}/{-y}.jpg',
    });

    var kiev2006Layer = new TileLayer({
        source: kiev2006LayerSource,
        name: 'kiev2006',
        visible: 0,
        isBaseLayer: true,
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
        visible: 0
    });

    //---------------------------------------------------- створення об'єкту Map
    mapSales = new Map({
        target: 'map',
        layers: [
            osmLayer,
            raster,
            kiev2006Layer,
            kievPublichka,
            //vector,
            //vectorPoints,
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

/*    var style = new Style({
        fill: new Fill({
            color: 'rgba(255, 255, 255, 0.4)',
        }),
        stroke: new Stroke({
            color: 'red',
            width: 5
        }),
        image: new Circle({
            radius: 7,
            fill: new Fill({
                color: '#ffcc33'
            })
        })
    });*/

    //zoomTo();
});