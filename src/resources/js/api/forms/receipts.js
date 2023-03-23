import axios from 'axios';
import ApiService from '../api';

export default class ReceiptApiService extends ApiService {
  /**
   * Constructor function
   *
   * @returns {void}
   */
  constructor() {
    super();

    /**
     * Base Url
     * @var {string}
     */
    this.baseUrl = '/api/forms/receipts';
  }

  /**
   * Get Receipt list
   *
   * Method: Patch
   * Endpoint: /api/forms/quotations/receipts-lists
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  getLists(data) {
    return axios.patch(`${this.baseUrl}/receipt-lists`, data, {
      headers: this.headers,
    });
  }

  /**
   * Validate form search inputs
   *
   * Method: POST
   * Endpoint: /api/forms/receipts/validate-receipts-search
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  validateSearchInputs(data) {
    return axios.post(`${this.baseUrl}/validate-receipt-search`, data, {
      headers: this.headers,
    });
  }

  /**
   * Validation of create receipt in form service
   *
   * Method: POST
   * Endpoint: /api/forms/receipts
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  validateCreateReceiptForm(data) {
    return axios.post(`${this.baseUrl}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Confirm receipt form
   *
   * Method: POST
   * Endpoint: /api/forms/receipts/confirm
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  confirmReceipt(data) {
    return axios.post(`${this.baseUrl}/confirm`, data, {
      headers: this.headers,
    });
  }

  /**
   * Update receipt in form service
   *
   * Method: POST
   * Endpoint: /api/forms/receipts/update/{form}
   *
   * @param {*} data Request data
   * @param int form form_id
   * @returns {Promise}
   */
  updateReceipt(data, form) {
    return axios.post(`${this.baseUrl}/update/${form}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Delete receipt
   *
   * Method: Delete
   * Endpoint: /api/forms/receipts/delete/{id}
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  deleteForms(id) {
    return axios.delete(`${this.baseUrl}/delete/${id}`, {
      headers: this.headers,
    });
  }
}
