// Initialize page on-load event
const pageLoadEvent = document.createEvent('Event');
pageLoadEvent.initEvent('bphero.onload', true, true);

window.addEventListener('load', function () {
  var preloader = document.querySelector('.page-loading');
  preloader.classList.remove('active');

  // Trigger bphero loading event with buffer time
  setTimeout(function () {
    document.dispatchEvent(pageLoadEvent);
  }, 500);

  // Buffer timeout for non-parsed preloader
  setTimeout(function () {
    preloader.classList.add('d-none');
  }, 2000);
});
