// import axios from 'axios';

export default class NeoApiService {
  constructor() {
    this.promise = null;
    this.headers = {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    };
  }
}
