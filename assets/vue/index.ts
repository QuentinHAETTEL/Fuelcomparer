import 'reflect-metadata';
import 'es6-shim';
import Vue from 'vue';
import App from '@/App.vue';


new Vue({
    el: '#app',
    render: h => h(App)
})
