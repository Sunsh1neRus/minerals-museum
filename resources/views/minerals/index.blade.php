@extends('...layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <main class="col-md-9">
            <div class="panel panel-default">

                <div class="panel-heading-panel">Список минералов
                    <div class="btn-group pull-right">
                        <a href="#" id="list" class="btn btn-default btn-md"><span class="glyphicon glyphicon-th-list"></span> Список</a>
                        <a href="#" id="grid" class="btn btn-default btn-md"><span class="glyphicon glyphicon-th-large"></span> Плитка</a>
                    </div>
                </div>

                <div class="panel-body">
                @include('...common.messages')

                    <div id="minerals">
                    @foreach ($minerals as $mineral)
                        <div class="row">
                        <div class="col-md-4">
                          <a href="/minerals/{{ $mineral->id }}">
                            <img class="thumbnail img-responsive center-block" src="{{ (!empty($mineral->mineralsImages[0]))?$mineral->mineralsImages[0]->url_middle:'/images/non-image-middle.jpg' }}">
                          </a>
                        </div>
                          <div class="col-md-8">
                            <a href="/minerals/{{ $mineral->id }}"><h4 class="media-heading">{{ $mineral->name }}</h4></a>

                            <ul>
                                <li>Класс минерала: {{ $mineral->class }}</li>
                                <li>Химическая формула: {!! clean($mineral->chemical_formula,'chemical_formula') !!}</li>
                                <li>Происхождение: {{ $mineral->genesis }}</li>
                            </ul>
                          <div class="pull-left media-bottom bottom-left">
                              <span class="posted-on">
                                  <i class="glyphicon glyphicon-time"> </i>
                                  <time class="entry-date published" datetime="2012-03-14T09:49:22+00:00" title="{{ $mineral->created_at->format('d M Y') }}">{{ $mineral->created_at->format('d M Y \в H:i') }}</time>
                              </span>
                              <span class="byline">
                                  <i class="glyphicon glyphicon-user"> </i>
                                  <span class="author vcard">
                                      <a class="url fn n" href="/users/{{ $mineral->user_id }}">{{ $mineral->user->name }}</a>
                                  </span>
                              </span>
                          </div>
                          </div>
                        </div>
                        <hr>
                    @endforeach
                    </div>
                    <div class="row col-lg-12">
                        <div class="pull-right link">{!! $minerals->render() !!}</div>
                    </div>
                </div>
            </div>
        </main>
        <aside role="complementary" class="col-md-3">
            <div id="search_block">
                / Search
            </div>
            <div id="search_block">
                / most pop minerals
            </div>
            <div id="search_block">
                / random minerals
            </div>
            <div id="search_block">
                / last updated minerals
            </div>
        </aside>
    </div>
</div>
@endsection
