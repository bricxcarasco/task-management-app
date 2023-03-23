/**
 * Get platform
 */
const getPlatform = function () {
    if (monaca.isAndroid) {
        return 'Android';
    }

    if (monaca.isIOS) {
        return 'iOS';
    }

    return 'Others'
}

var app = {
    /**
     * Initialize applicationn
     */
    initialize: function() {
        this.bindEvents();
    },
    /**
     * Attach device ready event listener
     */
    bindEvents: function() {
        document.addEventListener('deviceready', this.onDeviceReady, false);
    },
    /**
     * Redirect to target web app
     */
    onDeviceReady: function() {
        let platform = getPlatform();

        var targetUrl = process.env.TARGET_URL + '?platform_device=' + platform;
        var backupLink = document.getElementById("target-link");

        backupLink.setAttribute("href", targetUrl);
        backupLink.text = targetUrl;
        window.location.replace(targetUrl);
    },
};

app.initialize();