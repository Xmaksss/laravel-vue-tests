<template>
    <div id="tests">
        <div class="form-group">
            <button class="btn btn-success" v-if="!showForm" v-on:click="showForm = true">Add new test</button>
        </div>

        <add-test-form v-if="showForm" v-on:testAdded="testAdded" v-on:cancelForm="cancelForm"></add-test-form>

        <div class="form-group">
            <input type="text" v-model="search" class="form-control" placeholder="Search">
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Count</th>
                    <th width="1%"></th>
                    <th width="1%"></th>
                    <th width="1%"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(test,index) in filterTests">
                    <td>
                        <span v-if="testEditing != index" v-on:dblclick="testEdit(index)"><router-link v-bind:to="'test/' + test.id">{{test.title}}</router-link></span>
                        <div class="form-group" v-else>
                            <input type="text" class="form-control" v-model="title">
                        </div>
                    </td>
                    <td class="text-center">
                        <span v-if="testEditing != index">{{test.count_questions}}</span>
                        <div class="form-group" v-else>
                            <input type="number" min="1" class="form-control" v-model="countQuestions">
                        </div>
                    </td>
                    <td>
                        <button v-if="testEditing != index" v-on:click="testEdit(index)" class="btn btn-primary">Edit</button>
                        <button v-if="testEditing == index" v-on:click="testUpdate(index)" class="btn btn-info">Update</button>
                    </td>
                    <td>
                        <button v-if="testEditing != index" v-on:click="testDelete(index)" class="btn btn-danger">Delete</button>
                        <button v-if="testEditing == index" v-on:click="testEditing = null" class="btn btn-default">Cencel</button>
                    </td>
                    <td v-if="testEditing != index">
                        <router-link class="btn btn-success" v-bind:to="'testing/' + test.id">Go</router-link>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>


<script>
    import AddTestForm from './AddTestForm.vue';

    export default {
        data() {
            return {
                search: '',

                showForm: false,
                tests: [],
                testEditing: null,
                title: '',
                countQuestions: 1
            }
        },
        components: {
            'add-test-form': AddTestForm
        },
        mounted() {
            this.$http.get('/api/tests').then(res => {
                //console.log(res);
                this.tests = res.body.tests;
            }, err => {
                console.error(err);
            });
        },
        methods: {
            testAdded(test) {
                this.showForm = false;
                this.tests.push(test);
            },
            cancelForm() {
                this.showForm = false;
            },
            testEdit(index) {
                this.testEditing = index;
                this.title = this.tests[index].title;
                this.countQuestions = this.tests[index].count_questions;
            },
            testUpdate(index) {
                let data = {
                    test_id: this.tests[index].id,
                    title: this.title,
                    count_questions: this.countQuestions
                }
                this.$http.post('/api/tests/update', data).then(res => {
                    if(res.body.status) {
                        this.testEditing = null;
                        this.tests[index].title = this.title;
                        this.tests[index].count_questions = this.countQuestions;
                    }
                }, err => {

                });
            },
            testDelete(index) {
                let data = {
                    test_id: this.tests[index].id
                }
                if(confirm('Delete test?', false)) {
                    this.$http.post('/api/tests/delete', data).then(res => {
                   
                        if(res.body.status) {
                            this.tests.splice(index, 1);
                        } else {
                            console.log('Error');
                        }
                    }, err => {
                        console.error(err);
                    })
                }
            }
        },
        computed: {
            filterTests() {
                var self = this
                return self.tests.filter(function (test) {
                    return test.title.indexOf(self.search) !== -1
                })
            }
        }
    }
</script>