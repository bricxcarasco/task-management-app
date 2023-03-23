/** Common JS file */
import BpheroConfig from './config/bphero';
import DefaultImageCategory from './enums/DefaultImageCategory';

export default {
  /**
   * Construct validation error messages
   *
   * @param {object} errors
   * @return {object}
   */
  constructValidationErrors(errors) {
    const validationErrors = [];

    Object.keys(errors).forEach((key) => {
      /* eslint-disable prefer-destructuring */
      validationErrors[key] = [errors[key][0]];
    });

    return { ...validationErrors };
  },

  /**
   * Clear flatpicker YYYY-MM value
   *
   * @param {HTMLElement} datepicker
   * @return {void}
   */
  clearFlatpickrYearMonthValue(datepicker) {
    /* eslint no-undef: 0 */
    datepicker
      .flatpickr({
        locale: 'ja',
        static: true,
        altInput: true,
        dateFormat: 'Y\\年m\\日',
        disableMobile: 'true',
        plugins: [
          /* eslint new-cap: ["error", { "newIsCap": false }] */
          new monthSelectPlugin({
            shorthand: false,
            dateFormat: 'Y-m',
            altFormat: 'Y\\年m\\日',
          }),
        ],
        onReady(dObj, dStr, fp) {
          fp.calendarContainer.classList.add('year-month-dp');
        },
      })
      .clear();
  },

  /**
   * Flatpicker set default date for start & end dates
   *
   * @param {HTMLElement} datepicker
   * @param {mixed} date
   * @return {void}
   */
  setFlatpickrDateRange(datepicker, date) {
    /* eslint no-undef: 0 */
    datepicker
      .flatpickr({
        locale: 'ja',
        dateFormat: 'Y-m-d',
        disableMobile: 'true',
      })
      .setDate(date, true, 'Y-m-d');
  },

  /**
   * Fill form with object
   *
   * @param {HTMLElement} form - form being filled
   * @param {array} data - data that will be used to fill the form
   * @returns {void}
   */
  fillForm(form, data) {
    Object.keys(data).forEach((key) => {
      const field = form.querySelectorAll(`[name="${key}"]`);
      /* eslint-disable no-plusplus */
      for (let counter = 0; counter < field.length; counter++) {
        if (field[counter].getAttribute('type') === 'radio') {
          if (parseInt(field[counter].value, 10) === data[key]) {
            field[counter].checked = true;
            break;
          }
          /* eslint-disable no-continue */
          continue;
        }
        field[counter].value = data[key];
      }
    });
  },

  /**
   * Hard Reset Form
   *
   * @param {HTMLElement} form - form being filled
   * @returns {void}
   */
  hardResetForm(form) {
    form.reset();
    const fields = form.querySelectorAll('input[type=hidden]');

    for (let i = 0; i < fields.length; i += 1) {
      fields[i].value = '';
    }
  },

  /**
   * Construct date attribute
   *
   * @param {object} date
   * @param {string|null} separator
   * @returns {string}
   */
  constructDate(date, separator = null) {
    if (separator === null) {
      return (
        date.getFullYear() +
        `0${date.getMonth() + 1}`.slice(-2) +
        `0${date.getDate()}`.slice(-2)
      );
    }

    return (
      date.getFullYear() +
      separator +
      `0${date.getMonth() + 1}`.slice(-2) +
      separator +
      `0${date.getDate()}`.slice(-2)
    );
  },

  /**
   * Handle image render error
   * @param {event} event
   * @param {object} imageCategory
   */
  handleNotFoundImageException(event, imageCategory) {
    const imageElement = event;
    let image = null;

    switch (imageCategory) {
      case DefaultImageCategory.RIO_NEO:
        image = BpheroConfig.DEFAULT_IMG;
        break;
      case DefaultImageCategory.CLASSIFIED:
        image = BpheroConfig.DEFAULT_PRODUCT_IMG;
        break;
      case DefaultImageCategory.FORM:
      case DefaultImageCategory.DOCUMENT_MANAGEMENT:
        image = BpheroConfig.DEFAULT_NO_IMG;
        break;
      default:
        break;
    }

    imageElement.target.src = `${image}?${new Date().getTime()}`;
  },
};
