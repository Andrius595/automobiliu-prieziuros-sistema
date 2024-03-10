import { lt } from 'vuetify/locale'
import ltLang from '@/lang/lt'

export default defineI18nConfig(() => ({
    legacy: false,
    locale: 'en',
    messages: {
        en: {
            first_name: 'First name'
        },
        lt: {
            $vuetify: lt,
            ...ltLang
        }
    }
}))