<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Test;
use Auth;
use App\Question;

class TestingController extends Controller
{
    public function getQuestions(Request $request) {
        $data = array();

        $test = Test::where('id', $request['test_id'])
        ->where('user_id', Auth::user()->id)
        ->first();
        $data['status'] = false;

        if ($test) {
            $data['test'] = $test;
            /*$questions = Test::find($request['test_id'])
            ->questions()
            ->get();*/

            $questions = \DB::table('questions')
            ->select('questions.id as id', 'questions.answers', 'questions.question')
            ->join('tests', 'tests.id', '=', 'questions.test_id')
            ->inRandomOrder()
            ->where('tests.id', $request['test_id'])
            ->limit($test['count_questions'])
            ->get();

            foreach($questions as $key => $question) {
                $questions[$key]->answers = json_decode($question->answers);
                $questions[$key]->right = null;
            }

            $data['questions'] = $questions;

            $data['status'] = true;
        }
        
        return response()->json($data, 200);
    }

    public function getResults(Request $request) {
        $data = array();
        $result = 0;
        $weight = round(100/count($request['answers']), 5);
        $count = 0;
        $questions = array();

        foreach($request['answers'] as $answer) {
            $question = Question::find($answer['question_id']);
            $question->answer = $answer['answer'];
            $question->answers = json_decode($question->answers);
            $questions[] = $question;

            if($question->right == $answer['answer']) {
                $result += $weight;
                $count++;
            }
        }
        $data['questions'] = $questions;
        $data['results'] = round($result, 2);
        $data['count'] = $count;

        return response()->json($data, 200);
    }
}
