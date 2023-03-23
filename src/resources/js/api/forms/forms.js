import axios from 'axios';
import ApiService from '../api';

export default class FormApiService extends ApiService {
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
    this.baseUrl = '/api/forms';
  }

  /**
   * Validation of quotation product in form service
   *
   * Method: POST
   * Endpoint: /api/forms/product/validate
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  validateQuotationProduct(data) {
    return axios.post(`${this.baseUrl}/product/validate`, data, {
      headers: this.headers,
    });
  }

  /**
   * Validation of create quotation in form service
   *
   * Method: POST
   * Endpoint: /api/forms/quotations
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  saveCreateQuotationForm(data) {
    return axios.post(`${this.baseUrl}/quotations`, data, {
      headers: this.headers,
    });
  }

  /**
   * Update quotation in form service
   *
   * Method: POST
   * Endpoint: /api/forms/quotations/update/{form}
   *
   * @param {*} data Request data
   * @param int form form_id
   * @returns {Promise}
   */
  updateQuotation(data, form) {
    return axios.post(`${this.baseUrl}/quotations/update/${form}`, data, {
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
    return axios.post(`${this.baseUrl}/receipts`, data, {
      headers: this.headers,
    });
  }

  /**
   * Get Quotation list
   *
   * Method: Patch
   * Endpoint: /api/forms/delete-history
   *
   * @returns {Promise}
   */
  getLists(data) {
    return axios.patch(`${this.baseUrl}/delete-history`, data, {
      headers: this.headers,
    });
  }

  /**
   * Get single form history
   *
   * Method: POST
   * Endpoint: /api/forms/history
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  getFormHistory(data) {
    return axios.post(`${this.baseUrl}/history`, data, {
      headers: this.headers,
    });
  }

  /**
   * Get Update History List
   *
   * Method: Patch
   * Endpoint: /api/forms/update-history
   *
   * @returns {Promise}
   */
  getUpdateHistoryLists(data) {
    return axios.patch(`${this.baseUrl}/update-history`, data, {
      headers: this.headers,
    });
  }

  /**
   * Get Update History List Details
   *
   * Method: Get
   * Endpoint: /api/forms/update-history/{id}
   *
   * @returns {Promise}
   */
  getUpdateHistoryDetails(id) {
    return axios.get(`${this.baseUrl}/update-history/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Create/Update basic settings record
   *
   * Method: POST
   * Endpoint: api/forms/basic-settings/save
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  saveBasicSettings(data) {
    return axios.post(`${this.baseUrl}/basic-settings/save`, data, {
      headers: this.headers,
    });
  }

  /**
   * Save Form Files API
   *
   * Method: POST
   * Endpoint: /api/forms/file
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  saveFormPdfFile(data) {
    return axios.post(`${this.baseUrl}/file`, data, {
      headers: this.headers,
    });
  }
}
