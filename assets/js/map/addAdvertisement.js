import WKT from 'ol/format/WKT.js';
import {Vector as VectorSource} from 'ol/source.js';
import Icon from "ol/style/Icon";
import {Style} from "ol/style";
import {Vector as VectorLayer} from "ol/layer";
// import {Circle as CircleStyle, Fill, Stroke, Style} from 'ol/style.js';

$(function () {


    //-------------------------------------------for points from advertisement
    var features;
    var format = new WKT();
    var vectorSourcePoints = new VectorSource;


    //-------------------------------------------створюєм як буде виглядать іконка для відобреження ділянок(оголошень)
    var svg = $('#icon_advertisement');
    var styleAdvert = new Style({
        image: new Icon({
            src: 'data:image/svg+xml;utf8,' + svg[0].outerHTML,
        })
    });


    //----------------переберає отримані дані з контролєра і додає в обєкт Sourse
    function addAdvertLayers(data) {
        $.each(data.data, function (i, value) {
            features = format.readFeature(value.geom, {
                dataProjection: 'EPSG:4326',
                featureProjection: 'EPSG:3857'
            });
            features.set('id', value.id);
            vectorSourcePoints.addFeature(features);
        });
    }


    //--- запит для отримання даних про координати точок, використувується бандл FOSjsroutingbundle
    function getAllAdvertisement() {
        $.ajax({
            url: Routing.generate('get_all_advertisement'),
            dataType: 'json',
            method: 'POST',
            success: function (data) {
                $('body').preloader('remove');
                addAdvertLayers(data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('body').preloader('remove');
                $('html, body').css("cursor", "auto");
                if (jqXHR.responseJSON) {
                    bootbox.alert({
                        title: 'Виникла помилка',
                        message: jqXHR.responseJSON.error
                    });
                } else {
                    bootbox.alert({
                        title: 'Виникла помилка',
                        message: jqXHR.responseText
                    });
                }
            },
        });
    }
    getAllAdvertisement();

    var vectorPoints = new VectorLayer({
        source: vectorSourcePoints,
        style: styleAdvert
    });
    mapSales.addLayer(vectorPoints);

})

