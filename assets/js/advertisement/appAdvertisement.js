$(function () {
    console.log('Привіт!');
    // console.log($('#wrapper'));
    $(function () {
        $('[data-toggle="popover"]').popover()
    })
    $('[data-toggle="tooltip"]').tooltip({
        delay: {show: 100, hide: 100},
    });

    $("#ex2").slider({});



});