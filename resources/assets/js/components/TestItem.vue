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
                <button class="btn btn-danger" v-on:click="deleteAnswer(index)">&times;</button>
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
            <ul>
                <li v-for="(question, n) in questions">
                    <div>
                        <b v-if="editID != n">{{question.question}}</b>
                        <div class="form-group">
                            <input type="text" v-bind:class="{'form-control': 1, 'has-error': fields_errors.question}" v-model="eQuestion.question" v-if="editID == n">
                        </div>
                        <span class="pull-right" v-if="editID != n">
                            <button class="btn btn-info btn-xs" v-on:click="editQuestion(question, n)">Edit</button>
                            <button class="btn btn-danger btn-xs" v-on:click="deleteQuestion(n)">&times;</button>
                        </span>
                        <span class="pull-right" v-if="editID == n">
                            <button class="btn btn-info btn-xs" v-on:click="updateQuestion(question, n)">Update</button>
                            <button class="btn btn-default btn-xs" v-on:click="editID = null">Cancel</button>
                        </span>
                    </div>
                    <ul v-if="editID != n">
                        <li v-for="(answer, index) in question.answers">
                            <label v-bind:class="{ right: questions[n].right == index }">{{answer}}</label>
                        </li>
                    </ul>
                    <ul class="answer" v-if="editID == n">
                        <li v-for="(answer, index) in eQuestion.answers">
                            <input type="radio" v-model="eQuestion.right" v-bind:value="index">
                            <input type="text" class="form-control" v-model="eQuestion.answers[index]">
                            <button class="btn btn-danger" v-on:click="delAns(index)">&times;</button>
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

                eQuestion: {question: '', answers: [], right: null},
                editID: null
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
                if(this.answers.length > 2) {
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
            editQuestion(question, n) {
                let self = this;
                this.editID = n;
                this.eQuestion = {
                    question: question.question,
                    right: question.right,
                    answers: []
                };
                question.answers.forEach(item => {
                    self.eQuestion.answers.push(item);
                });
            },
            delAns(index) {
                if(this.eQuestion.answers.length > 2) {
                    this.eQuestion.answers.splice(index, 1);
                }
            },
            updateQuestion(question, index) {
                let data = {
                    question_id: question.id,
                    question: this.eQuestion.question,
                    answers: this.eQuestion.answers,
                    right: this.eQuestion.right
                }
                console.log(this.questions[index]);
                this.$http.post('/api/questions/update', data).then(res => {
                    if(res.body.status) {
                        this.editID = null;
                        this.fields_errors = [];
                        this.questions[index].question = this.eQuestion.question;
                        this.questions[index].answers = this.eQuestion.answers;
                        this.questions[index].right = this.eQuestion.right;
                        alertify.success('Question was update');
                    } else {
                        alertify.error('Question wasn\'t update :(');
                    }

                    if(res.body.message) {
                        alertify.error(res.body.message);
                    }
                    
                }, err => {
                    if(err.body) {
                        this.fields_errors = err.body;
                    }
                });
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
        width: 450px;
        float: left;
        margin-right: 10px;
    }
    .answer li {
        margin-top: 4px;
    }
</style>