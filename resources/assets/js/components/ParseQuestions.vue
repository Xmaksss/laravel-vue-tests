<template>
    <div id="parse-questions">
        <div class="form-group">
            <label for="text">Your text for parsing</label>
            <textarea name="text" v-model="text" id="text" rows="10" class="form-control" placeholder="Insert this your text for parsing"></textarea>
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
    </div>
</template>

<script>
    export default {
        data() {
            return {
                text: '',
                questions: []
            }
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
            }
        }
        
    }
</script>

<style>
    #parse-questions textarea {
        resize: vertical;
    }
    .questions ul {
        list-style: none;
    }
    .questions>ul {
        padding: 0;
    }
    .questions>ul>li>p {
        font-weight: bold;
    }
    .questions>ul ul{
        padding-left: 12px;
    }
    .questions label {
        padding: 3px;
        padding-left: 8px;
        font-weight: normal;
    }
    .questions label input {
        margin-right: 8px;
    }
    .questions .right {
        background: #2ECC71;
        color: #fff;
    }
</style>