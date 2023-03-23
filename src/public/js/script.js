/* eslint func-names: ["error", "never"] */
(function () {
  if (document.querySelector('.date-picker')) {
    const datepicker = document.querySelector('.date-picker');
    /* eslint no-undef: 0 */
    flatpickr(datepicker, {
      locale: 'ja',
      disableMobile: 'true',
    });
  }

  if (document.querySelector('.datetime-picker')) {
    const datepicker = document.querySelector('.datetime-picker');
    /* eslint no-undef: 0 */
    flatpickr(datepicker, {
      locale: 'ja',
      enableTime: true,
      dateFormat: 'Y-m-d H:i',
      disableMobile: 'true',
    });
  }

  if (document.querySelector('.datetime-picker_ymdhi')) {
    const datepicker = document.querySelector('.datetime-picker_ymdhi');
    /* eslint no-undef: 0 */
    const config = {
      locale: 'ja',
      dateFormat: 'Y-m-d',
      disableMobile: 'true',
    };

    flatpickr(datepicker, config);

    document
      .querySelector('.datetime-clear')
      .addEventListener('click', function () {
        datepicker.flatpickr().clear();
        flatpickr(datepicker, config);
      });
  }

  if (document.querySelector('.date-picker_ymd')) {
    const datepickerymd = document.querySelector('.date-picker_ymd');
    /* eslint no-undef: 0 */
    flatpickr(datepickerymd, {
      locale: 'ja',
      altFormat: 'Y-m-d',
      disableMobile: 'true',
    });
  }

  if (document.querySelector('.date-picker_ymd1')) {
    const datepickerymd = document.querySelector('.date-picker_ymd1');
    /* eslint no-undef: 0 */
    flatpickr(datepickerymd, {
      wrap: true,
      locale: 'ja',
      altFormat: 'Y-m-d',
      disableMobile: 'true',
    });
  }

  if (document.querySelector('.date-picker_ymd2')) {
    const datepickerymd = document.querySelector('.date-picker_ymd2');
    /* eslint no-undef: 0 */
    flatpickr(datepickerymd, {
      wrap: true,
      locale: 'ja',
      altFormat: 'Y-m-d',
      disableMobile: 'true',
    });
  }

  if (document.querySelector('.date-picker_ymd3')) {
    const datepickerymd = document.querySelector('.date-picker_ymd3');
    /* eslint no-undef: 0 */
    flatpickr(datepickerymd, {
      wrap: true,
      locale: 'ja',
      altFormat: 'Y-m-d',
      disableMobile: 'true',
    });
  }

  if (document.querySelector('.date-picker_ym')) {
    const datepickerym = document.querySelector('.date-picker_ym');

    /* eslint no-undef: 0 */
    flatpickr(datepickerym, {
      locale: 'ja',
      static: true,
      altInput: true,
      dateFormat: 'Y\\年m\\月',
      disableMobile: 'true',
      plugins: [
        new monthSelectPlugin({
          shorthand: false,
          dateFormat: 'Y-m',
          altFormat: 'Y\\年m\\月',
        }),
      ],
      onReady: function (dObj, dStr, fp) {
        fp.calendarContainer.classList.add('year-month-dp');
      },
    });
  }

  if (document.querySelector('.datepicker_ymd_start')) {
    const datepickerEl = document.querySelector('.datepicker_ymd_start');

    /* eslint no-undef: 0 */
    flatpickr(datepickerEl, {
      locale: 'ja',
      altInput: true,
      altFormat: 'y-m-d',
      dateFormat: 'Y-m-d',
      disableMobile: 'true',
    });
  }

  if (document.querySelector('.datepicker_ymd_end')) {
    const datepickerEl = document.querySelector('.datepicker_ymd_end');

    /* eslint no-undef: 0 */
    flatpickr(datepickerEl, {
      locale: 'ja',
      altInput: true,
      altFormat: 'y-m-d',
      dateFormat: 'Y-m-d',
      disableMobile: 'true',
    });
  }

  if (document.querySelector('.datepicker2_ymd_start')) {
    const datepickerEl2 = document.querySelector('.datepicker2_ymd_start');

    /* eslint no-undef: 0 */
    flatpickr(datepickerEl2, {
      locale: 'ja',
      altInput: true,
      altFormat: 'y-m-d',
      dateFormat: 'Y-m-d',
      disableMobile: 'true',
    });
  }

  if (document.querySelector('.datepicker2_ymd_end')) {
    const datepickerEl2 = document.querySelector('.datepicker2_ymd_end');

    /* eslint no-undef: 0 */
    flatpickr(datepickerEl2, {
      locale: 'ja',
      altInput: true,
      altFormat: 'y-m-d',
      dateFormat: 'Y-m-d',
      disableMobile: 'true',
    });
  }

  if (document.getElementById('register-present_address_nationality')) {
    const presentAddressPrefecture = document.querySelector(
      '.select-present-address-prefecture'
    );
    document.getElementById(
      'register-present_address_nationality'
    ).style.display = presentAddressPrefecture.value === '0' ? 'block' : 'none';
    presentAddressPrefecture.addEventListener('change', (event) => {
      if (event.target.value === '0') {
        document.getElementById(
          'register-present_address_nationality'
        ).style.display = 'block';
      } else {
        document.getElementById('present_address_nationality').value = '';
        document.getElementById(
          'register-present_address_nationality'
        ).style.display = 'none';
      }
    });
  }

  if (document.querySelector('.password-toggle-indicator')) {
    const passwordToggleCheck = document.querySelector(
      '.password-toggle-indicator'
    );
    passwordToggleCheck.addEventListener('click', () => {
      const passwordToggle = document.getElementById('password_toggle');
      if (passwordToggle) {
        if (passwordToggle.type === 'password') {
          passwordToggle.type = 'text';
        } else {
          passwordToggle.type = 'password';
        }
      }
    });
  }

  if (document.getElementById('count-neo_self_introduce')) {
    const neoSelfIntroduce = document.getElementById(
      'textarea-neo_self_introduce'
    );
    document.getElementById('count-neo_self_introduce').innerHTML =
      neoSelfIntroduce.value.length;
  }

  if (document.getElementById('textarea-neo_self_introduce')) {
    document.getElementById('textarea-neo_self_introduce').onkeyup =
      function () {
        document.getElementById('count-neo_self_introduce').innerHTML =
          this.value.length;
      };
  }
  if (document.querySelector('.start-date')) {
    const datepicker = document.querySelector('.start-date');
    let click = 0;
    /* eslint no-undef: 0 */
    flatpickr(datepicker, {
      mode: 'range',
      minDate: 'today',
      locale: 'ja',
      dateFormat: 'Y\\年m\\月',
      disableMobile: 'true',
      plugins: [new rangePlugin({ input: '#end-date' })],
    });
  }
  if (document.querySelector('.expiration-startDate')) {
    const datepicker = document.querySelector('.expiration-startDate');
    let click = 0;
    /* eslint no-undef: 0 */
    flatpickr(datepicker, {
      mode: 'range',
      minDate: 'today',
      locale: 'ja',
      dateFormat: 'Y\\年m\\月',
      disableMobile: 'true',
      plugins: [new rangePlugin({ input: '#expiration-endtDate' })],
    });
  }
  if (document.querySelector('.btn--next')) {
    let el = document.querySelector('.nav--horizontal');

    if (el.scrollLeft === 0) {
      document.querySelector('.btn--prev').style.display = 'none';
      document.querySelector('.btn--horizontal').style.justifyContent = 'end';
    }
    document.querySelector('.btn--next').onclick = function () {
      el.scrollLeft += 100;
      if (el.scrollLeft === 0) {
        document.querySelector('.btn--prev').style.display = 'none';
        document.querySelector('.btn--horizontal').style.justifyContent = 'end';
      } else {
        document.querySelector('.btn--prev').style.display = 'block';
      }
      document.querySelector('.btn--horizontal').style.justifyContent =
        'space-between';
      if (Math.abs(el.scrollLeft) === el.scrollWidth - el.clientWidth) {
        document.querySelector('.btn--next').style.display = 'none';
      } else {
        document.querySelector('.btn--next').style.display = 'block';
      }
    };
  }
  if (document.querySelector('.btn--prev')) {
    let el = document.querySelector('.nav--horizontal');
    document.querySelector('.btn--prev').onclick = function () {
      el.scrollLeft -= 100;
      if (el.scrollLeft === 0) {
        document.querySelector('.btn--prev').style.display = 'none';
        document.querySelector('.btn--horizontal').style.justifyContent = 'end';
      } else {
        document.querySelector('.btn--next').style.display = 'block';
      }
    };
  }

  if (document.querySelector('.classsified-create-price')) {
    // Disable dot in price input
    $('.classsified-create-price').keydown((e) => {
      if (e.keyCode === 190 || e.keyCode === 110) {
        e.preventDefault();
      }
    });
  }
})();
