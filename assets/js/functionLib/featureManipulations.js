
var featureMapControl = {

    /**
     * перевіряє чи існує шар з іменем layerName на мапі
     *
     * @param {string} layerName, name of Layer.
     * @return {module:ol/layer/Vector~VectorLayer} | {false}
     */

    ifLayerExist: function (layerName, map = mapSales) {

        var objLayer = '';
        map.getLayers().forEach(function (layer) {
            if (layer.get('name') == layerName) {
                objLayer = layer;
            }
        });
        return objLayer;
    },

    /**
     * Вираховує довжину лінії по координатам
     *
     * Length output.
     * @param {module:ol/geom/LineString~LineString} line The line.
     * @return {string} The length.
     */

    lengthFromCoordinate: function (line) {

        let lengthCoordSum = 0;
        line.forEachSegment(function (start, end) {
            let dx = Math.pow((end[0] - start[0]), 2);
            let dy = Math.pow((end[1] - start[1]), 2);
            lengthCoordSum += Math.sqrt(dx + dy);

        });
        return lengthCoordSum;
    },

    /**
     * Вираховує довжину лінії по координатам і форматує її
     *
     * Format length output.
     * @param {module:ol/geom/LineString~LineString} line The line.
     * @return {string} The formatted length.
     */

    formatLengthFromCoordinate: function (line) {

        let length = lengthFromCoordinate(line);
        var output;
        if (length > 1000) {
            output = (Math.round(length / 1000 * 100) / 100) +
                ' ' + 'км';
        } else {
            output = (Math.round(length * 100) / 100) +
                ' ' + 'м';
        }
        return output;
    },

    /*    /!**
         * Format length output.
         * @param {module:ol/geom/LineString~LineString} line The line.
         * @return {string} The formatted length.
         *!/
        formatLength: function (line) {
            var length = getLength(line);
            var output;
            if (length > 1000) {
                output = (Math.round(length / 1000 * 100) / 100) +
                    ' ' + 'км';
            } else {
                output = (Math.round(length * 100) / 100) +
                    ' ' + 'м';
            }
            return output;
        },*/


    /**
     * Зумує на обєкти features (дані в форматі VectorSource, обєкт - Map)
     *
     * @param vectorSourcePoints - ol/source/Vector~VectorSource
     * @param map - ol/Map
     *
     * @return mixed
     */

    zoomToFeature: function (vectorSourcePoints, map = mapSales) {
        try {
            var view = map.getView();
            if (vectorSourcePoints.getFeatures().length > 1) {
                view.fit(vectorSourcePoints.getExtent(), {duration: 300});

            } else {
                view.fit(vectorSourcePoints.getExtent(), {minResolution: 5, duration: 300});
            }
        } catch (e) {
            alert(e.message);
        }
    },

    /**
     * Зумує на обєкт feature (дані в форматі WKT, обєкт - Map)
     *
     * @param dataWkt - WKT object
     * @param map - ol/Map
     *
     * @return mixed
     */

    zoomToFeatureWKT: function (dataWkt, map = mapSales) {
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
            feature.zoomToFeatureWKT(dataWkt, map)
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
    /**
     * Видаляє написи на карті по ключу (id), у разі відсутності видаляє всі
     * @param overlaysId
     */

    clearOverlays: function (overlaysId = 'All') {

        var collections = mapSales.getOverlays();
        var collectionFilter = new CollectionGlobal();

        if (overlaysId !== 'All') {
            if (collections.getLength() > 0) {
                collections.forEach(function (overlay) {

                    if (overlay.getId() !== overlaysId) {
                        collectionFilter.push(overlay);
                    }
                })
            }
            collections.clear();
            collectionFilter.forEach(function (overlay) {
                mapSales.addOverlay(overlay);
            })
        } else {
            collections.clear();
        }
    },
}

module.exports = featureMapControl;