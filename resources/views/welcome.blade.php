@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Главная</div>

                <div class="panel-body">
                    @include('common.messages')
                    Главная страница. Скоро всё будет
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
