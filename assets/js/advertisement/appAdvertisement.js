$(function () {
    console.log('Привіт!');
    // console.log($('#wrapper'));
    $(function () {
        $('[data-toggle="popover"]').popover()
    })
    $('[data-toggle="tooltip"]').tooltip({
        //Краще убрать ці затримки
        delay: {show: 100, hide: 100},
    });
});