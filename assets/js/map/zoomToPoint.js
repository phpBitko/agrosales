

$(function () {

    $('body').on('click', '.map-details-info .advertisement-text-position', function () {
        if ($('#map-properties-geom').text() !== undefined && $('#map-properties-geom').text() !== '') {

            feature.zoomToFeatureWKT($('#map-properties-geom').text());
        }
    });

})

