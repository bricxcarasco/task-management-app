'use strict';
const pageTopShow = $(window).on('scroll load', () => {
  let trigger = $('.trigger').offset();
  let scrollTop = $(window).scrollTop();
  if (scrollTop > trigger.top) {
    $('.fix-btn').fadeIn();
  } else {
    $('.fix-btn').fadeOut();
  }
});
const windowWidth = $(window).width();
if (windowWidth < 800) {
  $('header').removeClass('show');
}
const headerShow = $(window).on('scroll load', () => {
  let trigger = $('.trigger2').offset();
  let scrollTop = $(window).scrollTop();
  if (scrollTop > trigger.top) {
    $('.show').fadeIn();
  } else {
    $('.show').fadeOut();
  }
});
const toPageTop = $('.to-page-top').click(function () {
  $('html, body').animate(
    {
      scrollTop: 0,
    },
    500
  );
  return false;
});
const toSignup = $('.to-signup').click(function () {
  if (windowWidth < 800) {
    $('header').removeClass('show');
  }
  let signupPosition = $('.free-signup').offset().top - 87.83;
  $('html,body').animate(
    {
      scrollTop: signupPosition,
    },
    {
      queue: false,
    }
  );
});
const toForm = $('.to-form').click(function () {
  window.open('https://hero.ne.jp/contact/', '_blank', 'noopener');
});
const windowResize = $(window).resize(function () {
  let windowWidth = $(window).width();
  if (windowWidth < 800) {
    $('header').removeClass('show');
    $('header').css('display', 'none');
  } else {
    $('header').addClass('show');
    $('header').css('display', 'block');
  }
});
