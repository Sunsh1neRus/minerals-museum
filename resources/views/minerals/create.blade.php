@extends('...layouts.app')

@section('content')
<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/js/jquery-ui-1.12.0.custom/jquery-ui.css') }}">
<script type="text/javascript" src="{{ URL::asset('assets/js/jquery-ui-1.12.0.custom/jquery-ui.js') }}"></script>

<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Добавить новый минерал</div>

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
                            delWarning(thumb_block);
                         });
                         function addWarning(thumbBlock){
                            if(thumbBlock.parent().find('#warning').length){} else
                                thumbBlock.parent().append('<span id="warning" class="label label-warning">Выберите главное изображение!</span>');
                         }
                         function delWarning(thumbBlock){
                            thumbBlock.parent().find('#warning').remove();
                         }
                         function addThumb(thumbBlock,image_id,url){
                             thumbBlock.append('<div data-id="' + image_id + '" class="single-thumb-block"><img src="' + url + '" id="thumb"><div class="close-background"></div><span class="close" data-toggle="tooltip" title="Удалить это изображение">×</span><div class="button-background"><span class="main_image" data-toggle="tooltip" title="Сделать это изображение гланым"><i class="fa fa-square-o fa-3x" aria-hidden="true"></i></span></div></div>');
                             addWarning(thumbBlock);
                         }
                         <?php
                            $images_ids = old('images_ids');
                            if (is_array($images_ids) AND !empty($images_ids)) {
                                $ar_images = \App\MineralsImage::whereIn('id',$images_ids)->get();
                                if (!is_null($ar_images)) {
                                    foreach($ar_images as $image) {
                                        echo 'addThumb($("#thumb_block"),"' . $image->id . '","' . $image->url_middle . '")' . PHP_EOL;
                                        if ((int)$image->id === (int)old('main_image_id')){
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
                        <form id="add_mineral" action="/minerals/create" method="POST" class="form-horizontal">
                          {{ csrf_field() }}

                          <div class="form-group @if ($errors->has('name')) has-error @endif">
                            <label for="mineral" class="col-sm-3 control-label">Название<span style="color: red">*</span></label>

                            <div class="col-sm-6">
                              <input type="text" name="name" id="mineral-name" class="form-control" value="{{ old('name') }}" placeholder="Например: Алмаз">
                              @if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="mineral" class="col-sm-3 control-label">Класс минерала</label>
                            <div class="col-sm-6">
                              <input type="text" name="class" id="mineral-class" class="form-control" value="{{ old('class') }}" placeholder="Например: Самородные элементы">
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="mineral" class="col-sm-3 control-label">Твёрдость(от до)</label>
                            <div class="col-sm-2">
                              <input type="text" name="hardness_before" id="mineral-hardness_before" class="form-control" value="{{ old('hardness_before') }}" placeholder="Например: 10">
                            </div>
                            <div class="col-sm-2">
                              <input type="text" name="hardness_after" id="mineral-hardness_after" class="form-control" value="{{ old('hardness_after') }}" placeholder="Например: 10">
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="mineral" class="col-sm-3 control-label">Химическая формула</label>

                            <div class="col-sm-6">
                              <div class="btn-toolbar">
                                <div class="btn-group">
                                  <button type="button" class="btn btn-default" id="removeformat" data-toggle="tooltip" title="Обычный текст"><span class="glyphicon glyphicon-font"></span> </button>
                                  <button type="button" class="btn btn-default" id="subscript" data-toggle="tooltip" title="Подстрочный шрифт" onclick="document.execCommand('Subscript')"><span class="glyphicon glyphicon-subscript"></span></button>
                                  <button type="button" class="btn btn-default" id="superscript" data-toggle="tooltip" title="Надстрочный шрифт" onclick="document.execCommand('Superscript')"><span class="glyphicon glyphicon-superscript"></span></button>
                                </div>
                              </div>
                              <div id="mineral-chemical_formula" class="form-control" contenteditable="true">{!! clean(old('chemical_formula'),'chemical_formula') !!}</div>
                            </div>
                          </div>

                          <script>
                          $('#removeformat').on('mousedown', function(){
                              document.execCommand('removeformat');
                          });
                          </script>

                          <div class="form-group">
                            <label for="mineral" class="col-sm-3 control-label">Цвет</label>

                            <div class="col-sm-6">
                              <input type="text" name="color" id="mineral-color" class="form-control" value="{{ old('color') }}" placeholder="Например: Бесцветный">
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="mineral" class="col-sm-3 control-label">Цвет черты</label>

                            <div class="col-sm-6">
                              <input type="text" name="color_in_line" id="mineral-color_in_line" class="form-control" value="{{ old('color_in_line') }}" placeholder="Например: Бесцветный">
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="mineral" class="col-sm-3 control-label">Прозрачность</label>

                            <div class="col-sm-6">
                              <input type="text" name="transparency" id="mineral-transparency" class="form-control" value="{{ old('transparency') }}" placeholder="Например: Прозрачный">
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="mineral" class="col-sm-3 control-label">Плотность(г/см<sup>3</sup>) (от до)</label>

                            <div class="col-sm-2">
                              <input type="text" name="density_before" id="mineral-density_before" class="form-control" value="{{ old('density_before') }}" placeholder="Например: 3,5">
                            </div>
                            <div class="col-sm-2">
                              <input type="text" name="density_after" id="mineral-density_after" class="form-control" value="{{ old('density_after') }}" placeholder="Например: 3,5">
                            </div>
                          </div>

                          <div class="form-group">
                             <label for="mineral" class="col-sm-3 control-label">Блеск</label>

                             <div class="col-sm-6">
                               <input type="text" name="shine" id="mineral-shine" class="form-control" value="{{ old('shine') }}" placeholder="Например: Алмазный, жирный">
                             </div>
                           </div>

                           <div class="form-group">
                             <label for="mineral" class="col-sm-3 control-label">Спайность</label>

                             <div class="col-sm-6">
                               <input type="text" name="cleavage" id="mineral-cleavage" class="form-control" value="{{ old('cleavage') }}" placeholder="Например: Совершенная">
                             </div>
                           </div>

                           <div class="form-group">
                             <label for="mineral" class="col-sm-3 control-label">Излом</label>

                             <div class="col-sm-6">
                               <input type="text" name="fracture" id="mineral-fracture" class="form-control" value="{{ old('fracture') }}" placeholder="Например: Раковистый">
                             </div>
                           </div>

                           <div class="form-group">
                             <label for="mineral" class="col-sm-3 control-label">Происхождение</label>

                             <div class="col-sm-6">
                               <input type="text" name="genesis" id="mineral-genesis" class="form-control" value="{{ old('genesis') }}" placeholder="Например: Магматическое">
                             </div>
                           </div>

                           <div class="form-group">
                             <label for="mineral" class="col-sm-3 control-label">Практическое применение</label>

                             <div class="col-sm-6">
                               <textarea name="practical_use" rows="4" id="mineral-practical_use" class="form-control" placeholder="Например: Используется в ювелирном деле, электронике">{{ old('practical_use') }}</textarea>
                             </div>
                           </div>

                           <div class="form-group">
                             <label for="mineral" class="col-sm-3 control-label">Месторождение</label>

                             <div class="col-sm-6">
                               <textarea name="deposit" rows="5" id="mineral-deposit" class="form-control" placeholder="Например: ЮАР (г. Кимберли), Индия, Бразилия, Россия (Уральские горы, Карелия, Кольский полуостров, Якутия), США (штат Арканзас, Мерфрисборо), Австралия (Аргайл, район Кимберли, штат Западная Австралия), Канада (Дайавик, Гахчо-Кью)">{{ old('deposit') }}</textarea>
                             </div>
                           </div>

                           <div class="form-group">
                             <label for="mineral" class="col-sm-3 control-label">Описание</label>

                             <div class="col-sm-6">
                               <textarea name="description" rows="7" id="mineral-description" class="form-control" placeholder="Например: АЛМАЗ - минерал, одна из природных кристаллических форм углерода наряду с минералами графит (graphite), лонсдейлит (lonsdaleite) и чаоит (chaoite). Алмаз - драгоценный камень. Самый твёрдый на сегодняшний день минерал, хотя по твёрдости его могут превосходить искусственные аналоги лонсдейлита и фуллерита.">{{ old('description') }}</textarea>
                             </div>
                           </div>
                            @if (request()->user()->is('admin|moderator'))
                            {{ old('seen') }}
                           <div class="form-group">
                             <div class="col-sm-6 col-sm-offset-3">
                               <div class="checkbox checkbox-success">
                                 <label><input name="seen" type="checkbox"@if(old('seen') == true) checked @endif  value="1">Информация достоверна и проверена модератором</label>
                               </div>
                             </div>
                           </div>
                           @endif


                          <!-- Кнопка добавления минерала -->
                          <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                              <button type="submit" class="btn btn-default">
                                <i class="fa fa-plus"></i> Добавить минерал
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
