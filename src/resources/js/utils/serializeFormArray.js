/**
 * Serialize Form Array
 *
 * @param {Node} form - form containing data to be converted into an object
 * @return {array}
 */
/* eslint-disable import/prefer-default-export */
export const serializeFormArray = (form) => {
  // Setup our serialized data
  const serialized = [];

  // Loop through each field in the form
  for (let i = 0; i < form.elements.length; i += 1) {
    const field = form.elements[i];

    // Don't serialize fields without a name, submits, buttons, file and reset inputs, and disabled fields
    if (
      !field.name ||
      field.disabled ||
      field.type === 'file' ||
      field.type === 'reset' ||
      field.type === 'submit' ||
      field.type === 'button'
    ) {
      /* eslint-disable no-continue */
      continue;
    }

    // If a multi-select, get all selections
    if (field.type === 'select-multiple') {
      for (let n = 0; n < field.options.length; n += 1) {
        if (!field.options[n].selected) {
          /* eslint-disable no-continue */
          continue;
        }
        serialized.push({
          name: field.name,
          value: field.options[n].value,
        });
      }
    }

    // Convert field data to a query string
    else if (
      (field.type !== 'checkbox' && field.type !== 'radio') ||
      field.checked
    ) {
      serialized.push({
        name: field.name,
        value: field.value,
      });
    }
  }

  return serialized;
};
