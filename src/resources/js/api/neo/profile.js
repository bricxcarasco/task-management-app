import axios from 'axios';
import NeoApiService from '../neo';

export default class NeoProfileApiService extends NeoApiService {
  /**
   * Constructor function
   *
   * @returns {void}
   */
  constructor() {
    super();

    /**
     * Base Url for Neo Profile API
     * @var {string}
     */
    this.baseUrl = 'api/neo/profile';
  }

  /**
   * Update NEO profile image
   *
   * @returns {Promise}
   */
  updateProfileImage(data) {
    this.promise = axios.post('api/neo/profile/update/image', data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Update NEO profile image
   *
   * @returns {Promise}
   */
  deleteProfileImage(id) {
    this.promise = axios.delete(`api/neo/profile/update/image/${id}`, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Update location of Neo profile
   *
   * Method: PATCH
   * Endpoint: /api/neo/profile/update/location/{id}
   *
   * @param {Object} data Request data
   * @param {Integer} id NEO id
   * @returns {Promise}
   */
  updateLocation(data, id) {
    return axios.patch(`api/neo/profile/update/location/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Update profession of authenticated user
   *
   * @returns {Promise}
   */
  updateOrganizationName(id, data) {
    this.promise = axios.put(
      `${this.baseUrl}/update/organization-name/${id}`,
      data,
      {
        headers: this.headers,
      }
    );
    return this.promise;
  }

  /**
   * Update the Telephone of authenticated user
   * @param {Object} data Request data
   * @param {Integer} id NEO id
   * @returns {Promise}
   */
  updateTelephone(data, id) {
    this.promise = axios.patch(`api/neo/profile/update/telephone/${id}`, data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Adds business hours/ holiday to neo_experts
   * @param {Object} data Request data
   * @param {Integer} id NEO id
   * @returns {Promise}
   */
  updateIntroduction(data, id) {
    return axios.patch(`api/neo/profile/update/introduction/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Fetches awards of Neo profile
   *
   * Method: GET
   * Endpoint: /api/neo/profile/award-history/{id}
   *
   * @param {Integer} id Neo ID
   * @returns {Promise}
   */
  getAwards(id) {
    return axios.get(`${this.baseUrl}/award-history/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Adds award of Neo profile
   *
   * Method: POST
   * Endpoint: /api/neo/profile/award-history/{id}
   *
   * @param {Integer} id Neo ID
   * @param {*} data Request data
   * @returns {Promise}
   */
  addAward(id, data) {
    return axios.post(`${this.baseUrl}/award-history/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Edits award of Neo profile
   *
   * Method: PUT
   * Endpoint: /api/neo/profile/award-history/{id}
   *
   * @param {Integer} id Neo Expert ID
   * @param {Object} data Request data
   * @returns {Promise}
   */
  updateAward(id, data) {
    return axios.put(`${this.baseUrl}/award-history/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Deletes the award of Neo profile
   *
   * Method: DELETE
   * Endpoint: /api/neo/profile/award-history/{id}
   *
   * @param {Integer} id Neo Expert ID
   * @returns {Promise}
   */
  deleteAward(id) {
    return axios.delete(`${this.baseUrl}/award-history/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Fetches industries of Neo profile
   *
   * Method: GET
   * Endpoint: /api/neo/profile/industry/{id}
   *
   * @param {Integer} id Neo ID
   * @returns {Promise}
   */
  getIndustries(id) {
    return axios.get(`${this.baseUrl}/industry/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Adds industry of Neo profile
   *
   * Method: POST
   * Endpoint: /api/neo/profile/industry/{id}
   *
   * @param {Integer} id Neo ID
   * @param {*} data Request data
   * @returns {Promise}
   */
  addIndustry(id, data) {
    return axios.post(`${this.baseUrl}/industry/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Edits industry of Neo profile
   *
   * Method: PUT
   * Endpoint: /api/neo/profile/industry/{id}
   *
   * @param {Integer} id Neo Expert ID
   * @param {Object} data Request data
   * @returns {Promise}
   */
  updateIndustry(id, data) {
    return axios.put(`${this.baseUrl}/industry/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Deletes the industry of Neo profile
   *
   * Method: DELETE
   * Endpoint: /api/neo/profile/industry/{id}
   *
   * @param {Integer} id Neo Expert ID
   * @returns {Promise}
   */
  deleteIndustry(id) {
    return axios.delete(`${this.baseUrl}/industry/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Fetches qualifications of Neo profile
   *
   * Method: GET
   * Endpoint: /api/neo/profile/qualification/{id}
   *
   * @param {Integer} id Neo ID
   * @returns {Promise}
   */
  getQualifications(id) {
    return axios.get(`${this.baseUrl}/qualification/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Adds qualification of Neo profile
   *
   * Method: POST
   * Endpoint: /api/neo/profile/qualification/{id}
   *
   * @param {Integer} id Neo ID
   * @param {*} data Request data
   * @returns {Promise}
   */
  addQualification(id, data) {
    return axios.post(`${this.baseUrl}/qualification/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Edits qualification of Neo profile
   *
   * Method: PUT
   * Endpoint: /api/neo/profile/qualification/{id}
   *
   * @param {Integer} id Neo Expert ID
   * @param {Object} data Request data
   * @returns {Promise}
   */
  updateQualification(id, data) {
    return axios.put(`${this.baseUrl}/qualification/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Deletes the qualification of Neo profile
   *
   * Method: DELETE
   * Endpoint: /api/neo/profile/qualification/{id}
   *
   * @param {Integer} id Neo Expert ID
   * @returns {Promise}
   */
  deleteQualification(id) {
    return axios.delete(`${this.baseUrl}/qualification/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Fetches skills of Neo profile
   *
   * Method: GET
   * Endpoint: /api/neo/profile/skill/{id}
   *
   * @param {Integer} id Neo ID
   * @returns {Promise}
   */
  getSkills(id) {
    return axios.get(`${this.baseUrl}/skill/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Adds skill of Neo profile
   *
   * Method: POST
   * Endpoint: /api/neo/profile/skill/{id}
   *
   * @param {Integer} id Neo ID
   * @param {*} data Request data
   * @returns {Promise}
   */
  addSkill(id, data) {
    return axios.post(`${this.baseUrl}/skill/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Edits skill of Neo profile
   *
   * Method: PUT
   * Endpoint: /api/neo/profile/skill/{id}
   *
   * @param {Integer} id Neo Expert ID
   * @param {Object} data Request data
   * @returns {Promise}
   */
  updateSkill(id, data) {
    return axios.put(`${this.baseUrl}/skill/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Deletes the skill of Neo profile
   *
   * Method: DELETE
   * Endpoint: /api/neo/profile/skill/{id}
   *
   * @param {Integer} id Neo Expert ID
   * @returns {Promise}
   */
  deleteSkill(id) {
    return axios.delete(`${this.baseUrl}/qualification/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Update establishment date of neo
   *
   * @returns {Promise}
   */
  updateEstablishmentDate(id, data) {
    this.promise = axios.patch(
      `${this.baseUrl}/update/establishment-date/${id}`,
      data,
      {
        headers: this.headers,
      }
    );
    return this.promise;
  }

  /**
   * Fetches histories of specified neo
   *
   * Method: GET
   * Endpoint: /api/neo/profile/history/{id}
   *
   * @param {Integer} id
   * @returns {Promise}
   */
  getHistories(id) {
    this.promise = axios.get(`${this.baseUrl}/history/${id}`, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Adds history of specified neo
   *
   * Method: POST
   * Endpoint: /api/neo/profile/history/{id}
   *
   * @param {Integer} id
   * @param {*} data Request data
   * @returns {Promise}
   */
  addHistory(id, data) {
    this.promise = axios.post(`${this.baseUrl}/history/${id}`, data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Edits history of specified neo
   *
   * Method: PUT
   * Endpoint: /api/neo/profile/history/{id}
   *
   * @param {Integer} id
   * @param {Object} data Request data
   * @returns {Promise}
   */
  updateHistory(id, data) {
    this.promise = axios.put(`${this.baseUrl}/history/${id}`, data, {
      headers: this.headers,
    });

    return this.promise;
  }

  /**
   * Deletes the history of specified neo
   *
   * @returns {Promise}
   */
  deleteHistory(id) {
    this.promise = axios.delete(`${this.baseUrl}/history/${id}`, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Fetches products of Neo profile
   *
   * Method: GET
   * Endpoint: /api/neo/profile/product/{id}
   *
   * @param {Integer} id Neo ID
   * @returns {Promise}
   */
  getProducts(id) {
    return axios.get(`${this.baseUrl}/product/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Adds product of Neo profile
   *
   * Method: POST
   * Endpoint: /api/neo/profile/product/{id}
   *
   * @param {Integer} id Neo ID
   * @param {*} data Request data
   * @returns {Promise}
   */
  addProduct(id, data) {
    return axios.post(`${this.baseUrl}/product/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Edits product of Neo profile
   *
   * Method: PUT
   * Endpoint: /api/neo/profile/product/{id}
   *
   * @param {Integer} id Neo Expert ID
   * @param {Object} data Request data
   * @returns {Promise}
   */
  updateProduct(id, data) {
    return axios.put(`${this.baseUrl}/product/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Deletes the product of Neo profile
   *
   * Method: DELETE
   * Endpoint: /api/neo/profile/product/{id}
   *
   * @param {Integer} id Neo Expert ID
   * @returns {Promise}
   */
  deleteProduct(id) {
    return axios.delete(`${this.baseUrl}/product/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Fetches URLs of Neo profile
   *
   * Method: GET
   * Endpoint: /api/neo/profile/url/{id}
   *
   * @param {Integer} id Neo ID
   * @returns {Promise}
   */
  getUrls(id) {
    return axios.get(`${this.baseUrl}/url/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Adds url of Neo profile
   *
   * Method: POST
   * Endpoint: /api/neo/profile/url/{id}
   *
   * @param {Integer} id Neo ID
   * @param {*} data Request data
   * @returns {Promise}
   */
  addUrl(id, data) {
    return axios.post(`${this.baseUrl}/url/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Edits url of Neo profile
   *
   * Method: PUT
   * Endpoint: /api/neo/profile/url/{id}
   *
   * @param {Integer} id Neo Expert ID
   * @param {Object} data Request data
   * @returns {Promise}
   */
  updateUrl(id, data) {
    return axios.put(`${this.baseUrl}/url/${id}`, data, {
      headers: this.headers,
    });
  }

  /**
   * Deletes the url of Neo profile
   *
   * Method: DELETE
   * Endpoint: /api/neo/profile/url/{id}
   *
   * @param {Integer} id Neo Expert ID
   * @returns {Promise}
   */
  deleteUrl(id) {
    return axios.delete(`${this.baseUrl}/url/${id}`, {
      headers: this.headers,
    });
  }

  /**
   * Fetches email address of authenticated user
   *
   * @returns {Promise}
   */
  getEmails(id) {
    this.promise = axios.get(`api/neo/profile/email-address/${id}`, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Add email address of authenticated user
   *
   * @returns {Promise}
   */
  addEmail(data, id) {
    this.promise = axios.post(`api/neo/profile/email-address/${id}`, data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Delete email address of authenticated user
   *
   * @returns {Promise}
   */
  deleteEmail(id) {
    this.promise = axios.delete(`api/neo/profile/email-address/${id}`, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Update email address of authenticated user
   *
   * @returns {Promise}
   */
  updateEmail(data, id) {
    this.promise = axios.put(`api/neo/profile/email-address/${id}`, data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Adds business hours/ holiday to neo_experts
   *
   * Method: POST
   * Endpoint: /api/neo/profile/business-holiday/{id}
   *
   * @param {Object} data Request data
   * @param {Integer} id NEO id
   * @returns {Promise}
   */
  upsertBusinessHoliday(data, id) {
    this.promise = axios.post(
      `api/neo/profile/update/business-holiday/${id}`,
      data,
      {
        headers: this.headers,
      }
    );
    return this.promise;
  }

  /**
   * Update overseas support of Neo Profile
   * @param {Object} data Request data
   * @param {Integer} id NEO id
   * @returns {Promise}
   */
  updateOverseasSupport(data, id) {
    this.promise = axios.patch(
      `api/neo/profile/update/overseas-support/${id}`,
      data,
      {
        headers: this.headers,
      }
    );
    return this.promise;
  }
}
