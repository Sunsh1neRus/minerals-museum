@extends('...layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-body">
                @include('...common.messages')
                @if ($mineral->seen == false)
                    <script>
                        messages.appendWarning('Информация на данной странице ещё не проверена модератором на достоверность.');
                    </script>
                @endif
                <!--
                            $table->text('description')->nullable();
                            $table->float('hardness_before')->nullable();
                            $table->float('hardness_after')->nullable();
                            $table->string('color')->nullable();
                            $table->string('color_in_line')->nullable();
                            $table->string('transparency')->nullable();
                            $table->float('density_before')->nullable();
                            $table->float('density_after')->nullable();
                            $table->string('shine')->nullable();
                            $table->string('cleavage')->nullable();
                            $table->string('fracture')->nullable();
                            $table->text('practical_use')->nullable();
                            $table->text('deposit')->nullable();

                            $table->boolean('seen')->default(false);
                            $table->integer('last_updater_id')->unsigned()->nullable();
                            $table->foreign('last_updater_id')->references('id')->on('users');
                            $table->timestamps();
                -->
                <div class="page-header">
                  <h1>{{ $mineral->name }}</h1>
                </div>
                    <div id="mineral">
                        <div class="media">
                          <a class="pull-left" href="#">
                            <img class="media-object" src="{{ (!empty($mineral->mineralsImages[0]))?$mineral->mineralsImages[0]->url_original:'/images/non-image-148x223.jpeg' }}" style="min-width: 200px; min-height: 200px; max-width: 70%; max-height: 70%;">
                          </a>
                          <div class="media-body">
                            <ul>
                                <li>Класс минерала: {{ $mineral->class }}</li>
                                <li>Химическая формула: {!! clean($mineral->chemical_formula,'chemical_formula') !!}</li>
                                <li>Происхождение: {{ $mineral->genesis }}</li>
                                <li>Автор: <a href="/users/{{ $mineral->user_id }}">{{ $mineral->user->name }}</a></li>
                            </ul>
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
