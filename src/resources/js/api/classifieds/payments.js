import axios from 'axios';
import ApiService from '../api';

export default class ClassifiedPaymentsApiService extends ApiService {
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
    this.baseUrl = '/api/classifieds/payments';
  }

  /**
   * Issue payments
   *
   * Method: POST
   * Endpoint: /api/classifieds/payments/issuance
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  issueNewPayment(data) {
    return axios.post(`${this.baseUrl}/issuance`, data, {
      headers: this.headers,
    });
  }
}
