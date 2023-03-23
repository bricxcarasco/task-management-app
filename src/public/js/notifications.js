// Notifications JS

$(document).ready(function () {
  /**
   * Read notification handler
   */
  $('.js-read-notif').on('click', function (event) {
    event.preventDefault();
    let url = $(this).data('url');

    $.ajax({
      type: 'GET',
      dataType: 'json',
      url,
      beforeSend: function (request) {
        let csrfToken = $('meta[name="csrf-token"]').attr('content');
        let lang = $('html').attr('lang');
        request.setRequestHeader('X-CSRF-TOKEN', csrfToken);
        request.setRequestHeader('Accept-Language', lang);
      },
      success: function (response) {
        if (response.data.redirect_url !== null) {
          window.location.href = response.data.redirect_url;
        }
      },
      error: function (error) {
        console.error(error);
      },
    });
  });
});
