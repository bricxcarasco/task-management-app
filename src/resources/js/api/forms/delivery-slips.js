import axios from 'axios';
import ApiService from '../api';

export default class DeliverySlipApiService extends ApiService {
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
    this.baseUrl = '/api/forms/delivery-slips';
  }

  /**
   * Get Delivery Slip list
   *
   * Method: Patch
   * Endpoint: /api/forms/delivery-slips/delivery-slips-lists *
   * @param {*} data Request data
   * @returns {Promise}
   */
  getLists(data) {
    return axios.patch(`${this.baseUrl}/delivery-slip-lists`, data, {
      headers: this.headers,
    });
  }

  /**
   * Validation of create delivery slip in form service
   *
   * Method: POST
   * Endpoint: /api/forms/delivery-slips
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  validateCreateDeliverySlipForm(data) {
    return axios.post(`${this.baseUrl}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Confirm delivery slip form
   *
   * Method: POST
   * Endpoint: /api/forms/delivery-slips/confirm
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  confirmDeliverySlip(data) {
    return axios.post(`${this.baseUrl}/confirm`, data, {
      headers: this.headers,
    });
  }

  /**
   * Update delivery slip form
   *
   * Method: POST
   * Endpoint: /api/forms/delivery-slips/update/{form}
   *
   * @param {*} data Request data
   * @param int form form_id
   * @returns {Promise}
   */
  updateDeliverySlip(data, form) {
    return axios.post(`${this.baseUrl}/update/${form}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Validate form search inputs
   *
   * Method: POST
   * Endpoint: /api/forms/quotations/validate-delivery-slip-search
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  validateSearchInputs(data) {
    return axios.post(`${this.baseUrl}/validate-delivery-slip-search`, data, {
      headers: this.headers,
    });
  }

  /**
   * Get image
   *
   * Method: Patch
   * Endpoint: /api/forms/delivery-slips/restore
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  getImage() {
    return axios.get(`${this.baseUrl}/restore`, {
      headers: this.headers,
    });
  }

  /**
   * Delete delivery slip
   *
   * Method: Delete
   * Endpoint: /api/forms/delivery-slips/delete/{id}
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
