import axios from 'axios';
import ApiService from '../api';

export default class PurchaseOrderApiService extends ApiService {
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
    this.baseUrl = '/api/forms/purchase-orders';
  }

  /**
   * Get Purchase Order list
   *
   * Method: PATCH
   * Endpoint: /api/forms/purchase-orders/purchase-order-lists
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  getLists(data) {
    return axios.patch(`${this.baseUrl}/purchase-order-lists`, data, {
      headers: this.headers,
    });
  }

  /**
   * Validate form search inputs
   *
   * Method: POST
   * Endpoint: /api/forms/purchase-orders/validate-purchase-order-search
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  validateSearchInputs(data) {
    return axios.post(`${this.baseUrl}/validate-purchase-order-search`, data, {
      headers: this.headers,
    });
  }

  /**
   * Delete purchase order
   *
   * Method: DELETE
   * Endpoint: /api/forms/purchase-orders/delete/{id}
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
   * Validate purchase order form
   *
   * Method: POST
   * Endpoint: /api/forms/purchase-orders
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  validateCreatePurchaseOrderForm(data) {
    return axios.post(`${this.baseUrl}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Get image
   *
   * Method: GET
   * Endpoint: /api/forms/purchase-orders/restore
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
   * Confirm purchase order form
   *
   * Method: POST
   * Endpoint: /api/forms/purchase-orders/confirm
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  confirmPurchaseOrder(data) {
    return axios.post(`${this.baseUrl}/confirm`, data, {
      headers: this.headers,
    });
  }

  /**
   * Update purchase order in form service
   *
   * Method: POST
   * Endpoint: /api/forms/purchase-orders/update/{form}
   *
   * @param {*} data Request data
   * @param int form id
   * @returns {Promise}
   */
  updatePurchaseOrder(data, form) {
    return axios.post(`${this.baseUrl}/update/${form}`, data, {
      headers: this.headers,
    });
  }
}
