import axios from 'axios';
import ApiService from '../api';

export default class QuotationApiService extends ApiService {
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
    this.baseUrl = '/api/forms/quotations';
  }

  /**
   * Get Quotation list
   *
   * Method: Patch
   * Endpoint: /api/forms/quotations/quotation-lists
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  getLists(data) {
    return axios.patch(`${this.baseUrl}/quotation-lists`, data, {
      headers: this.headers,
    });
  }

  /**
   * Validate form search inputs
   *
   * Method: POST
   * Endpoint: /api/forms/quotations/validate-quotation-search
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  validateSearchInputs(data) {
    return axios.post(`${this.baseUrl}/validate-quotation-search`, data, {
      headers: this.headers,
    });
  }

  /**
   * Delete quotation
   *
   * Method: Delete
   * Endpoint: /api/forms/quotations/delete/{id}
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
