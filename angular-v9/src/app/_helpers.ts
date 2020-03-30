import { environment } from 'src/environments/environment';

/*
 * Js Helpers:
 *
 * correctHeight() - fix the height of main wrapper
 * detectBody() - detect windows size
 * smoothlyMenu() - add smooth fade in/out on navigation show/hide
 */
declare var jQuery: any;

export function correctHeight() {
  const pageWrapper = jQuery('#page-wrapper');
  const navbarHeight = jQuery('nav.navbar-default').height();
  const wrapperHeigh = pageWrapper.height();
  if (navbarHeight > wrapperHeigh) {
    pageWrapper.css('min-height', navbarHeight + 'px');
  }
  if (navbarHeight < wrapperHeigh) {
    if (navbarHeight < jQuery(window).height()) {
      pageWrapper.css('min-height', jQuery(window).height() + 'px');
    } else {
      pageWrapper.css('min-height', navbarHeight + 'px');
    }
  }
  if (jQuery('body').hasClass('fixed-nav')) {
    if (navbarHeight > wrapperHeigh) {
      pageWrapper.css('min-height', navbarHeight + 'px');
    } else {
      pageWrapper.css('min-height', jQuery(window).height() - 60 + 'px');
    }
  }
}

export function detectBody() {
  if (jQuery(document).width() < 769) {
    jQuery('body').addClass('body-small');
  } else {
    jQuery('body').removeClass('body-small');
  }
}

export function smoothlyMenu() {
  if (!jQuery('body').hasClass('mini-navbar') || jQuery('body').hasClass('body-small')) {
    // Hide menu in order to smoothly turn on when maximize menu
    jQuery('#side-menu').hide();
    // For smoothly turn on menu
    setTimeout(
      function () {
        jQuery('#side-menu').fadeIn(400);
      }, 200);
  } else if (jQuery('body').hasClass('fixed-sidebar')) {
    jQuery('#side-menu').hide();
    setTimeout(
      function () {
        jQuery('#side-menu').fadeIn(400);
      }, 100);
  } else {
    // Remove all inline style from jquery fadeIn function to reset menu state
    jQuery('#side-menu').removeAttr('style');
  }
}

/**
 * Write Log to console on Dev Mod:
 * @param pars Message
 */
export function consoleLog(pars) {
  if (!environment.production) {
    console.log(pars); // IE not support apply
  }
}

/**
 * Write Log to console on Dev Mod:
 * @param pars Message
 */
export function consoleErr(pars) {
  if (!environment.production) {
    console.error(pars); // IE not support apply
  }
}

export function bodauTiengViet(str) {
  str = str.toLowerCase();
  str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, 'a');
  str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, 'e');
  str = str.replace(/ì|í|ị|ỉ|ĩ/g, 'i');
  str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, 'o');
  str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, 'u');
  str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, 'y');
  str = str.replace(/đ/g, 'd');
  return str;
}
