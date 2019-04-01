$(function () {
    if ($("input").is('#slider-price')) {
        function setValue(sliderObj) {
            var sliderObjName = (sliderObj.attr('id')).substring(7);
            var valuePrice = sliderObj.slider("getValue");
            $("#item_filter_" + sliderObjName + "_left_number").val(valuePrice[0]);
            $("#item_filter_" + sliderObjName + "_right_number").val(valuePrice[1]);
        }

        //------------------- налаштування слайдера Ціна

        var valuePrice = [Number($("#item_filter_price_left_number").val()), Number($("#item_filter_price_right_number").val())];
        valuePrice[0] = (valuePrice[0] == 0) ? 0 : valuePrice[0];
        valuePrice[1] = (valuePrice[1] == 0) ? Number(1000000) : valuePrice[1];

        var sliderPrice = $("input#slider-price").slider();
        sliderPrice.slider('setValue', valuePrice);

        sliderPrice.slider().change(function () {
            setValue(sliderPrice);
        });

        $('#item_filter_price_left_number').focusout(function () {

            var valuePrice = sliderPrice.slider("getValue");
            sliderPrice.slider('setValue', [Number($("#item_filter_price_left_number").val()), valuePrice[1]]);
        });

        $('#item_filter_price_right_number').focusout(function () {

            var valuePrice = sliderPrice.slider("getValue");
            sliderPrice.slider('setValue', [valuePrice[0], Number($("#item_filter_price_right_number").val())]);
        });

        //------------------- налаштування слайдера Площа

        var valueArea = [Number($("#item_filter_area_left_number").val()), Number($("#item_filter_area_right_number").val())];
        valueArea[0] = (valueArea[0] == 0) ? 0 : valueArea[0];
        valueArea[1] = (valueArea[1] == 0) ? Number(1000) : valueArea[1];

        var sliderArea = $("input#slider-area").slider();
        sliderArea.slider('setValue', valueArea);

        sliderArea.slider().change(function () {
            setValue(sliderArea);
        });

        $('#item_filter_area_left_number').focusout(function () {

            var valueArea = sliderArea.slider("getValue");
            sliderArea.slider('setValue', [Number($("#item_filter_area_left_number").val()), valueArea[1]]);
        });

        $('#item_filter_area_right_number').focusout(function () {

            var valueArea = sliderArea.slider("getValue");
            sliderArea.slider('setValue', [valueArea[0], Number($("#item_filter_area_right_number").val())]);
        });

    }

    /**
     * Змінює вигляд оголошень на list при ширині екрана менше 768px
     */

    function modifyUrl() {
        let hrefStart = window.location.href;
        let urlStart = hrefStart.split('?');

        if (urlStart[0].slice(-3) == 'tab') {
            hrefModify = urlStart[0].replace('/tab', '');

            if (typeof urlStart[1] !== "undefined") {
                hrefModify = hrefModify + '?' + urlStart[1]
            }
            window.location = hrefModify;
        }
    }

    const large = window.matchMedia('all and (max-width: 1199px)');
    if (large.matches) {
        $('#filter').addClass('collapsed-box');
        $('#btn-label').addClass('fa-plus');
        $('#btn-label').removeClass('fa-minus');
    }

    const small = window.matchMedia('all and (max-width: 768px)');
    if (small.matches) {
        modifyUrl();
    }
    $(window).on('resize', function () {
        var win = $(this); //this = window

        if (win.width() < 769) {
            modifyUrl();
        }
    });


});
