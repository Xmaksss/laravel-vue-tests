@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-sm-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <ul class="nav nav-pills">
                        <li><router-link to="/tests">Tests</router-link></li>
                        <li><router-link to="/parse-questions">Parse Questions</router-link></li>
                    </ul>
                </div>
                <div class="panel-body">
                    <router-view></router-view>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
