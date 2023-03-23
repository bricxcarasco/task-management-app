const app = {
  /**
   * Initialize applicationn
   */
  initialize() {
    this.bindEvents();
  },
  /**
   * Attach device ready event listener
   */
  bindEvents() {
    document.addEventListener('deviceready', this.onDeviceReady, false);
  },
  /**
   * Redirect to target web app
   */
  async onDeviceReady() {
    /* eslint no-undef: 0 */
    const fcmToken = await FCM.getToken();
    const fcmField = document.querySelector('form input[name=fcm_token]');

    // Inject fcm token to login form
    if (fcmField) {
      fcmField.value = fcmToken;
    }
  },
};

app.initialize();
