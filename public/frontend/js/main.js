// ==================================================
// * Project Name   :  Qxpay API Documentation
// * File           :  JS Base
// * Version        :  1.0
// * Last change    :  1 november 2025, Saturday
// * Author         :  tdevs (https://codecanyon.net/user/tdevs/portfolio)
// ==================================================

(function ($) {
  'use strict';

  // Sidebar Toggle
  $(".offcanvas-close, .offcanvas-overlay").on("click", function () {
    $(".offcanvas-area").removeClass("info-open");
    $(".offcanvas-overlay").removeClass("overlay-open");
    $("body").removeClass("no-scroll");
  });

  $(".sidebar-toggle").on("click", function () {
    $(".offcanvas-area").addClass("info-open");
    $(".offcanvas-overlay").addClass("overlay-open");
    $("body").addClass("no-scroll");
  });

  // Initialize mobile-menu
  if ($("#mobile-menu").length > 0) {
    $("#mobile-menu").meanmenu({
      meanMenuContainer: ".mobile-menu-container",
      meanScreenWidth: "991",
      meanExpand: ['<i class="fa-regular fa-angle-right"></i>'],
    });
  }

  // Remove sidebar on screen resize > 991px
  $(window).on("resize", function () {
    if ($(window).width() > 991) {
      $(".offcanvas-area").removeClass("info-open");
      $(".offcanvas-overlay").removeClass("overlay-open");
      $("body").removeClass("no-scroll");
    }
  });

  // Body overlay Js
  $(".body-overlay").on("click", function () {
    $(".offcanvas-area").removeClass("opened");
    $(".body-overlay").removeClass("opened");
  });

  // Header sticky
  $(window).scroll(function () {
    if ($(this).scrollTop() > 250) {
      $("#header-sticky").addClass("active-sticky");
    } else {
      $("#header-sticky").removeClass("active-sticky");
    }
  });

  // Back to top js  
  if ($(".back-to-top-wrap path").length > 0) {
    var progressPath = document.querySelector(".back-to-top-wrap path");
    var pathLength = progressPath.getTotalLength();
    progressPath.style.transition = progressPath.style.WebkitTransition =
      "none";
    progressPath.style.strokeDasharray = pathLength + " " + pathLength;
    progressPath.style.strokeDashoffset = pathLength;
    progressPath.getBoundingClientRect();
    progressPath.style.transition = progressPath.style.WebkitTransition =
      "stroke-dashoffset 10ms linear";
    var updateProgress = function () {
      var scroll = $(window).scrollTop();
      var height = $(document).height() - $(window).height();
      var progress = pathLength - (scroll * pathLength) / height;
      progressPath.style.strokeDashoffset = progress;
    };
    updateProgress();
    $(window).scroll(updateProgress);
    var offset = 150;
    var duration = 550;
    jQuery(window).on("scroll", function () {
      if (jQuery(this).scrollTop() > offset) {
        jQuery(".back-to-top-wrap").addClass("active-progress");
      } else {
        jQuery(".back-to-top-wrap").removeClass("active-progress");
      }
    });
    jQuery(".back-to-top-wrap").on("click", function (event) {
      event.preventDefault();
      jQuery("html, body").animate({
        scrollTop: 0
      }, duration);
      return false;
    });
  }

  // Apply background image from 'data-background' attribute
  $("[data-background]").each(function () {
    let bg = $(this).attr("data-background");
    if (bg) {
      $(this).css("background-image", `url(${bg})`);
    }
  });

  // Set width from 'data-width' attribute
  $("[data-width]").each(function () {
    let width = $(this).attr("data-width");
    if (width) {
      $(this).css("width", width);
    }
  });

  // Apply background color from 'data-bg-color' attribute
  $("[data-bg-color]").each(function () {
    let bgColor = $(this).attr("data-bg-color");
    if (bgColor) {
      $(this).css("background-color", bgColor);
    }
  });

})(jQuery);