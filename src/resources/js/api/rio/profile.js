import axios from 'axios';
import RioApiService from '../rio';

export default class RioProfileApiService extends RioApiService {
  /**
   * Update RIO profile image
   *
   * @returns {Promise}
   */
  updateProfileImage(data) {
    this.promise = axios.post('api/rio/profile/update/image', data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Update RIO profile image
   *
   * @returns {Promise}
   */
  deleteProfileImage(id) {
    this.promise = axios.delete(`api/rio/profile/update/image/${id}`, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Fetches professions of authenticated user
   *
   * @returns {Promise}
   */
  getProfessions() {
    this.promise = axios.get('api/rio/profile/profession', {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Adds profession of authenticated user
   *
   * @returns {Promise}
   */
  addProfession(data) {
    this.promise = axios.post('api/rio/profile/profession', data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Update profession of authenticated user
   *
   * @returns {Promise}
   */
  updateProfession(id, data) {
    this.promise = axios.put(`api/rio/profile/profession/${id}`, data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Deletes the profession of authenticated user
   *
   * @returns {Promise}
   */
  deleteProfession(id) {
    this.promise = axios.delete(`api/rio/profile/profession/${id}`, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Fetches industries of authenticated user
   *
   * Method: GET
   * Endpoint: /api/rio/profile/industry
   *
   * @returns {Promise}
   */
  getIndustries() {
    this.promise = axios.get('api/rio/profile/industry', {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Adds industry of authenticated user
   *
   * Method: POST
   * Endpoint: /api/rio/profile/industry
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  addIndustry(data) {
    this.promise = axios.post('api/rio/profile/industry', data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Edits industry of authenticated user
   *
   * Method: PUT
   * Endpoint: /api/rio/profile/industry
   *
   * @param {Integer} id Rio Expert ID
   * @param {Object} data Request data
   * @returns {Promise}
   */
  updateIndustry(id, data) {
    this.promise = axios.put(`api/rio/profile/industry/${id}`, data, {
      headers: this.headers,
    });

    return this.promise;
  }

  /**
   * Deletes the industry of authenticated user
   *
   * @returns {Promise}
   */
  deleteIndustry(id) {
    this.promise = axios.delete(`api/rio/profile/industry/${id}`, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Fetches qualifications of authenticated user
   *
   * @returns {Promise}
   */
  getQualifications() {
    this.promise = axios.get('api/rio/profile/qualification', {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Adds qualification of authenticated user
   *
   * @returns {Promise}
   */
  addQualification(data) {
    this.promise = axios.post('api/rio/profile/qualification', data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Update qualification of authenticated user
   *
   * @returns {Promise}
   */
  updateQualification(id, data) {
    this.promise = axios.put(`api/rio/profile/qualification/${id}`, data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Deletes the qualification of authenticated user
   *
   * @returns {Promise}
   */
  deleteQualification(id) {
    this.promise = axios.delete(`api/rio/profile/qualification/${id}`, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Fetches skills of authenticated user
   *
   * @returns {Promise}
   */
  getSkills() {
    this.promise = axios.get('api/rio/profile/skill', {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Adds skill of authenticated user
   *
   * @returns {Promise}
   */
  addSkill(data) {
    this.promise = axios.post('api/rio/profile/skill', data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Update skill of authenticated user
   *
   * @returns {Promise}
   */
  updateSkill(id, data) {
    this.promise = axios.put(`api/rio/profile/skill/${id}`, data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Deletes the skill of authenticated user
   *
   * @returns {Promise}
   */
  deleteSkill(id) {
    this.promise = axios.delete(`api/rio/profile/skill/${id}`, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Fetches educational backgrounds of authenticated user
   *
   * Method: GET
   * Endpoint: /api/rio/profile/educational-background
   *
   * @returns {Promise}
   */
  getEducationalBackgrounds() {
    this.promise = axios.get('api/rio/profile/educational-background', {
      headers: this.headers,
    });

    return this.promise;
  }

  /**
   * Adds educational background to authenticated user
   *
   * Method: POST
   * Endpoint: /api/rio/profile/educational-background
   *
   * @param {Object} data Request data
   * @returns {Promise}
   */
  addEducationalBackground(data) {
    this.promise = axios.post('api/rio/profile/educational-background', data, {
      headers: this.headers,
    });

    return this.promise;
  }

  /**
   * Update educational background of authenticated user
   *
   * @returns {Promise}
   */
  updateEducationalBackground(id, data) {
    this.promise = axios.put(
      `api/rio/profile/educational-background/${id}`,
      data,
      {
        headers: this.headers,
      }
    );
    return this.promise;
  }

  /**
   * Deletes the educational background of authenticated user
   *
   * Method: DELETE
   * Endpoint: /api/rio/profile/educational-background/{id}
   *
   * @param {Number} id Request data
   * @returns {Promise}
   */
  deleteEducationalBackground(id) {
    this.promise = axios.delete(
      `api/rio/profile/educational-background/${id}`,
      {
        headers: this.headers,
      }
    );

    return this.promise;
  }

  /**
   * Fetches awards of authenticated user
   *
   * @returns {Promise}
   */
  getAwards() {
    this.promise = axios.get('api/rio/profile/award-history', {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Add award history to authenticated user
   *
   * Method: POST
   * Endpoint: /api/rio/profile/award-history
   *
   * @param {Object} data Request data
   * @returns {Promise}
   */
  addAwardHistory(data) {
    this.promise = axios.post('api/rio/profile/award-history', data, {
      headers: this.headers,
    });

    return this.promise;
  }

  /**
   * Edits award history of authenticated user
   *
   * Method: PUT
   * Endpoint: /api/rio/profile/award-history/{id}
   *
   * @param {Integer} id Rio Expert ID
   * @param {Object} data Request data
   * @returns {Promise}
   */
  updateAwardHistory(id, data) {
    this.promise = axios.put(`api/rio/profile/award-history/${id}`, data, {
      headers: this.headers,
    });

    return this.promise;
  }

  /**
   * Delete award history to authenticated user
   *
   * Method: DELETE
   * Endpoint: /api/rio/profile/award-history/{id}
   *
   * @param {Number} id Request data
   * @returns {Promise}
   */
  deleteAwardHistory(id) {
    this.promise = axios.delete(`api/rio/profile/award-history/${id}`, {
      headers: this.headers,
    });

    return this.promise;
  }

  /**
   * Fetches products of authenticated user
   *
   * @returns {Promise}
   */
  getProducts() {
    this.promise = axios.get('api/rio/profile/product', {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Adds product of authenticated user
   *
   * @returns {Promise}
   */
  addProduct(data) {
    this.promise = axios.post('api/rio/profile/product', data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Update product of authenticated user
   *
   * @returns {Promise}
   */
  updateProduct(id, data) {
    this.promise = axios.put(`api/rio/profile/product/${id}`, data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Deletes the product of authenticated user
   *
   * @returns {Promise}
   */
  deleteProduct(id) {
    this.promise = axios.delete(`api/rio/profile/product/${id}`, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Update the gender of authenticated user
   *
   * @returns {Promise}
   */
  updateGender(data) {
    this.promise = axios.patch('api/rio/profile/update/gender', data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Update the birthdate of authenticated user
   *
   * * Method: PATCH
   * Endpoint: /api/rio/profile/update/birthdate
   *
   * @param {*} data Request data
   *
   * @returns {Promise}
   */
  updateBirthdate(data) {
    this.promise = axios.patch('api/rio/profile/update/birthdate', data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Update present address of authneticated user
   *
   * Method: PATCH
   * Endpoint: /api/rio/profile/update/present-address
   *
   * @param {*} data Request data
   * @returns {Promise}
   */
  updatePresentAddress(data) {
    this.promise = axios.patch('api/rio/profile/update/present-address', data, {
      headers: this.headers,
    });

    return this.promise;
  }

  /**
   * Updates self-introduction of authenticated user
   *
   * @returns {Promise}
   */
  updateSelfIntroduction(data) {
    this.promise = axios.patch(
      'api/rio/profile/update/self-introduction',
      data,
      {
        headers: this.headers,
      }
    );
    return this.promise;
  }

  /**
   * Update the Telephone of authenticated user
   *
   * @returns {Promise}
   */
  updateTelephone(data) {
    this.promise = axios.patch('api/rio/profile/update/telephone', data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Update the gender of authenticated user
   *
   * @returns {Promise}
   */
  updateName(data) {
    this.promise = axios.put('api/rio/profile/update/name', data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Update the Home address of authenticated user
   *
   * @returns {Promise}
   */
  updateHomeAddress(data) {
    this.promise = axios.put('api/rio/profile/update/home', data, {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Fetches affiliation of authenticated user
   *
   * @returns {Promise}
   */
  getAffiliates() {
    this.promise = axios.get('api/rio/profile/affiliate', {
      headers: this.headers,
    });
    return this.promise;
  }

  /**
   * Update affiliation of authenticated user
   *
   * @returns {Promise}
   */
  updateAffiliate(id, data) {
    this.promise = axios.put(`api/rio/profile/affiliate/${id}`, data, {
      headers: this.headers,
    });
    return this.promise;
  }
}
