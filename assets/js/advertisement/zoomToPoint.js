
$(function () {

    if ($('#map-properties-geom').text() !== undefined && $('#map-properties-geom').text() !== '')
    {
        featureMapControl.zoomToFeatureWKT($('#map-properties-geom').text());
    }
})

