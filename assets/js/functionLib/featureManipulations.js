var feature = {
    /**
     * Зумує на обєкт feature (дані в форматі WKT, обєкт - Map)
     *
     * @param dataWkt - WKT object
     * @param map - ol/Map
     *
     * @return mixed
     */

    zoomToFeature: function (dataWkt, map = mapSales) {
        try {
            var features = objWkt.readFeature(dataWkt);
            var figure = features.getGeometry();
            var view = map.getView();

            if (figure.getType() == 'Point') {
                view.fit(figure, {minResolution: 5, duration: 300});
            } else {
                var extent = figure.getExtent();
                view.fit(extent, {duration: 300});
            }
        } catch (e) {
            alert(e.message);
        }
    },

    /**
     * Відображає обєкт feature і зумує на нього (дані в форматі WKT, обєкт - Style, обєкт - Map)
     *
     * @param dataWkt - WKT object
     * @param style - ol/style
     * @param map - ol/Map
     *
     * @return mixed
     */

    drawFeatureInLayer: function (dataWkt, style = '', map = mapSales) {
        try {
            if (style instanceof styleGlobal) {
                var features = objWkt.readFeature(dataWkt);

                var lSource = new sourceVectorGlobal();
                lSource.addFeature(features);

                var layer = new layerVectorGlobal({
                    source: lSource,
                    style: style,
                    name: 'layer',
                });
                map.addLayer(layer);
            } else {
                alert("параметр style не є обєктом ol/Style, не можливо відобразити дані!");
            }
            feature.zoomToFeature(dataWkt, map)
        } catch (e) {
            alert(e.message);
        }

    },
/*
    drawFeatureInDraw: function (dataWkt, style = '', map = mapSales) {
        //console.log(style);

        var lSource = new sourceVectorGlobal();
        var features = objWkt.readFeature(dataWkt);
        //console.log(features);
        lSource.addFeature(features);
        var layer = new layerDrawGlobal({
            type: 'Point',
            source: lSource,
            style: style,

        });
        map.addInteraction(layer);
        feature.zoomToFeature(dataWkt, map)
    },*/
}

module.exports = feature;