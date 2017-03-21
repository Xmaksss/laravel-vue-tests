<template>
    <div id="parse-questions">
        <div class="form-group">
            <label for="text">Your text for parsing</label>
            <textarea name="text" v-model="text" id="text" rows="10" class="form-control" placeholder="Insert this your text for parsing"></textarea>
        </div>
        <div class="form-group">
            <label>Test name</label>
            <select name="test_id" id="test_id" v-model="testID" class="form-control">
                <option v-for="test in tests" v-bind:value="test.id">{{test.title}}</option>
            </select>
        </div>
        <div class="form-group">
            <button class="btn btn-primary" v-on:click="parse">Parse</button>
        </div>
        <div class="questions">
            <ul>
                <li v-for="(question, n) in questions">
                    <p>{{question.question}}</p>
                    <ul>
                        <li v-for="(answer, index) in question.ans">
                            <label v-bind:class="{ right: questions[n].right == index }"><input type="radio" v-model="questions[n].right" v-bind:value="index">{{answer}}</label>
                        </li>
                    </ul>
                    <hr>
                </li>
            </ul>
        </div>
        <div class="form-group">
            <button v-if="testID != null && questions.length > 0" class="btn btn-success" v-on:click="saveQuestions">Save</button>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                text: '',
                questions: [],
                tests: [],
                testID: null
            }
        },
        mounted() {
            this.$http.get('/api/tests').then(res => {
                this.tests = res.body.tests;
            }, err => {
                console.error(err);
            });
        },
        methods: {
            parse() {
                var rows = this.text.split('\n');
                var questions = [];
                rows.forEach(function(item, key) {
                    item = item.trim();
                    
                    if(item.substr(-1) == ':') {
                        var q = item.slice(item.split(" ")[0].length+1);
                        questions.push({question: q, ans: [], right: undefined});
                    } else {

                        if(item.includes('**')) {
                            item = item.split("**");
                            item = item[1];
                            questions[questions.length-1].right = questions[questions.length-1].ans.length;
                        }

                        if(item != '' && item.length > 3) {
                            if(item.includes('\t')) {
                                item = item.slice(item.split("\t")[0].length+1);
                            }
                            questions[questions.length-1].ans.push(item);
                        }
                        
                    }
                });

                this.filter(questions);
            },
            filter(questions) {
                this.questions = questions.filter((item) => {
                    return item.right != undefined && item.ans.length > 1;
                });
            },
            saveQuestions() {
                let data = {
                    questions: this.questions,
                    test_id: this.testID
                }
                this.$http.post('/api/questions/parse', data).then(res => {
                    if(res.data.message) {
                        alert(res.data.message);
                    }
                    if(res.data.status) {
                        this.text= '';
                        this.questions = [];
                    }
                }, err => {

                })
            }
        }
        
    }
</script>

<style>
    #parse-questions textarea {
        resize: vertical;
    }
</style>