(function($) {
	'use strict';

  $(document).ready(function() {
     document.getElementsByTagName("body")[0].style.visibility = "visible";
   });

  // Add class on add-car-page
  $(function() {
    var loc = window.location.href; // returns the full URL
    if(/add/.test(loc)) {
      $('body').addClass('add-car-page');
    }
  });
  // Add class on about-us
  $(function() {
    var loc = window.location.href; // returns the full URL
    if(/about-us/.test(loc)) {
      $('body').addClass('about-us');
    }
  });
  // Add class on contact-us
  $(function() {
    var loc = window.location.href; // returns the full URL
    if(/contact-us/.test(loc)) {
      $('body').addClass('contact-us');
    }
  });

  // Wrappers for add car form

  // input fields START
  $("#edit-field-car-type-wrapper,#edit-field-vehicle-category-wrapper,#edit-field-car-brand-wrapper").wrapAll("<div class='form-3col-wraper1'/>");

  $("#edit-field-car-model-year-wrapper,#edit-field-car-assembly-wrapper,#edit-field-body-ty-wrapper").wrapAll("<div class='form-3col-wraper2'/>");

  $("#edit-field-registered-city-wrapper,#edit-field-transmission-wrapper,#edit-field-car-wrapper").wrapAll("<div class='form-3col-wraper3'/>");

  $("#edit-field-millage-wrapper,#edit-field-engine-capacity-wrapper,#edit-field-fue-wrapper").wrapAll("<div class='form-3col-wraper4'/>");
  // checkboxes START

  $(".js-form-item-field-additional-equipment-1,.js-form-item-field-additional-equipment-2,.js-form-item-field-additional-equipment-3,.js-form-item-field-additional-equipment-4,.js-form-item-field-additional-equipment-5").wrapAll("<div class='checkbox-3col-wraper1'/>");

  $(".js-form-item-field-additional-equipment-59,.js-form-item-field-additional-equipment-6,.js-form-item-field-additional-equipment-7,.js-form-item-field-additional-equipment-8,.js-form-item-field-additional-equipment-9").wrapAll("<div class='checkbox-3col-wraper2'/>");

  $(".js-form-item-field-additional-equipment-57,.js-form-item-field-additional-equipment-55,.js-form-item-field-additional-equipment-58,.js-form-item-field-additional-equipment-10,.js-form-item-field-additional-equipment-56").wrapAll("<div class='checkbox-3col-wraper3'/>");

  // input fields START

  $("#edit-field-price-wrapper,#edit-field-location-wrapper").wrapAll("<div class='input-wraper-form1'/>");

  $("#edit-field-city-wrapper,#edit-field-state-wrapper,#edit-field-country-wrapper").wrapAll("<div class='input-wraper-form2'/>");

  // user login-register page START

  $("#block-used-cars-local-tasks,#block-used-cars-content").wrapAll("<div class='user-login-page-wraper'/>");


  // ------------------------------------------------------
  if (window.location.pathname === "/user/register"){
    $(".form-email").attr("placeholder", "Email");
    $(".username").attr("placeholder","Username");
    $(".password-field").attr("placeholder","Password");
    $(".password-confirm").attr("placeholder","Confirm Password");
    $("label").css("display","none");
    $("#edit-contact").css("display","none");
    $("#edit-mail--description").css("display","none");
    $("#edit-name--description").css("display","none");
  }

  if (window.location.pathname === "/node/add/car") {
    $('#block-used-cars-page-title h2').text("Post A Car");
  }

  // ------------------------------------------
  // latest,featured,popular cars slider START
  // ------------------------------------------

  (function() {

    // store the slider in a local variable
    var $window = $(window),
        flexslider = { vars:{} };

    // tiny helper function to add breakpoints
    function getGridSize() {
      return (window.innerWidth < 768) ? 1 :
          (window.innerWidth < 992) ? 2 :
          (window.innerWidth > 992) ? 4 : 4;
    }

    $window.ready(function() {
      $('.block-featured-cars-block .flexslider,.block-latest-cars-block .flexslider, .block-popular-cars-block .flexslider').flexslider({
        animation: "slide",
        animationLoop: false,
        minItems: getGridSize(), // use function to pull in initial value
        maxItems: getGridSize(), // use function to pull in initial value
        slideshow: true,
        itemWidth: 270,
        itemMargin: 25,
        startAt: 0,
        move: 1,
        animationSpeed: 300,
        customDirectionNav: $("prev","next"),
        touch: true
      });
    });

    // check grid size on resize event

    $window.resize(function() {
      var gridSize = getGridSize();

      flexslider.vars.minItems = gridSize;
      flexslider.vars.maxItems = gridSize;
    });
  }());
  // ----------------------------------------
  // latest,featured,popular cars slider END
  // ----------------------------------------


  // -------------------------
  // testimonials slider START
  // -------------------------

  (function() {

    // store the slider in a local variable
    var $window = $(window),
        flexslider = { vars:{} };

    // tiny helper function to add breakpoints
    function getGridSize() {
      return (window.innerWidth < 768) ? 1 :
          (window.innerWidth < 992) ? 2 :
              (window.innerWidth > 992) ? 3 : 3;
    }

    $window.ready(function() {
      $('.block-testimonials-block .flexslider').flexslider({
        animation: "slide",
        animationLoop: false,
        minItems: getGridSize(), // use function to pull in initial value
        maxItems: getGridSize(), // use function to pull in initial value
        slideshow: true,
        itemWidth: 300,
        itemMargin: 10,
        startAt: 0,
        move: 1,
        animationSpeed: 300,
        customDirectionNav: $("prev","next"),
        touch: true
      });
    });

    // check grid size on resize event
    $window.resize(function() {
      var gridSize = getGridSize();

      flexslider.vars.minItems = gridSize;
      flexslider.vars.maxItems = gridSize;
    });
  }());
  // ------------------------
  // testimonials slider END
  // ------------------------

  $('.form-managed-file >.image-widget-data').addClass('fix');


  $('.list-button').click(function(){
    $('.search-results-content').removeClass("search-grid");
  });

  $('.grid-button').click(function(){
    $('.search-results-content').addClass("search-grid");
  });

  // ===== Scroll to Top ====
  $(window).scroll(function() {
    if ($(this).scrollTop() >=300) {
      $('.backtotop').fadeIn(200);
    } else {
      $('.backtotop').fadeOut(200);
    }
  });
  $('.backtotop').click(function() {
    $('body,html').animate({
      scrollTop : 0
    }, 500);
  });

  //Navigation hamburger main menu / toggle classes

  $(function() {
    $('.navbar-toggle').click(function(e) {
      $(this).toggleClass('reactive');
      $('#block-used-cars-main-menu').slideToggle(100).toggleClass('reactive');

      e.preventDefault();
    });
  });

  if (window.location.pathname.indexOf("/blog/") === 0) {
    $('h2.comment-form__title').text("Leave Your Comments");
  }

  if (window.location.pathname.indexOf("/blog/") === 0) {
    $(".button--primary").attr("value", "SEND");
    $(".comment-comment-form label").css("display","none");
    $(".comment-comment-form .form-text").attr("placeholder","Name");
  }

  if( !$("body").hasClass( "user-logged-in" ) ) {
    $("#cke_1_top, #edit-comment-body-0-format").hide();
  }

  if( $("body").hasClass( "user-logged-in" ) ) {
    $("#edit-field-comment-email-0-value").hide();
  }

  if (window.location.pathname.indexOf("/blog/") === 0) {
    $('#block-used-cars-page-title h2').text("Blog Detail");
  }


})(jQuery);