

$(function () {
    $('body').on('click', '.map-details-info .advertisement-text-position', function (func) {
        func.preventDefault();
        if ($('#map-properties-geom').text() !== undefined && $('#map-properties-geom').text() !== '') {

            featureMapControl.zoomToFeatureWKT($('#map-properties-geom').text());
        }
    });

})

