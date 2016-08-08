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
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="pull-left">{{ $mineral->name }}</h1>
                        @if (!is_null(request()->user()) AND (request()->user()->is('admin|moderator') OR ((request()->user()->is('editor')) AND ($mineral->user_id === request()->user()->id))))
                            <div class="btn-group pull-right" style="margin: 21px 0px 10px 0px">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Действия <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="/minerals/{{ $mineral->id }}/update"><i class="fa fa-pencil"></i> Изменить</a></li>
                                    <li class="divider"></li>
                                    <li><a href="/minerals/{{ $mineral->id }}/delete"><i class="fa fa-trash-o"></i> Удалить</a></li>
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
                <?php
                    $main_img = null;
                    if (!empty($mineral->mineralsImages[0])) {
                        foreach($mineral->mineralsImages as $image){
                            if ($image->main_image_of_mineral) {
                                $main_img = $image;
                                break;
                            }
                        }
                        if (is_null($main_img)) {
                            $main_img = $mineral->mineralsImages[0];
                        }
                    }
                ?>

                <hr>
                    <div id="mineral">
                        <div class="row">
                            <div class="col-md-10">
                                <a id="full" href="{{ $main_img?$main_img->url_original:'/images/non-image-original.jpg' }}"><img class="thumbnail img-responsive" src="{{ $main_img?$main_img->url_original:'/images/non-image-original.jpg' }}" alt="{{ $main_img?$main_img->description:'' }}"></a>
                            </div>
                            <div class="col-sm-2">
                                @foreach($mineral->mineralsImages as $thumb)
                                <a id="to_full" data-url="{{ $thumb->url_original }}" href="#"><img class="img-responsive" src="{{ $thumb->url_middle }}" alt="{{ $thumb->description }}" style="padding-bottom: 5px;"></a>
                                @endforeach
                            </div>
                        </div>
                        <script>
                            $(document).ready(function(){
                                $(document).on('click','#to_full',function(){
                                    $('#full').attr('href',$(this).data('url')).find('img').attr('src',$(this).data('url')).attr('alt',$(this).find('img').attr('alt'));
                                });
                            });
                        </script>
                        <ul>
                            <li>Класс минерала: {{ $mineral->class }}</li>
                            <li>Химическая формула: {!! clean($mineral->chemical_formula,'chemical_formula') !!}</li>
                            <li>Происхождение: {{ $mineral->genesis }}</li>
                              <!--
                              $table->text('description')->nullable();
                              -->
                            <li>Твёрдость: {{ $mineral->hardness_before . ' - ' . $mineral->hardness_after }}</li>
                            <li>Цвет: {{ $mineral->color }}</li>
                            <li>Цвет черты: {{ $mineral->color_in_line }}</li>
                            <li>Прозрачность: {{ $mineral->transparency }}</li>
                            <li>Плотность: {{ $mineral->density_before . ' - ' . $mineral->density_after }}</li>
                            <li>Блеск: {{ $mineral->shine }}</li>
                            <li>Спайность: {{ $mineral->cleavage }}</li>
                            <li>Излом: {{ $mineral->fracture }}</li>
                            <li>Практическое применение: {{ $mineral->practical_use }}</li>
                            <li>Месторождение: {{ $mineral->deposit }}</li>
                            <hr>
                            <li>Добавлен: {{ $mineral->created_at }}</li>
                            <li>Отредактирован: {{ $mineral->updated_at }}</li>
                            <li>Автор: <a href="/users/{{ $mineral->user_id }}">{{ $mineral->user->name }}</a></li>
                            @if ($mineral->last_updater_id)
                            <li>Последний редактировавший: <a href="/users/{{ $mineral->last_updater_id }}">{{ $mineral->lastUpdater->name }}</a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
