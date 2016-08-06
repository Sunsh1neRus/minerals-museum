@extends('...layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Изменить минерал</div>

                <div class="panel-body">

                        @include('...common.messages')

                        <!-- загрузка изображений -->

                        <!-- Форма нового минерала -->
                        <form action="/minerals/{{ $mineral->id }}/update" method="POST" class="form-horizontal">
                          {{ csrf_field() }}

                          <div class="form-group">
                            <label for="mineral" class="col-sm-3 control-label">Название<span style="color: red">*</span></label>

                            <div class="col-sm-6">
                              <input type="text" name="name" id="mineral-name" class="form-control" value="{{ (old('name') === '')?old('name'):$mineral->name }}" placeholder="Например: Алмаз">
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="mineral" class="col-sm-3 control-label">Класс минерала</label>
                            <div class="col-sm-6">
                              <input type="text" name="class" id="mineral-class" class="form-control" value="{{(old('class') === '')?old('class'):$mineral->class }}" placeholder="Например: Самородные элементы">
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="mineral" class="col-sm-3 control-label">Твёрдость(от до)</label>
                            <div class="col-sm-2">
                              <input type="text" name="hardness_before" id="mineral-name" class="form-control" value="{{ (old('hardness_before') === '')?old('hardness_before'):$mineral->hardness_before }}" placeholder="Например: 10">
                            </div>
                            <div class="col-sm-2">
                              <input type="text" name="hardness_after" id="mineral-name" class="form-control" value="{{ (old('hardness_after') === '')?old('hardness_after'):$mineral->hardness_after }}" placeholder="Например: 10">
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="mineral" class="col-sm-3 control-label">Химическая формула</label>

                            <div class="col-sm-6">
                              <div class="btn-toolbar">
                                <div class="btn-group">
                                  <button type="button" class="btn btn-default" id="removeformat" data-toggle="tooltip" title="Обычный текст"><span class="glyphicon glyphicon-font"></span> </button>
                                  <button type="button" class="btn btn-default" id="superscript" data-toggle="tooltip" title="Надстрочный шрифт"><span class="glyphicon glyphicon-superscript"></span></button>
                                  <button type="button" class="btn btn-default" id="subscript" data-toggle="tooltip" title="Подстрочный шрифт"><span class="glyphicon glyphicon-subscript"></span></button>
                                </div>
                              </div>
                              <div id="textarea_chemical_formula" class="form-control" contenteditable="true">{!! (old('chemical_formula') === '')?old('chemical_formula'):$mineral->chemical_formula !!}</div>
                            </div>
                          </div>

                          <script>
                          $('#subscript').on('mousedown', function(){
                              document.execCommand('subscript',null,null);
                              return false;
                          });

                          $('#superscript').on('mousedown', function(){
                              document.execCommand('superscript',null,null);
                              return false;
                          });
                          $('#removeformat').on('mousedown', function(){
                              document.execCommand('removeformat',null,null);
                              return false;
                          });
                          $("#add_mineral").submit( function(eventObj) {
                              $('<input />').attr('type', 'hidden')
                                  .attr('name', "chemical_formula")
                                  .attr('value', $("#textarea_chemical_formula").html())
                                  .appendTo('#add_mineral');
                              return true;
                          });
                          </script>

                          <div class="form-group">
                            <label for="mineral" class="col-sm-3 control-label">Цвет</label>

                            <div class="col-sm-6">
                              <input type="text" name="color" id="mineral-color" class="form-control" value="{{ (old('color') === '')?old('color'):$mineral->color }}" placeholder="Например: Бесцветный">
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="mineral" class="col-sm-3 control-label">Цвет черты</label>

                            <div class="col-sm-6">
                              <input type="text" name="color_in_line" id="mineral-color_in_line" class="form-control" value="{{ (old('color_in_line') === '')?old('color_in_line'):$mineral->color_in_line }}" placeholder="Например: Бесцветный">
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="mineral" class="col-sm-3 control-label">Прозрачность</label>

                            <div class="col-sm-6">
                              <input type="text" name="transparency" id="mineral-transparency" class="form-control" value="{{ (old('transparency') === '')?old('transparency'):$mineral->transparency }}" placeholder="Например: Прозрачный">
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="mineral" class="col-sm-3 control-label">Плотность(г/см<sup>3</sup>) (от до)</label>

                            <div class="col-sm-2">
                              <input type="text" name="density_before" id="mineral-density_before" class="form-control" value="{{ (old('density_before') === '')?old('density_before'):$mineral->density_before }}" placeholder="Например: 3,5">
                            </div>
                            <div class="col-sm-2">
                              <input type="text" name="density_after" id="mineral-density_after" class="form-control" value="{{(old('density_after') === '')?old('density_after'):$mineral->density_after }}" placeholder="Например: 3,5">
                            </div>
                          </div>

                          <div class="form-group">
                             <label for="mineral" class="col-sm-3 control-label">Блеск</label>

                             <div class="col-sm-6">
                               <input type="text" name="shine" id="mineral-shine" class="form-control" value="{{ (old('shine') === '')?old('shine'):$mineral->shine }}" placeholder="Например: Алмазный, жирный">
                             </div>
                           </div>

                           <div class="form-group">
                             <label for="mineral" class="col-sm-3 control-label">Спайность</label>

                             <div class="col-sm-6">
                               <input type="text" name="cleavage" id="mineral-cleavage" class="form-control" value="{{ (old('cleavage') === '')?old('cleavage'):$mineral->cleavage }}" placeholder="Например: Совершенная">
                             </div>
                           </div>

                           <div class="form-group">
                             <label for="mineral" class="col-sm-3 control-label">Излом</label>

                             <div class="col-sm-6">
                               <input type="text" name="fracture" id="mineral-fracture" class="form-control" value="{{ (old('fracture') === '')?old('fracture'):$mineral->fracture }}" placeholder="Например: Раковистый">
                             </div>
                           </div>

                           <div class="form-group">
                             <label for="mineral" class="col-sm-3 control-label">Происхождение</label>

                             <div class="col-sm-6">
                               <input type="text" name="genesis" id="mineral-genesis" class="form-control" value="{{ (old('genesis') === '')?old('genesis'):$mineral->genesis }}" placeholder="Например: Магматическое">
                             </div>
                           </div>

                           <div class="form-group">
                             <label for="mineral" class="col-sm-3 control-label">Практическое применение</label>

                             <div class="col-sm-6">
                               <textarea name="practical_use" rows="4" id="mineral-practical_use" class="form-control" placeholder="Например: Используется в ювелирном деле, электронике">{{ (old('practical_use') === '')?old('practical_use'):$mineral->practical_use }}</textarea>
                             </div>
                           </div>

                           <div class="form-group">
                             <label for="mineral" class="col-sm-3 control-label">Месторождение</label>

                             <div class="col-sm-6">
                               <textarea name="deposit" rows="4" id="mineral-deposit" class="form-control" placeholder="Например: ЮАР (г. Кимберли), Индия, Бразилия, Россия (Уральские горы, Карелия, Кольский полуостров, Якутия), США (штат Арканзас, Мерфрисборо), Австралия (Аргайл, район Кимберли, штат Западная Австралия), Канада (Дайавик, Гахчо-Кью)">{{ (old('deposit') === '')?old('deposit'):$mineral->deposit }}</textarea>
                             </div>
                           </div>

                           <div class="form-group">
                             <label for="mineral" class="col-sm-3 control-label">Описание</label>

                             <div class="col-sm-6">
                               <textarea name="description" rows="6" id="mineral-description" class="form-control" placeholder="Например: АЛМАЗ - минерал, одна из природных кристаллических форм углерода наряду с минералами графит (graphite), лонсдейлит (lonsdaleite) и чаоит (chaoite). Алмаз - драгоценный камень. Самый твёрдый на сегодняшний день минерал, хотя по твёрдости его могут превосходить искусственные аналоги лонсдейлита и фуллерита.">{{ (old('description') === '')?old('description'):$mineral->description }}</textarea>
                             </div>
                           </div>

                          <!-- Кнопка добавления минерала -->
                          <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                              <button type="submit" class="btn btn-default">
                                <i class="fa fa-plus"></i> Применить изменения
                              </button>
                            </div>
                          </div>

                          <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-4">
                               <span style="color: red">*</span><span>- поля, обязательные для заполнения</span>
                            </div>
                          </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
