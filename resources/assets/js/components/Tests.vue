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
                    <td v-bind:colspan="testEditing == index ? 2 : 1">
                        <span v-if="testEditing != index" v-on:dblclick="testEdit(index)"><router-link v-bind:to="'test/' + test.id">{{test.title}}</router-link></span>
                        <div class="form-group" v-else>
                            <input type="text" class="form-control" v-model="title">
                        </div>
                    </td>
                    <td class="text-center">
                        <span v-if="testEditing != index">{{test.count_questions}} ({{test.questions_count}})</span>
                        <div class="form-group" v-else>
                            <input type="number" min="1" class="form-control" v-model="countQuestions">
                        </div>
                    </td>
                    <td>
                        <button v-if="testEditing != index" v-on:click="testEdit(index)" class="btn btn-primary btn-xs">Edit</button>
                        <button v-if="testEditing == index" v-on:click="testUpdate(index)" class="btn btn-info btn-xs">Update</button>
                    </td>
                    <td>
                        <button v-if="testEditing != index" v-on:click="testDelete(index)" class="btn btn-danger btn-xs">Delete</button>
                        <button v-if="testEditing == index" v-on:click="testEditing = null" class="btn btn-default btn-xs">Cencel</button>
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
                alertify.error('Error! :(');
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
                        alertify.success('Test was update :)');
                        this.testEditing = null;
                        this.tests[index].title = this.title;
                        this.tests[index].count_questions = this.countQuestions;
                    }
                }, err => {
                    alertify.error('Error! :(');
                });
            },
            testDelete(index) {
                let data = {
                    test_id: this.tests[index].id
                }
                 alertify.confirm('Delete Test?', 'Deleting', () => {
                    this.$http.post('/api/tests/delete', data).then(res => {
                   
                        if(res.body.status) {
                            alertify.success('Test was delete');
                            this.tests.splice(index, 1);
                        } else {
                            alertify.error('Error! :(');
                        }
                    }, err => {
                        console.error(err);
                        alertify.error('Error! :(');
                    })
                 }, () => {}).set('labels', {ok:'Yes', cancel:'Cancel'});;
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

<style>
    input[type=number] {
        padding-right: 2px;
    }
</style>