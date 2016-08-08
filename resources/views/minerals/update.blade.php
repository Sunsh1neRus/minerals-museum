@extends('...layouts.app')

@section('content')
<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/js/jquery-ui-1.12.0.custom/jquery-ui.css') }}">
<script type="text/javascript" src="{{ URL::asset('assets/js/jquery-ui-1.12.0.custom/jquery-ui.js') }}"></script>

<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Изменить минерал</div>

                <div class="panel-body">

                        @include('...common.messages')

                        <!-- загрузка изображений -->
                         <form id="image_upload_form" action="#" class="form-horizontal" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="mineral" class="col-sm-3 control-label">Изображения минерала<br><sup>( Максимум 30 изображений )</sup></label>
                                <div class="col-sm-6">
                                    <input type="file" name="image" id="mineral_image" multiple accept="image/jpeg,image/png" class="file-loading">
                                    <br>

                         	        <!-- блок превью-изображений -->
                         	        <div id="thumb_block"></div>
                                </div>
                            </div>
                         </form>

                         <script>
                         $(document).on("click", '#thumb_block .close', function(){
                             var this_element = $(this);
                             $.ajax({
                             	url: '/images/delete-mineral-image',
                             	type: 'POST',
                             	data: 'image_id='+this_element.parent().data('id'),
                             	// Функция удачного ответа с сервера
                             	success: function(result) {
                             		if (result.success === 1) {
                                         messages.fromJson(result);
                                         this_element.parent().remove();
                             		} else if (result.success === 0) {
                                        messages.fromJson(result);
                                    } else {
                                    messages.appendError('Внутренняя ошибка сервера.');
                                    }
                             	},
                             	// Что-то пошло не так
                             	error: function (result) {
                             		if (result.responseJSON.success === 0) {
                                        messages.fromJson(result.responseJSON);
                                    } else {
                                    messages.appendError('Внутренняя ошибка сервера.');
                                    }
                             	}
                             });
                         });

                         $(document).on("click", '#thumb_block .main_image', function(){
                            var thumb_block = $('#thumb_block');
                            if ($(this).find('i').hasClass('fa-square-o')) {
                                thumb_block.find('.fa-check-square-o').parents('.single-thumb-block').removeAttr('data-main');
                                thumb_block.find('.fa-check-square-o').removeClass('fa-check-square-o').addClass('fa-square-o');
                                $(this).parents('.single-thumb-block').attr('data-main','1');
                                $(this).find('i').removeClass('fa-square-o').addClass('fa-check-square-o');
                            }
                         });
                         $('#thumb_block').bind("DOMSubtreeModified",function(){
                             updateWarningState($('#thumb_block'));
                         });
                         function updateWarningState(thumbBlock){
                            if (thumbBlock.find('div[data-main="1"]').length){
                                thumbBlock.parent().find('#warning').remove();
                            } else {
                                if(thumbBlock.parent().find('#warning').length){} else
                                    thumbBlock.parent().append('<span id="warning" class="label label-warning">Выберите главное изображение!</span>');
                            }
                         }
                         function addThumb(thumbBlock,image_id,url){
                             thumbBlock.append('<div data-id="' + image_id + '" class="single-thumb-block"><img src="' + url + '" id="thumb"><div class="close-background"></div><span class="close" data-toggle="tooltip" title="Удалить это изображение">×</span><div class="button-background"><span class="main_image" data-toggle="tooltip" title="Сделать это изображение гланым"><i class="fa fa-square-o fa-3x" aria-hidden="true"></i></span></div></div>');
                         }
                         <?php
                            $images_ids = [];
                            $main_img_id = null;
                            foreach($mineral->mineralsImages as $image) {
                                $images_ids[] = $image->id;
                                if ($image->main_image_of_mineral) {
                                    $main_img_id = $image->id;
                                }
                            }
                            $images_ids = old('images_ids')?old('images_ids'):$images_ids;
                            $main_img_id = old('main_image_id')?old('main_image_id'):$main_img_id;
                            if (is_array($images_ids) AND !empty($images_ids)) {
                                $ar_images = \App\MineralsImage::whereIn('id',$images_ids)->get();
                                if (!is_null($ar_images)) {
                                    foreach($ar_images as $image) {
                                        echo 'addThumb($("#thumb_block"),"' . $image->id . '","' . $image->url_middle . '")' . PHP_EOL;
                                        if ((int)$image->id === (int)$main_img_id){
                                            echo '$(document).ready(function(){$("#thumb_block div[data-id=\'' . $image->id . '\'] .main_image").trigger("click");});' . PHP_EOL;
                                        }
                                    }
                                }
                            }
                         ?>
                         function addProgressBar(thumbBlock){
                            thumbBlock.append('<div class="progress"><div class="progress-bar" style="width:0%"></div></div>');
                         }
                         function delProgressBar(thumbBlock){
                            thumbBlock.find('div.progress').first().remove();
                         }

                         $(document).ready(function(){
                            var uploadForm = $('#image_upload_form'),
                                uploadInput = $('#mineral_image'); // Инпут с файлом

                            uploadInput.on('change', function(){
                                // Скроем все сообщения
                                messages.removeAll();
                                var formdata = new FormData(uploadForm[0]);
                                $.each(formdata.getAll('image'),function(i){
                                    var tmpFormData = new FormData();
                                    tmpFormData.set('_token',formdata.get('_token'));
                                    tmpFormData.set('image',formdata.getAll('image')[i]);
                                    uploadImage(uploadForm,tmpFormData);
                                });
                         	});
                         	function uploadImage($form,$formData){
                         	    addProgressBar($form.find('#thumb_block'));
                         		var request = new XMLHttpRequest();
                         		//progress event...
                         		request.upload.addEventListener('progress',function(e){
                         			var percent = Math.round(e.loaded/e.total * 100);
                         			$form.find('.progress-bar').first().width(percent+'%').html(percent+'%');
                         		});
                         		//progress completed load event
                         		request.addEventListener('loadend',function(e){
                         		    delProgressBar($form.find('#thumb_block'));
                         		    try {
                         		        var jsonResp = JSON.parse(request.responseText);
                         		        if (jsonResp.success === 1) {
                                            addThumb($form.find('#thumb_block'),jsonResp.image_id,jsonResp.url_m_image);
                                        } else {
                                            // покажем ошибку
                                            messages.fromJson(jsonResp);
                                        }
                         		    } catch(e) {
                                        // покажем ошибку 500
                                        messages.appendError('Внутренняя ошибка сервера.');
                         		    }
                         		});
                         		request.open('POST', '/images/upload-mineral-image');
                         		request.send($formData);
                         	}
                         });
                         </script>

                        <!-- Форма нового минерала -->
                        <form id="add_mineral" action="/minerals/{{ $mineral->id }}/update" method="POST" class="form-horizontal">
                          {{ csrf_field() }}

                          <div class="form-group @if ($errors->has('name')) has-error @endif">
                            <label for="mineral" class="col-sm-3 control-label">Название<span style="color: red">*</span></label>

                            <div class="col-sm-6">
                              <input type="text" name="name" id="mineral-name" class="form-control" value="{{ old('name',$mineral->name) }}" placeholder="Например: Алмаз">
                              @if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
                            </div>
                          </div>

                          <div class="form-group @if ($errors->has('class')) has-error @endif">
                            <label for="mineral" class="col-sm-3 control-label">Класс минерала</label>
                            <div class="col-sm-6">
                              <input type="text" name="class" id="mineral-class" class="form-control" value="{{ old('class',$mineral->class) }}" placeholder="Например: Самородные элементы">
                              @if ($errors->has('class')) <p class="help-block">{{ $errors->first('class') }}</p> @endif
                            </div>
                          </div>

                          <div class="form-group @if ($errors->has('hardness_before') or $errors->has('hardness_after')) has-error @endif">
                            <label for="mineral" class="col-sm-3 control-label">Твёрдость(от до)</label>
                            <div class="col-sm-2">
                              <input type="text" name="hardness_before" id="mineral-hardness_before" class="form-control" value="{{ old('hardness_before',$mineral->hardness_before) }}" placeholder="Например: 10">
                              @if ($errors->has('hardness_before')) <p class="help-block">{{ $errors->first('hardness_before') }}</p> @endif
                            </div>
                            <div class="col-sm-2">
                              <input type="text" name="hardness_after" id="mineral-hardness_after" class="form-control" value="{{ old('hardness_after',$mineral->hardness_after) }}" placeholder="Например: 10">
                              @if ($errors->has('hardness_after')) <p class="help-block">{{ $errors->first('hardness_after') }}</p> @endif
                            </div>
                          </div>

                          <div class="form-group @if ($errors->has('chemical_formula')) has-error @endif">
                            <label for="mineral" class="col-sm-3 control-label">Химическая формула</label>

                            <div class="col-sm-6">
                              <div class="btn-toolbar">
                                <div id="formula_btns" class="btn-group">
                                  <button type="button" class="btn btn-default" id="removeFormat" data-toggle="tooltip" title="Обычный текст"><span class="glyphicon glyphicon-font"></span></button>

                                  <button type="button" class="btn btn-default" id="Subscript" data-toggle="tooltip" title="Подстрочный шрифт (Ctrl + ,)"><span class="glyphicon glyphicon-subscript"></span></button>
                                  <button type="button" class="btn btn-default" id="Superscript" data-toggle="tooltip" title="Надстрочный шрифт (Ctrl + .)"><span class="glyphicon glyphicon-superscript"></span></button>
                                </div>
                              </div>
                              <div id="mineral-chemical_formula" class="form-control" contenteditable="true">{!! clean(old('chemical_formula',$mineral->chemical_formula),'chemical_formula') !!}</div>
                              @if ($errors->has('chemical_formula')) <p class="help-block">{{ $errors->first('chemical_formula') }}</p> @endif
                            </div>
                          </div>

                          <script>
                                $(document).ready(function(){
                                    function updateBtnsState(){
                                        $('#formula_btns button').each(function( index ) {
                                            if(document.queryCommandState ($(this).attr('id'))){
                                                $(this).addClass('active');
                                            } else {
                                                $(this).removeClass('active');
                                            }
                                        });
                                    }
                                    $('#mineral-chemical_formula').bind("DOMSubtreeModified",function(){
                                        updateBtnsState();
                                    });
                                    $(document).on('click','#formula_btns button',function(){
                                        if (document.execCommand($(this).attr('id'))){
                                            updateBtnsState();
                                        }
                                    });
                                    <!-- HotKeys -->
                                    document.onkeydown = function(e) {
                                        if (e.ctrlKey && e.keyCode == 190) {
                                            if (document.execCommand('Superscript')){
                                                updateBtnsState();
                                            }
                                            return false;
                                        }

                                        if (e.ctrlKey && e.keyCode == 188) {
                                            if (document.execCommand('Subscript')){
                                                updateBtnsState();
                                            }
                                            return false;
                                        }
                                    };
                                });
                          </script>

                          <div class="form-group @if ($errors->has('color')) has-error @endif">
                            <label for="mineral" class="col-sm-3 control-label">Цвет</label>

                            <div class="col-sm-6">
                              <input type="text" name="color" id="mineral-color" class="form-control" value="{{ old('color',$mineral->color) }}" placeholder="Например: Бесцветный">
                              @if ($errors->has('color')) <p class="help-block">{{ $errors->first('color') }}</p> @endif
                            </div>
                          </div>

                          <div class="form-group @if ($errors->has('color_in_line')) has-error @endif">
                            <label for="mineral" class="col-sm-3 control-label">Цвет черты</label>

                            <div class="col-sm-6">
                              <input type="text" name="color_in_line" id="mineral-color_in_line" class="form-control" value="{{ old('color_in_line',$mineral->color_in_line) }}" placeholder="Например: Бесцветный">
                              @if ($errors->has('color_in_line')) <p class="help-block">{{ $errors->first('color_in_line') }}</p> @endif
                            </div>
                          </div>

                          <div class="form-group @if ($errors->has('transparency')) has-error @endif">
                            <label for="mineral" class="col-sm-3 control-label">Прозрачность</label>

                            <div class="col-sm-6">
                              <input type="text" name="transparency" id="mineral-transparency" class="form-control" value="{{ old('transparency',$mineral->transparency) }}" placeholder="Например: Прозрачный">
                              @if ($errors->has('transparency')) <p class="help-block">{{ $errors->first('transparency') }}</p> @endif
                            </div>
                          </div>

                          <div class="form-group @if ($errors->has('density_before') OR $errors->has('density_after')) has-error @endif">
                            <label for="mineral" class="col-sm-3 control-label">Плотность(г/см<sup>3</sup>) (от до)</label>

                            <div class="col-sm-2">
                              <input type="text" name="density_before" id="mineral-density_before" class="form-control" value="{{ old('density_before',$mineral->density_before) }}" placeholder="Например: 3,5">
                              @if ($errors->has('density_before')) <p class="help-block">{{ $errors->first('density_before') }}</p> @endif
                            </div>
                            <div class="col-sm-2">
                              <input type="text" name="density_after" id="mineral-density_after" class="form-control" value="{{ old('density_after',$mineral->density_after) }}" placeholder="Например: 3,5">
                              @if ($errors->has('density_after')) <p class="help-block">{{ $errors->first('density_after') }}</p> @endif
                            </div>
                          </div>

                          <div class="form-group @if ($errors->has('shine')) has-error @endif">
                             <label for="mineral" class="col-sm-3 control-label">Блеск</label>

                             <div class="col-sm-6">
                               <input type="text" name="shine" id="mineral-shine" class="form-control" value="{{ old('shine',$mineral->shine) }}" placeholder="Например: Алмазный, жирный">
                               @if ($errors->has('shine')) <p class="help-block">{{ $errors->first('shine') }}</p> @endif
                             </div>
                           </div>

                           <div class="form-group @if ($errors->has('cleavage')) has-error @endif">
                             <label for="mineral" class="col-sm-3 control-label">Спайность</label>

                             <div class="col-sm-6">
                               <input type="text" name="cleavage" id="mineral-cleavage" class="form-control" value="{{ old('cleavage',$mineral->cleavage) }}" placeholder="Например: Совершенная">
                               @if ($errors->has('cleavage')) <p class="help-block">{{ $errors->first('cleavage') }}</p> @endif
                             </div>
                           </div>

                           <div class="form-group @if ($errors->has('fracture')) has-error @endif">
                             <label for="mineral" class="col-sm-3 control-label">Излом</label>

                             <div class="col-sm-6">
                               <input type="text" name="fracture" id="mineral-fracture" class="form-control" value="{{ old('fracture',$mineral->fracture) }}" placeholder="Например: Раковистый">
                               @if ($errors->has('fracture')) <p class="help-block">{{ $errors->first('fracture') }}</p> @endif
                             </div>
                           </div>

                           <div class="form-group @if ($errors->has('genesis')) has-error @endif">
                             <label for="mineral" class="col-sm-3 control-label">Происхождение</label>

                             <div class="col-sm-6">
                               <input type="text" name="genesis" id="mineral-genesis" class="form-control" value="{{ old('genesis',$mineral->genesis) }}" placeholder="Например: Магматическое">
                               @if ($errors->has('genesis')) <p class="help-block">{{ $errors->first('genesis') }}</p> @endif
                             </div>
                           </div>

                           <div class="form-group @if ($errors->has('practical_use')) has-error @endif">
                             <label for="mineral" class="col-sm-3 control-label">Практическое применение</label>

                             <div class="col-sm-6">
                               <textarea name="practical_use" rows="4" id="mineral-practical_use" class="form-control" placeholder="Например: Используется в ювелирном деле, электронике">{{ old('practical_use',$mineral->practical_use) }}</textarea>
                               @if ($errors->has('practical_use')) <p class="help-block">{{ $errors->first('practical_use') }}</p> @endif
                             </div>
                           </div>

                           <div class="form-group @if ($errors->has('deposit')) has-error @endif">
                             <label for="mineral" class="col-sm-3 control-label">Месторождение</label>

                             <div class="col-sm-6">
                               <textarea name="deposit" rows="5" id="mineral-deposit" class="form-control" placeholder="Например: ЮАР (г. Кимберли), Индия, Бразилия, Россия (Уральские горы, Карелия, Кольский полуостров, Якутия), США (штат Арканзас, Мерфрисборо), Австралия (Аргайл, район Кимберли, штат Западная Австралия), Канада (Дайавик, Гахчо-Кью)">{{ old('deposit',$mineral->deposit) }}</textarea>
                               @if ($errors->has('deposit')) <p class="help-block">{{ $errors->first('deposit') }}</p> @endif
                             </div>
                           </div>

                           <div class="form-group @if ($errors->has('description')) has-error @endif">
                             <label for="mineral" class="col-sm-3 control-label">Описание</label>

                             <div class="col-sm-6">
                               <textarea name="description" rows="7" id="mineral-description" class="form-control" placeholder="Например: АЛМАЗ - минерал, одна из природных кристаллических форм углерода наряду с минералами графит (graphite), лонсдейлит (lonsdaleite) и чаоит (chaoite). Алмаз - драгоценный камень. Самый твёрдый на сегодняшний день минерал, хотя по твёрдости его могут превосходить искусственные аналоги лонсдейлита и фуллерита.">{{ old('description',$mineral->description) }}</textarea>
                               @if ($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
                             </div>
                           </div>
                            @if (request()->user()->is('admin|moderator'))
                           <div class="form-group @if ($errors->has('seen')) has-error @endif">
                             <div class="col-sm-6 col-sm-offset-3">
                               <div class="checkbox checkbox-success">
                                 <label><input name="seen" type="checkbox"@if(old('seen',$mineral->seen) == true) checked @endif  value="1">Информация достоверна и проверена модератором</label>
                                 @if ($errors->has('seen')) <p class="help-block">{{ $errors->first('seen') }}</p> @endif
                               </div>
                             </div>
                           </div>
                           @endif


                          <!-- Кнопка добавления минерала -->
                          <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                              <button type="submit" class="btn btn-default">
                                <i class="fa fa-plus"></i> Применить изменения
                              </button>
                            </div>
                          </div>

                          <script>
                          $("#add_mineral").submit( function(eventObj) {
                              $('<input />').attr('type', 'hidden')
                                  .attr('name', "chemical_formula")
                                  .attr('value', $("#mineral-chemical_formula").html())
                                  .appendTo('#add_mineral');

                              $.each($('#thumb_block').find('div.single-thumb-block'),function(i,v) {
                                $('<input />').attr('type', 'hidden')
                                   .attr('name', "images_ids[]")
                                   .attr('value', $(v).data('id'))
                                   .appendTo('#add_mineral');
                                if ($(v).data('main') == 1) {
                                    $('<input />').attr('type', 'hidden')
                                        .attr('name', "main_image_id")
                                        .attr('value', $(v).data('id'))
                                        .appendTo('#add_mineral');
                                }
                              });

                              return true;
                          });
                          </script>

                          <!-- autocomplete -->
                          <script>
                          var ar_autocomplete_fields = ['class','color','color_in_line','transparency','shine','cleavage','fracture','genesis'];
                          for (var i = 0, len = ar_autocomplete_fields.length; i < len; i++) {
                            determine_autocomplete(ar_autocomplete_fields[i]);
                          }
                          function determine_autocomplete(field){
                            $( "#mineral-"+field ).autocomplete({
                                minLength: 2,
                                source: function(request, response){
                                                $.ajax({
                                                    type: 'POST',
                                                    dataType: 'json',
                                                    url : '/minerals/autocomplete',

                                                    data:{
                                                        field: field,
                                                        term: request.term // поисковая фраза
                                                    },
                                                    success: function(data){
                                                        response($.map(data.response, function(item){
                                                            return {
                                                                value: item.value,
                                                                label: item.label
                                                            }
                                                        }));
                                                    }
                                                });
                                            }
                            });
                            }
                          </script>
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
