<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ValidTest;
use DB;
use Auth;
use App\User;
use App\Test;
use App\Question;

class TestController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function getTests() {
        $data = array();

        $data['tests'] = Auth::user()->tests()->get();

        return response()->json($data, 200);
    }

    public function postTest(ValidTest $request) {
        $data = array();

        $test = new Test();
        $test->title = $request['title'];
        $test->count_questions = $request['count_questions'];
        $test->user_id = Auth::user()->id;

        $test->save();

        $data['test'] = $test;

        return response()->json($data, 200);
    }
    public function update(ValidTest $request) {
        $data = array();

        $data['status'] = Test::where('id', $request['test_id'])
        ->where('user_id', Auth::user()->id)
        ->update([
            'title' => $request['title'], 
            'count_questions' =>  $request['count_questions']
            ]);

        return response()->json($data, 200);
    }
    
    public function delete(Request $request) {
        $data = array();

        $data['status'] = Test::where('id', $request['test_id'])
        ->where('user_id', Auth::user()->id)->delete();
        
        if($data['status']) {
            Question::where('test_id', $request['test_id'])->delete();
        }

        return response()->json($data, 200);
    }
}
