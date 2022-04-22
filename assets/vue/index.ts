import 'reflect-metadata';
import 'es6-shim';
import Vue from 'vue';
import i18n from '@/i18n';
import Dashboard from '@/components/Dashboard.vue';


new Vue({
    i18n,
    el: '#app-dashboard',
    render: h => h(Dashboard)
})
