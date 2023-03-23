import axios from 'axios';
import ApiService from '../api';

export default class ClassifiedFavoritesApiService extends ApiService {
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
    this.baseUrl = '/api/classifieds/favorites';
  }

  /**
   * Favorite product
   *
   * Method: Patch
   * Endpoint: /api/classifieds/favorites
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  getFavorites(data) {
    return axios.patch(`${this.baseUrl}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Favorite product
   *
   * Method: Patch
   * Endpoint: /api/classifieds/favorites
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  favoriteProduct(id, data) {
    return axios.patch(`${this.baseUrl}/favorite-product/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Unfavorite product
   *
   * Method: Patch
   * Endpoint: /api/classifieds/favorites
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  unfavoriteProduct(id, data) {
    return axios.patch(`${this.baseUrl}/unfavorite-product/${id}`, data, {
      headers: this.headers,
    });
  }
}
