$(function () {
    console.log('Привіт!');
    // console.log($('#wrapper'));
    $(function () {
        $('[data-toggle="popover"]').popover()
    })
    $('[data-toggle="tooltip"]').tooltip({
        delay: {show: 700, hide: 100},
    });
});