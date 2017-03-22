<template>
    <div>
    <h4>{{title}}</h4>
        <hr>
        <div class="questions">
            <ul>
                <li v-for="(question, n) in questions">
                    <div v-show="n == num">
                        <div><b>{{question.question}}</b></div>
                        <ul>
                            <li v-for="(answer, index) in question.answers">
                                <label><input type="radio" v-model="questions[n].right" v-bind:value="index">{{answer}}</label>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        <div class="result" v-if="showResult">
            <h2 class="text-center">{{res}}% ({{ans}}/{{count}})</h2>
            <results v-bind:questions="answers"></results>
        </div>
         <hr>
        <button class="btn btn-primary" v-on:click="nextQuestion" v-if="(num+1) < count">Next</button>
        <button class="btn btn-info" v-on:click="finishTest" v-if="(num+1) == count">Finish</button>
        <button class="btn btn-info" v-on:click="restartTest" v-if="(num+1) > count">Restart</button>
        <h3 class="text-center"  v-if="!showResult"><b>{{num}}/{{count}}</b></h3>
    </div>
</template>

<script>
    import Results from './Results.vue';
    export default {
        data() {
            return {
                title: '',
                count: 1,
                testId: this.$route.params.testId,
                questions: [],
                filteredQuestions: [],

                num: 0,
                showResult: false,
                res: 0,
                ans: 0,
                answers: []
            }
        },
        components: {
            'results': Results
        },
        mounted() {
            this.getQuestions();
        },
        methods: {
            getQuestions() {
                this.$http.get('/api/testing?test_id=' + this.testId).then(res => {
                    if(res.body.status) {
                        this.questions = res.body.questions;
                        this.title = res.body.test.title;
                        this.count = res.body.test.count_questions;
                        if(this.count > this.questions.length) {
                            this.count = this.questions.length
                        }
                        console.log(this.questions);
                    } else {
                        this.$route.router.go('/tests');
                    }
                }, err => {
                    console.error(err);
                });
            },
            nextQuestion() {
                if(this.num <= (this.count-1)) {
                    this.num++;
                }
            },
            finishTest() {
                this.num++;
                //console.log(this.questions);
                let answers = [];

                this.questions.forEach((item) => {
                    answers.push({question_id: item.id, answer: item.right});
                });

                this.$http.post('/api/testing/get-results', {answers}).then(res => {
                    this.showResult = true;
                    this.res = res.body.results;
                    this.ans = res.body.count;
                    this.answers = res.body.questions;
                }, err => {
                    console.error(err);
                });
            },
            restartTest() {
                this.num = 0;
                this.getQuestions();
                this.showResult = false;
                this.results = 0;
            }
        }
    }
</script>