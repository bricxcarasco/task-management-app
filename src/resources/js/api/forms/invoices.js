import axios from 'axios';
import ApiService from '../api';

export default class InvoiceApiService extends ApiService {
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
    this.baseUrl = '/api/forms/invoices';
  }

  /**
   * Get Invoices list
   *
   * Method: PATCH
   * Endpoint: /api/forms/invoices/invoice-lists
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  getLists(data) {
    return axios.patch(`${this.baseUrl}/invoice-lists`, data, {
      headers: this.headers,
    });
  }

  /**
   * Validate form search inputs
   *
   * Method: POST
   * Endpoint: /api/forms/quotations/validate-invoice-search
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  validateSearchInputs(data) {
    return axios.post(`${this.baseUrl}/validate-invoice-search`, data, {
      headers: this.headers,
    });
  }

  /**
   * Delete invoice
   *
   * Method: DELETE
   * Endpoint: /api/forms/invoices/delete/{id}
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  deleteForms(id) {
    return axios.delete(`${this.baseUrl}/delete/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Validation of create invoice in form service
   *
   * Method: POST
   * Endpoint: /api/forms/invoices
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  validateCreateInvoiceForm(data) {
    return axios.post(`${this.baseUrl}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Confirm invoice form
   *
   * Method: POST
   * Endpoint: /api/forms/invoices/confirm
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  confirmInvoice(data) {
    return axios.post(`${this.baseUrl}/confirm`, data, {
      headers: this.headers,
    });
  }

  /**
   * Update invoice in form service
   *
   * Method: POST
   * Endpoint: /api/forms/invoices/update/{form}
   *
   * @param {*} data Request data
   * @param int form form_id
   * @returns {Promise}
   */
  updateInvoice(data, form) {
    return axios.post(`${this.baseUrl}/update/${form}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Get image
   *
   * Method: GET
   * Endpoint: /api/forms/invoices/restore
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  getImage() {
    return axios.get(`${this.baseUrl}/restore`, {
      headers: this.headers,
    });
  }
}
