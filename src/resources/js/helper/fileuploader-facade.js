import 'filepond-polyfill';
import * as FilePond from 'filepond';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import localeJs from './utils/fileuploader-locale.ja';
import i18n from '../i18n';

/**
 * Checks if file needs to be uploaded
 *
 * @param {FilePondFile}  item  File instance of Filepond
 * @returns {boolean}
 */
const isFilePendingUpload = (file) => {
  const isAddedFile = file.origin === FilePond.FileOrigin.INPUT;
  const isLimboFile = file.origin === FilePond.FileOrigin.LIMBO;
  const isProcessed = file.status === FilePond.FileStatus.PROCESSING_COMPLETE;
  const isForUpload = isAddedFile || isLimboFile;

  return isForUpload && !isProcessed;
};

/**
 * Checks if file is not a valid file
 *
 * @param {FilePondFile}  file  File instance of Filepond
 * @returns {boolean}
 */
const isNotValidFile = (file) => {
  const isLoadErrorFile = file.status === FilePond.FileStatus.LOAD_ERROR;

  return isLoadErrorFile;
};

/**
 * Checks if file is finished uploading/processing
 *
 * @param {FilePondFile}  file  File instance of Filepond
 * @returns {boolean}
 */
const isFileProcessComplete = (file) => {
  const isAddedFile = file.origin === FilePond.FileOrigin.INPUT;
  const isLocalFile = file.origin === FilePond.FileOrigin.LOCAL;
  const isLimboFile = file.origin === FilePond.FileOrigin.LIMBO;
  const isProcessFile = file.status === FilePond.FileStatus.PROCESSING_COMPLETE;
  const isForUpload = isAddedFile || isLimboFile;

  return (isForUpload && isProcessFile) || isLocalFile;
};

/**
 * Generate basic server configuration based on data attributes given
 *
 * @param {Element}  element  file input element
 * @returns {Object}
 */
const generateServerConfig = (element) => {
  const csrfToken = document.head.querySelector(
    'meta[name=csrf-token][content]'
  ).content;
  const { dataset } = element;
  const server = {};
  const headers = { 'X-CSRF-Token': csrfToken };

  // Setup basic process configuration
  if (typeof dataset.upload !== 'undefined') {
    server.process = {
      url: dataset.upload,
      headers,
    };
  }

  // Setup basic revert configuration
  if (typeof dataset.revert !== 'undefined') {
    server.revert = {
      url: dataset.revert,
      headers,
    };
  }

  // Setup basic patch configuration
  if (typeof dataset.chunk !== 'undefined') {
    server.patch = {
      url: `${dataset.chunk}?patch=`,
      headers,
    };
  }

  // Setup restore configuration
  if (typeof dataset.restore !== 'undefined') {
    server.restore = {
      url: `${dataset.restore}?code=`,
      headers,
    };
  }

  // Setup restore configuration
  if (typeof dataset.load !== 'undefined') {
    server.load = {
      url: `${dataset.load}?path=`,
      headers,
    };
  }

  return server;
};

/**
 * File Uploader Object
 *
 * Based upon Filepond JS library, contains custom properties and methods
 *
 * @property    pond                Filepond instance, will be set on intialize
 * @method      initialize          Initializes Filepond Instance, options are set here
 * @method      isValidFiles        Checks if the files are valid or status is not invalid
 * @method      hasNoPendingUpload  Checks if the files has no pending files to be uploaded
 * @method      isProcessComplete   Checks if all the processes/upload are complete
 * @method      uploadFiles         Starts upload/process of files
 * @method      clearFiles          Clears files in file uploader
 */
const FileUploaderFacade = (options = {}) => {
  // Register default plugins
  FilePond.registerPlugin(
    FilePondPluginFileValidateSize,
    FilePondPluginFileValidateType
  );

  // Register custom plugins
  if (Object.prototype.hasOwnProperty.call(options, 'plugins')) {
    FilePond.registerPlugin(...options.plugins);
  }

  // Register custom plugins
  if (Object.prototype.hasOwnProperty.call(options, 'hasImagePreview')) {
    if (options.hasImagePreview === true) {
      FilePond.registerPlugin(FilePondPluginImagePreview);
    }
  }

  // Initialize element and token
  const element = document.querySelector(
    options.selector || '.js-file-uploader'
  );

  // Initialize default configurations
  let config = {
    name: element.name,
    credits: false,
    hasThumbnail: false,
    maxFiles: 100,
    maxFileSize: '30MB',
    maxTotalFileSize: '300MB',
    files: [],
  };

  // Setup localization
  const lang = document.querySelector('html').getAttribute('lang');
  if (lang === 'jp') {
    config = Object.assign(config, localeJs);
  }

  // Get dataset of element
  const { dataset } = element;

  // Setup max file count
  if (typeof dataset.maxFileCount !== 'undefined') {
    config.maxFiles = dataset.maxFileCount;
  }

  // Setup max file size
  if (typeof dataset.maxFileSize !== 'undefined') {
    config.maxFileSize = dataset.maxFileSize;
  }

  // Setup max total file size
  if (typeof dataset.maxTotalFileSize !== 'undefined') {
    config.maxTotalFileSize = dataset.maxTotalFileSize;
  }

  // Setup list of file codes to restore
  if (Object.prototype.hasOwnProperty.call(options, 'codesSelector')) {
    const tempCodes = document.querySelectorAll(
      `${options.codesSelector} input`
    );

    tempCodes.forEach((codeInput) => {
      config.files.push({
        source: codeInput.value,
        options: {
          type: 'limbo',
        },
      });
    });
  }

  // Setup list of file codes to load
  if (Object.prototype.hasOwnProperty.call(options, 'pathsSelector')) {
    const localPaths = document.querySelectorAll(
      `${options.pathsSelector} input`
    );

    localPaths.forEach((pathInput) => {
      config.files.push({
        source: pathInput.value,
        options: {
          type: 'local',
        },
      });
    });
  }

  // Setup configuration and overrides
  config.server = generateServerConfig(element);

  // Overwrite config options data
  config = Object.assign(config, options);

  // Create filepond element
  const pond = FilePond.create(element, config);

  return {
    pond,
    isValidFiles() {
      const files = this.pond.getFiles();
      for (let i = 0; i < files.length; i += 1) {
        if (isNotValidFile(files[i])) {
          return false;
        }
      }

      return true;
    },
    hasNoPendingUpload() {
      const files = this.pond.getFiles();
      for (let i = 0; i < files.length; i += 1) {
        if (isFilePendingUpload(files[i])) {
          return false;
        }
      }

      return true;
    },
    isProcessComplete() {
      const files = this.pond.getFiles();
      for (let i = 0; i < files.length; i += 1) {
        if (!isFileProcessComplete(files[i])) {
          return false;
        }
      }

      return true;
    },
    uploadFiles() {
      return new Promise((resolve, reject) => {
        // Check if valid files
        if (!this.isValidFiles()) {
          const error = {
            message: i18n.global.t('alerts.error'),
            status: null,
          };

          return reject(error);
        }

        // Disregard upload file if no pending upload
        if (this.hasNoPendingUpload()) {
          return resolve();
        }

        // Attach on process event listener
        this.pond.onprocessfile = () => {
          if (this.isProcessComplete()) {
            return resolve();
          }

          return null;
        };

        // Start file upload
        this.pond.processFiles().catch((data) => {
          const error = {
            message: data.error.body,
            status: null,
          };

          reject(error);
        });

        return null;
      });
    },
    clearFiles() {
      this.pond.removeFiles();
    },
  };
};

window.FileUploaderFacade = FileUploaderFacade;
