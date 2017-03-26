<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'question', 'right', 'answers'
    ];

    public function test() {
        return $this->belongsTo('App\Test');
    }
}
