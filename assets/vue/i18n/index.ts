import Vue from 'vue';
import VueI18n from 'vue-i18n';
import fr_FR from './fr_FR.json';


Vue.use(VueI18n);

export default new VueI18n({
    locale: 'fr_FR',
    fallbackLocale: 'fr_FR',
    messages: {
        fr_FR: fr_FR
    }
});