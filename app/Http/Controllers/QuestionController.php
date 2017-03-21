<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ValidQuestion;
use App\Test;
use Auth;
use App\Question;

class QuestionController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function getQuestions(Request $request) {
        $data = array();

        $test = Test::where('id', $request['test_id'])
        ->where('user_id', Auth::user()->id)
        ->first();
        $data['status'] = false;

        if ($test) {
            $data['test'] = $test;
            $questions = Test::find($request['test_id'])
            ->questions()
            ->get();

            foreach($questions as $key => $question) {
                $questions[$key]['answers'] = json_decode($question['answers']);
                if($request['t']) {
                    $questions[$key]['right'] = null;
                }
            }

            $data['questions'] = $questions;

            $data['status'] = true;
        }
        
        return response()->json($data, 200);
    }

    public function postQuestion(ValidQuestion $request) {
        $data = array();

        $question = new Question();

        $test = Test::find($request['test_id']);

        if($test->user_id == Auth::user()->id) {
            $question->question = $request['question'];
            $question->right = $request['right'];
            $question->type = 'radio';
            $question->image = '';
            $question->answers = json_encode($request['answers']);
            $question->test_id = $request['test_id'];

            if($question->save()) {
                $data['question'] = $question;
            } else {
                $data['message'] = 'Error saving :(';
            }
        } else {
            $data['message'] = 'This is not your test!';
        }

        return response()->json($data, 200);
    }

    public function delete(Request $request) {
        $data = array();

        $question = Question::find($request['question_id']);
        $test = Test::find($question->test_id);

        $data['status'] = false;

        if($test->user_id == Auth::user()->id) {
            $data['status'] = Question::where('id', $request['question_id'])
            ->delete();
        }

        return response()->json($data, 200);
    }
    public function parse(Request $request) {
        $data = array();
        $questions = $request['questions'];

        $test = Test::find($request['test_id']);

        $data['status'] = false;

        if($test->user_id == Auth::user()->id) {
            foreach($questions as $item) {
                $question = new Question();
                $question->question = $item['question'];
                $question->right = $item['right'];
                $question->type = 'radio';
                $question->image = '';
                $question->answers = json_encode($item['ans']);
                $question->test_id = $test->id;
                $question->save();
            }
            $data['message'] = 'Saved :)';
            $data['status'] = true;
        } else {
            $data['message'] = 'Error :(';
        }

        return response()->json($data, 200);
    }
}
