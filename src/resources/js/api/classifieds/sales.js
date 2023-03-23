import axios from 'axios';
import ApiService from '../api';

export default class ClassifiedSalesApiService extends ApiService {
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
    this.baseUrl = '/api/classifieds/sales';
  }

  /**
   * Get Products list based on conditions
   *
   * Method: PATCH
   * Endpoint: /api/classifieds/sales/products-list
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  getLists(data) {
    return axios.patch(`${this.baseUrl}/products-list`, data, {
      headers: this.headers,
    });
  }

  /**
   * Get Registered products list based on conditions
   *
   * Method: PATCH
   * Endpoint: /api/classifieds/sales/registered-products
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  getRegisteredLists(data) {
    return axios.patch(`${this.baseUrl}/registered-products`, data, {
      headers: this.headers,
    });
  }

  /**
   * Update product accessibility
   *
   * Method: PUT
   * Endpoint: /api/classifieds/sales/update/accessibility/{id}
   *
   * @param {*} id Product ID
   * @param {*} data Request data
   * @returns {Promise}
   */
  updateAccessibility(id, data) {
    return axios.put(`${this.baseUrl}/update/accessibility/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Delete Registered product
   *
   * Method: DELETE
   * Endpoint: /api/classifieds/sales/registered-products/delete-registered-products/{id}
   *
   * @param {*} id Product ID
   * @returns {Promise}
   */
  deleteRegisteredProduct(id) {
    return axios.delete(
      `${this.baseUrl}/registered-products/delete-registered-products/${id}`,
      {
        headers: this.headers,
      }
    );
  }

  /**
   * Get Sales Categories list
   *
   * Method: PATCH
   * Endpoint: /api/classifieds/sales/sales-categories-list
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  getSalesCategories() {
    return axios.patch(`${this.baseUrl}/sales-categories-list`, {
      headers: this.headers,
    });
  }
}
