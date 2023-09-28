$(function () {
  $('[data-toggle="tooltip"]').tooltip()
});

$("select").select2();

new WOW().init();
$(document).ready(function(){
    $('.menuOpen').on('click', function () {
        $("#navbarNav").addClass('nav-translate');
        $("#mainBody").addClass('overflow-hidden');
    });
    $('.menuClose').on('click', function () {
        $("#navbarNav").removeClass('nav-translate');
        $("#mainBody").removeClass('overflow-hidden');
    });

    $('.logUser-btn').on('click', function () {
        $(".dbMenu-box").addClass('nav-translate');
        $("#mainBody").addClass('overflow-hidden');
    });

    $('.dashboardMenuClose').on('click', function () {
        $(".dbMenu-box").removeClass('nav-translate');
        $("#mainBody").removeClass('overflow-hidden');
    });

    
});



if ( $('.product__slider-main').length ) {
	var $slider = $('.product__slider-main').on('init', function(slick) {
  		$('.product__slider-main').fadeIn(1000);
    }).slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        lazyLoad: 'ondemand',
        autoplaySpeed: 3000,
        asNavFor: '.product__slider-thmb',
        prevArrow: '<button class="slide-arrow prev-arrow"><i class="fal fa-chevron-left"></i></button>',
        nextArrow: '<button class="slide-arrow next-arrow"><i class="fal fa-chevron-right"></i></button>',
    });
  	var thumbnailsSlider = $('.product__slider-thmb').on('init', function(slick) {
  		$('.product__slider-thmb').fadeIn(1000);
    }).slick({
      	slidesToShow: 5,
      	slidesToScroll: 1,
      	lazyLoad: 'ondemand',
      	asNavFor: '.product__slider-main',
      	dots: false,
      	centerMode: false,
      	arrows: false,
      	focusOnSelect: true,
      	infinite: false,
        responsive: [
        {
          breakpoint: 767,
          settings: {
            slidesToShow: 3,
          }
        }],
    });
}


if ($(window).width() > 1199){
    $(window).on("load",function(){
        $(".catHead-menu-inner ul").mCustomScrollbar({
          axis:"x",
          autoExpandScrollbar:true,
          advanced:{autoExpandHorizontalScroll:true}
        });
    });
}(jQuery);




