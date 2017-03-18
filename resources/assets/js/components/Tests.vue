<template>
    <div id="tests">
        <button class="btn btn-success" v-if="!showForm" v-on:click="showForm = true">Add new test</button>
        <add-test-form v-if="showForm" v-on:testAdded="showForm = false"></add-test-form>
        <ul>
            <li>test 1</li>
            <li>test 2</li>
        </ul>
    </div>
</template>


<script>
    import AddTestForm from './AddTestForm.vue';

    export default {
        data() {
            return {
                showForm: false
            }
        },
        components: {
            'add-test-form': AddTestForm
        }
    }
</script>