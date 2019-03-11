$(function () {

    $('.datepicker').datepicker({
        clearBtn: true,
        language: 'uk',
    });

    $('.advertisement-filter').removeClass('col-3');
    $('.box').removeClass('hidden');


    //-------------------------------маска Inputmask

    serviceFunction.checkForInputTypeNumberBug($('.filter-number-range input[type=number]'));

/*    $('#item_filter_area_left_number, #item_filter_area_right_number').inputmask("9{1,10}[.]9{1,4}", {
        placeholder: "",
        allowMinus: false,

    });

    $('#item_filter_price_left_number, #item_filter_price_right_number').inputmask("9{1,10}", {
        numericInput: true,
        placeholder: "",
        rightAlign: false,
    });*/

})