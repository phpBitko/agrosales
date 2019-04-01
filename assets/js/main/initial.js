$(function () {
    $('.last-advertisement').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 500000,
        arrows: false,
        responsive: [
            {
                breakpoint: 1199,
                settings: { slidesToShow: 3 }
            },
            {
                breakpoint: 991,
                settings: { slidesToShow: 2 }
            },
            {
                breakpoint: 767,
                settings: { slidesToShow: 1 }
            }
        ]
    });
});