require('../../css/advertisement/details.scss');

$(function () {
    //---------------------------------------------підключаєм бутстрап слайдер і відміняє автоматичний скролінг
    $('.carousel').carousel({
        interval: false,
    });

    //---------------------------------------------змінює велике зображення при натисканні на мініатюру
    $('.details-picture-miniature img').on('click', function () {
        var imgIndex = $(this).index();
        var selected = $('.carousel-item').eq(imgIndex);
        if (!selected.hasClass('active')) {
            $('.carousel-inner .active').removeClass('active');
            selected.addClass('active');
        }
    });
    //----------------------------------------------слайдер мініатюр

    var delta = 14;//----сума padding і margin кожної мініатюри

    var heightBlockMiniature = $('.details-picture-miniature').height();
    var sumImgHeight = 0;
    $('.details-picture-miniature img').each(function (i, elem) {
        sumImgHeight = sumImgHeight + $(this).height() + delta;

    })
    if (sumImgHeight > heightBlockMiniature) {
        $('.detail-column').prepend('<a class="arrow-up btn btn-secondary btn-sm"><i class="fas fa-angle-up"></i></a>');
        $('.detail-column').append('<a class="arrow-down btn btn-secondary btn-sm"><i class="fas fa-angle-down"></i></a>');
    }

    var containerTop = $('.details-picture-miniature-container').offset().top;
    var widthFirstChild = $('.details-picture-miniature-container').find('img:first-child').height();//----висота першої картинки

    // console.log("початкова висота - ", containerTop);
    // console.log("висота блока - ", widthFirstChild);
    // console.log("сума блоків - ", sumImgHeight);
    $('.detail-column').find('a').on('click', function () {

        if ($(this).hasClass('arrow-down')) {
            if (($('.details-picture-miniature-container').offset().top - containerTop) > (sumImgHeight - heightBlockMiniature) * (-1)) {
                $('.details-picture-miniature-container').animate({
                    top: "-=80"
                }, 200)
                console.log("поточна висота - ", $('.details-picture-miniature-container').offset().top);
            }

        } else {
            if ($('.details-picture-miniature-container').offset().top < containerTop) {

                console.log("поточка висота - ", $('.details-picture-miniature-container').offset().top);
                if ((( $('.details-picture-miniature-container').offset().top - containerTop) > (-1*widthFirstChild)) && ( $('.details-picture-miniature-container').offset().top - containerTop) < 10) {
                    $('.details-picture-miniature-container').animate({
                        top: 0
                    }, 200)
                    console.log("зайшло - ");
                } else {
                    $('.details-picture-miniature-container').animate({
                        top: "+=80"
                    }, 200)
                }
            }
        }
    })

});
