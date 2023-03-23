import { createI18n } from 'vue-i18n';
import en from './locales/en.json';
import jp from './locales/jp.json';
import BpheroConfig from './config/bphero';

const i18n = createI18n({
  locale: BpheroConfig.LOCALE || 'jp',
  fallbackLocale: 'en',
  messages: {
    en,
    jp,
  },
});

export default i18n;
