/* eslint func-names: ["error", "never"] */
(function () {
  const datepicker = document.querySelector('.date-picker');
  /* eslint-disable no-undef */
  flatpickr(datepicker, {
    locale: 'ja', // locale for this instance only
    // "dateFormat": "Y\\年m\\月d\\日",
    disableMobile: 'true',
  });
})();
