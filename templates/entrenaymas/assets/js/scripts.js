$(document).ready(function() {
  "use strict";
  //WOW Script
  new WOW().init();

  //Magnific Popup Script
  $('.psweb-popup').magnificPopup ({
    delegate: 'a',
    type: 'image',
    closeOnContentClick: false,
    closeBtnInside: false,
    removalDelay: 100,
    mainClass: 'mfp-fade mfp-img-mobile',
    closeMarkup:'<div class="mfp-close" title="%title%"></div>',
    image: {
      verticalFit: true,
      titleSrc: function(item) {
        return item.el.attr('title') + ' &middot; <a class="image-source-link" href="'+item.el.attr('data-source')+'" target="_blank">image source</a>';
      }
    },
    gallery: {
      enabled: true,
      arrowMarkup:'<div title="%title%" class="mfp-arrow mfp-arrow-%dir%"></div>',
    },
  });

  //Add/Remove Class Script
  /*
  $('.psweb-toggle').on('click', function () {
    $(this).toggleClass('active');
  });
  $('.doctor-tags a').click(function() {
    $(this).toggleClass('active');
  });
  */
  $('.mid-item h3').click(function() {
    $(this).toggleClass('active');
    $(this).parent().find('.mid-item-info').slideToggle();
  });

  //Dropdown Script
  /*
  $('.dropdown').on('click', function (e) {
    $(this).attr('tabindex', 1).focus();
    $(this).toggleClass('active');
    $(this).find('.dropdown-menu').slideToggle(300);
  }).on('focusout', function () {
    $(this).removeClass('active');
    $(this).find('.dropdown-menu').slideUp(300);
  });
  */
  $('.dropdown').on('click', function (e) {
    if ($(this).hasClass("active")) {
      $(this).removeClass('active');
      $(this).find('.dropdown-menu').slideUp(300);
    } else {
      $(this).attr('tabindex', 1).focus();
      $(this).addClass('active');
      $(this).find('.dropdown-menu').slideDown(300);
    }
  });
  $(".custom-checkbox").on("click",function(e){
    e.stopPropagation();
    if ($(e.currentTarget).hasClass("buscador")) window.buscar();
  })
  /*
  $('.dropdown .dropdown-menu li').click(function () {
    $(this).parents('.dropdown').find('span').text($(this).text());
    $(this).parents('.dropdown').find('input').attr('value', $(this).attr('id'));
  });
  */

  //Insert Script
  $(window).resize(function() {
    if (screen.width <= 1199) {
      $('.psweb-topbar').insertAfter('.psweb-navigation > ul');
    };
    if (screen.width <= 575) {
      $('.header-link').insertAfter('.psweb-navigation > ul');
    };
  });
  $(window).trigger('resize');

  //Tooltip Script
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })

  //Owl Carousel Slider Script
  $('.owl-carousel').each(function() {
    var $carousel = $(this);
    var $items = ($carousel.data('items') !== undefined) ? $carousel.data('items') : 1;
    var $items_tablet = ($carousel.data('items-tablet') !== undefined) ? $carousel.data('items-tablet') : 1;
    var $items_mobile_landscape = ($carousel.data('items-mobile-landscape') !== undefined) ? $carousel.data('items-mobile-landscape') : 1;
    var $items_mobile_portrait = ($carousel.data('items-mobile-portrait') !== undefined) ? $carousel.data('items-mobile-portrait') : 1;
    $carousel.owlCarousel ({
      loop : ($carousel.data('loop') !== undefined) ? $carousel.data('loop') : true,
      items : $carousel.data('items'),
      margin : ($carousel.data('margin') !== undefined) ? $carousel.data('margin') : 0,
      dots : ($carousel.data('dots') !== undefined) ? $carousel.data('dots') : true,
      nav : ($carousel.data('nav') !== undefined) ? $carousel.data('nav') : false,
      navText : ["<div class='slider-no-current'><span class='current-no'></span><span class='total-no'></span></div><span class='current-monials'></span>", "<div class='slider-no-next'></div><span class='next-monials'></span>"],
      autoplay : ($carousel.data('autoplay') !== undefined) ? $carousel.data('autoplay') : false,
      autoplayTimeout : ($carousel.data('autoplay-timeout') !== undefined) ? $carousel.data('autoplay-timeout') : 5000,
      animateIn : ($carousel.data('animatein') !== undefined) ? $carousel.data('animatein') : false,
      animateOut : ($carousel.data('animateout') !== undefined) ? $carousel.data('animateout') : false,
      mouseDrag : ($carousel.data('mouse-drag') !== undefined) ? $carousel.data('mouse-drag') : true,
      autoWidth : ($carousel.data('auto-width') !== undefined) ? $carousel.data('auto-width') : false,
      autoHeight : ($carousel.data('auto-height') !== undefined) ? $carousel.data('auto-height') : false,
      center : ($carousel.data('center') !== undefined) ? $carousel.data('center') : false,
      responsiveClass: true,
      dotsEachNumber: true,
      smartSpeed: 600,
      autoplayHoverPause: true,
      responsive : {
        0 : {
          items : $items_mobile_portrait,
        },
        576 : {
          items : $items_mobile_landscape,
        },
        992 : {
          items : $items_tablet,
        },
        1200 : {
          items : $items,
        }
      }
    });
    var totLength = $('.owl-dot', $carousel).length;
    $('.total-no', $carousel).html(totLength);
    $('.current-no', $carousel).html(totLength);
    $carousel.owlCarousel();
    $('.current-no', $carousel).html(1);
    $carousel.on('changed.owl.carousel', function(event) {
      var total_items = event.page.count;
      var currentNum = event.page.index +1;
      $('.total-no', $carousel ).html(total_items);
      $('.current-no', $carousel).html(currentNum);
    });
  });
});