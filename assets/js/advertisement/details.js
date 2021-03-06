$(function () {
    //------------------------------------підключаєм бутстрап слайдер і відміняє автоматичний скролінг
    $('.carousel').carousel({
        interval: false,
    });


    //-------------------------------------змінює велике зображення при натисканні на мініатюру
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

    });

    if (sumImgHeight > heightBlockMiniature) {
        $('.detail-column').prepend('<a class="arrow-up btn btn-secondary btn-sm"><i class="fas fa-angle-up"></i></a>');
        $('.detail-column').append('<a class="arrow-down btn btn-secondary btn-sm"><i class="fas fa-angle-down"></i></a>');
    }

    if ($('.details-picture-miniature-container').length > 0) {
        var containerTop = $('.details-picture-miniature-container').offset().top;
        var widthFirstChild = $('.details-picture-miniature-container').find('img:first-child').height();//----висота першої картинки

    }

    $('.detail-column').find('a').on('click', function () {

        if ($(this).hasClass('arrow-down')) {
            if (($('.details-picture-miniature-container').offset().top - containerTop) > (sumImgHeight - heightBlockMiniature) * (-1)) {
                $('.details-picture-miniature-container').animate({
                    top: "-=80"
                }, 200)

            }

        } else {
            if ($('.details-picture-miniature-container').offset().top < containerTop) {

                if ((($('.details-picture-miniature-container').offset().top - containerTop) > (-1 * widthFirstChild)) && ($('.details-picture-miniature-container').offset().top - containerTop) < 10) {
                    $('.details-picture-miniature-container').animate({
                        top: 0
                    }, 200)
                    //console.log("зайшло - ");
                } else {
                    $('.details-picture-miniature-container').animate({
                        top: "+=80"
                    }, 200)
                }
            }
        }
    });

    //---------------------------------------------прилипання блоку автор оголошення при прокрутці

    if ($('.details-author').length > 0) {
        var topPosition = $('.details-author').offset().top;
        $(window).scroll(function () {
            var top = $(document).scrollTop();
            if (top > topPosition) {
                $('.details-author').addClass('fixed');
            }
            else {
                $('.details-author').removeClass('fixed');
            }
        });
    }


    /**
     * ------------------------- Отримання телефону клієнта
     *
     */

    $('.show-phone').on('click', function (func) {
        func.preventDefault();
        /*  Відміняємо стандартну поведінку ссилки (не відбуваєтся перехід по посиланню)  */
        var id = $(this).data('id');
        console.log(id);
        $.ajax({
            url: Routing.generate('advertisement_get_phone'),
            dataType: 'json',
            data: {id: id},
            method: 'POST',
            beforeSend:function(){
                $('#details-user .overlay').css('visibility','visible');
            },
            success: function (data) {
                $('.phone').html(data.advertisementPhone);
                $('#details-user .overlay').css('visibility','hidden');
                $('#phone-show').css('pointer-events','none');
            },

            error: function (jqXHR, textStatus, errorThrown) {
                $('#details-user .overlay').css('visibility','hidden');

                bootboxAlertMessage(jqXHR);
            },
        });
    });

    $('#details-user').addClass('collapsed-box');
    $('#details-user .btn-box-tool i').addClass('fa-plus');
    $('#details-user .btn-box-tool i').removeClass('fa-minus');

});

