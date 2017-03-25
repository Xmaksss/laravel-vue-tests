<template>
    <div id="test">
        <p><b>Title:</b> {{title}}</p>
        <p><b>Count questions in test:</b> {{count}}</p>
        <hr>
        <form v-on:submit.prevent="addQuestion">
            <div class="errors" v-for="errors in fields_errors">
                <div class="alert alert-danger" role="alert" v-for="error in errors">{{error}}</div>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" v-model="question" id="question" placeholder="Your Question">
            </div>
            <div class="form-group answer" v-for="(answer, index) in answers">
                <input type="radio" v-model="right" v-bind:value="index">
                <input type="text" class="form-control" v-model="answers[index]" placeholder="Answer">
                <button class="btn btn-danger" v-on:click="deleteAnswer(index)">Delete</button>
            </div>
            <div class="form-group">
                <button class="btn btn-info btn-xs" type="button" v-on:click="addAnswer">Add answer</button>
            </div>
            <div class="form-group">
                <button class="btn btn-success" type="submit">Save question</button>
            </div>
        </form>
        <hr>
        <div class="questions">
        {{eAnswers}}
            <ul>
                <li v-for="(question, n) in questions">
                    <div><b>{{question.question}}</b>
                        <span class="pull-right">
                            <button class="btn btn-info btn-xs" v-on:click="editQuestion(n)">Edit</button>
                            <button class="btn btn-danger btn-xs" v-on:click="deleteQuestion(n)">&times;</button>
                        </span>
                    </div>
                    <ul>
                        <li v-for="(answer, index) in question.answers">
                            <label v-bind:class="{ right: questions[n].right == index }">{{answer}}</label>
                        </li>
                    </ul>
                    <hr>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>

    export default {
        data() {
            return {
                title: '',
                count: 1,
                testId: this.$route.params.testId,
                questions: [],

                question: '',
                answers: [''],
                right: null,

                fields_errors: [],

                eQuestion: {question: '', answers: [], right: null}
            }
        },
        mounted() {
            this.$http.get('/api/questions?test_id=' + this.testId).then(res => {
                if(res.body.status) {
                    this.questions = res.body.questions;
                    this.title = res.body.test.title;
                    this.count = res.body.test.count_questions;
                    //console.log(this.questions);
                } else {
                    this.$route.router.go('/tests');
                }
            }, err => {
                
            });
        },
        methods: {
            addAnswer() {
                this.answers.push('');
            },
            deleteAnswer(index) {
                if(this.answers.length > 1) {
                    this.answers.splice(index, 1);
                }
            },
            addQuestion() {
                let data = {
                    test_id: this.testId,
                    question: this.question,
                    answers: this.answers,
                    right: this.right
                }
                this.$http.post('/api/questions', data).then(res => {
                    if(res.body.question) {
                        let question = res.body.question;
                        question.answers = JSON.parse(question.answers);
                        this.questions.push(question);
                        this.question = '';
                        this.answers = [''];
                        this.right = null;
                        this.fields_errors = [];
                    }
                }, err => {
                    if(err.body) {
                        this.fields_errors = err.body;
                    }
                });
            },
            deleteQuestion(item) {
                let data = {
                    question_id: this.questions[item].id
                }
                alertify.confirm('Delete Question?', 'Deleting', () => { 
                    this.$http.post('/api/questions/delete', data).then(res => {
                        if(res.body.status) {
                            alertify.success('Question was delete');
                            this.questions.splice(item, 1);
                        }
                    }, err => {
                        alertify.error('Error! :(')
                    });
                }, () => {}).set('labels', {ok:'Yes', cancel:'Cancel'});
            },
            editQuestion(n) {
                console.log(n, this.questions[n]);
                this.eQuestion = this.questions[n];
            }
        }
    }
</script>

<style>
    .answer input[type=radio] {
        float: left;
        margin-top: 10px;
        margin-right: 10px;
    }
    .answer .form-control{
        width: 500px;
        float: left;
        margin-right: 10px;
    }
</style>