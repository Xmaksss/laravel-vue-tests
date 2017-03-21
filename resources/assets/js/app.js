
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import Tests from './components/Tests.vue';
import TestItem from './components/TestItem.vue';
import Testing from './components/Testing.vue';
import ParseQuestions from './components/ParseQuestions.vue';

const router = new VueRouter({
    routes: [
        {path: '/', redirect: '/tests'},
        {path: '/tests', component: Tests},
        {path: '/test/:testId', component: TestItem},
        {path: '/testing/:testId', component: Testing},
        {path: '/parse-questions', component: ParseQuestions}
    ]
});

const app = new Vue({
    el: '#app',
    router: router,
    data: {
        
    },
    mounted() {
        //console.log(routes);
    }
});
