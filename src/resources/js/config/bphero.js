// BPHERO Configuration file

const BPHERO = {
  /**
   * Default profile image path
   */
  DEFAULT_IMG: '../../../../../images/profile/user_default.png',

  /**
   * Default no image path
   */
  DEFAULT_NO_IMG: '../../../../../img/default-image.png',

  /**
   * Default no image product
   */
  DEFAULT_PRODUCT_IMG: '../../../../../around/img/80.png',

  /**
   * Pagination Number Offet Range
   */
  PAGINATION_OFFSET_RANGE: 4,

  /**
   * Document Management - Maximum File Size
   */
  DOCUMENT_MANAGEMENT_MAX_FILE_SIZE: '100MB',

  /**
   * Chat Service - Max File Size
   */
  CHAT_SERVICE_MAX_FILE_SIZE: '100MB',

  /**
   * Chat Service - Max File Count
   */
  CHAT_SERVICE_MAX_FILES_COUNT: 5,

  /**
   * Chat Service - Image MIME types for preview
   */
  CHAT_SERVICE_PREVIEW_IMAGE_MIMETYPES: [
    'image/apng',
    'image/avif',
    'image/jpeg',
    'image/png',
    'image/svg+xml',
    'image/webp',
  ],

  /**
   * Profile Image - Max File Size
   */
  PROFILE_IMAGE_MAX_FILE_SIZE: '300KB',

  /**
   * Profile Image - Max File Count
   */
  PROFILE_IMAGE_MAX_FILES_COUNT: 1,

  /**
   * Classified Service - Max File Size
   */
  CLASSIFIED_SERVICE_MAX_FILE_SIZE: '5MB',

  /**
   * Classified Service - Max File Count
   */
  CLASSIFIED_SERVICE_MAX_FILES_COUNT: 5,

  /**
   * Classified Service - Allowed File Types
   */
  CLASSIFIED_SERVICE_ALLOWED_TYPES: ['image/jpeg', 'image/png'],

  /**
   * Form Service - Max File Size
   */
  FORM_SERVICE_MAX_FILE_SIZE: '5MB',

  /**
   * Form Service - Max File Count
   */
  FORM_SERVICE_MAX_FILES_COUNT: 1,

  /**
   * Form Service - Allowed File Types
   */
  FORM_SERVICE_ALLOWED_TYPES: [
    'image/jpeg',
    'image/jpg',
    'image/pjpeg',
    'image/png',
  ],

  /**
   * Locale
   */
  LOCALE: process.env.MIX_APP_LOCALE || 'jp',

  /**
   * Google Calendar ID
   */
  GOOGLE_CALENDAR_ID: process.env.MIX_GOOGLE_CALENDAR_ID || null,

  /**
   * Google Calendar API key
   */
  GOOGLE_CALENDAR_API_KEY: process.env.MIX_GOOGLE_CALENDAR_API_KEY || null,

  /**
   * Electronic Contract Service - Max File Size
   */
  ELECTRONIC_CONTRACT_MAX_FILE_SIZE: '100MB',

  /**
   * Electronic Contract Service - Max File Count
   */
  ELECTRONIC_CONTRACT_MAX_FILES_COUNT: 1,

  /**
   * TinyMCE api key
   */
  TINYMCE_API_KEY: process.env.MIX_TINYMCE_API_KEY,

  /**
   * Paginate count
   */
  PAGINATE_COUNT: 20,

  /**
   * Workflow Service - Maximum File Size
   */
  WORKFLOW_SERVICE_MAX_FILE_SIZE: '100MB',

  /**
   * Workflow Service - Max File Count
   */
  WORKFLOW_SERVICE_MAX_FILES_COUNT: 5,
};

export default BPHERO;
