import axios from 'axios';
import ApiService from '../api';

export default class ClassifiedContactsApiService extends ApiService {
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
    this.baseUrl = '/api/classifieds/contacts';
  }

  /**
   * Send inquiry/message from buyer to seller
   *
   * Method: POST
   * Endpoint: /api/classifieds/contacts/send-inquiry
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  sendInquiry(data) {
    return axios.post(`${this.baseUrl}/send-inquiry`, data, {
      headers: this.headers,
    });
  }
}
