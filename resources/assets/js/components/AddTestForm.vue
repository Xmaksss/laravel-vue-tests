<template>
    <form class="form" v-on:submit.prevent="addTest">
        <div class="errors" v-for="errors in fields_errors">
            <div class="alert alert-danger" role="alert" v-for="error in errors">{{error}}</div>
        </div>
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" v-model="title" class="form-control" placeholder="title">
        </div>
        <div class="form-group">
            <label for="count_questions">Count Questions</label>
            <input type="number" min="1" id="count_questions" v-model="countQuestions" class="form-control" placeholder="title">
        </div>
        <div class="form-group">
            <button class="btn btn-primary" type="submit">Save</button>
            <button class="btn btn-default" type="button" v-on:click="cancelForm">Cancel</button>
        </div>
        <hr>
    </form>
</template>

<script>
    export default {
        data() {
            return {
                title: '',
                countQuestions: 1,
                fields_errors: []
            }
        },
        methods: {
            addTest() {
                let data = {
                    title: this.title,
                    count_questions: this.countQuestions
                }
                this.$http.post('/api/tests', data).then(res => {
                    console.log(res);
                    this.$emit('testAdded', res.body.test);
                }, 
                err => {
                    console.log(err);
                    if(err.body) {
                        this.fields_errors = err.body;
                    }
                })
                
            },
            cancelForm() {
                this.$emit('cancelForm');
            }
        }
    }
</script>

<style>
    .alert {
        padding: 6px 12px;
        margin-bottom: 12px;
    }
</style>