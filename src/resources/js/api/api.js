export default class ApiService {
  constructor() {
    this.promise = null;
    this.headers = {
      'Content-Type': 'application/json',
      Accept: 'application/json',
    };
  }
}
