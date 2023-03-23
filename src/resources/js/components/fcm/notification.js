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
    FCM.eventTarget.addEventListener(
      'notification',
      (data) => {
        const { detail } = data;
        /* eslint no-undef: 0 */
        setToastNotification(detail.title, detail.body);
      },
      false
    );
  },
};

app.initialize();
