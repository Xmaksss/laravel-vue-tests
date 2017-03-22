<template>
    <div class="questions">
            <ul>
                <li v-for="question in questions">
                    <div>
                        <div><b>{{question.question}}</b></div>
                        <ul>
                            <li v-for="(answer, index) in question.answers">
                                <label 
                                v-bind:class="{
                                    success: question.right == question.answer && index == question.right || question.right == index && question.right != question.answer, 
                                    error: question.right != question.answer && index == question.answer
                                    }">{{answer}}</label>
                            </li>
                        </ul>
                    </div>
                    <hr>
                </li>
            </ul>
        </div>
</template>

<script>
    export default {
        props: ['questions'],
        data() {
            return {

            }
        }
    }
</script>

<style>
    .error {
        background: #E74C3C;
        color: #fff;
    }
    .success {
        background: #27AE60;
        color: #fff;
    }
</style>