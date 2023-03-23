/* eslint no-undef: 0 */
$(document).ready(() => {
  /**
   * Check if user is authenticated
   */
  /* eslint-disable func-names */
  const checkAuth = function () {
    setTimeout(() => {
      $.get('/api/auth/check', () => {
        window.location.reload();
      }).fail(() => {
        checkAuth();
      });
    }, 5000);
  };

  /**
   * Attach event listener to check if user submitted form
   */
  $(document).on('submit', '#login-form', () => {
    $('.js-section-loader').removeClass('d-none');
    checkAuth();
  });
});
