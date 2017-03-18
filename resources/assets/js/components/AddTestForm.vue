<template>
    <form class="form" v-on:submit="AddTest">
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
        </div>
    </form>
</template>

<script>
    export default {
        data() {
            return {
                title: '',
                countQuestions: 1
            }
        },
        methods: {
            AddTest() {
                console.log('add');
                this.$emit('testAdded');
            }
        }
    }
</script>