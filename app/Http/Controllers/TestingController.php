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
            ->limit($test['limit'])
            ->get();

            foreach($questions as $key => $question) {
                $questions[$key]->answers = json_decode($question->answers);
                //$questions[$key]->right = null;
            }

            $data['questions'] = $questions;

            $data['status'] = true;
        }
        
        return response()->json($data, 200);
    }

    public function getResults(Request $request) {
        $data = array();
        $result = 0;
        $weight = round(100/count($request['answers']), 2);
        $count = 0;

        foreach($request['answers'] as $answer) {
            if(Question::find($answer['question_id'])->right == $answer['answer']) {
                $result += $weight;
                $count++;
            }
        }
        $data['results'] = $result;
        $data['count'] = $count;

        return response()->json($data, 200);
    }
}
