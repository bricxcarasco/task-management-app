/**
 * Objectify Form
 *
 * Converts a form inputs into an object/key-value data
 *
 * @param {Node} form
 * @return {Object}
 */

import { serializeFormArray } from './serializeFormArray';

/* eslint-disable import/prefer-default-export */
export const objectifyForm = (form) => {
  const formArray = serializeFormArray(form);
  const formObject = {};
  for (let i = 0; i < formArray.length; i += 1) {
    let fieldName = formArray[i].name;

    if (fieldName.includes('[]')) {
      // Handle array fields
      fieldName = fieldName.replace('[]', '');
      formObject[fieldName] = formObject[fieldName] ?? [];
      formObject[fieldName].push(formArray[i].value);
    } else {
      // Simple assignment
      formObject[fieldName] = formArray[i].value;
    }
  }

  return formObject;
};
