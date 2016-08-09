@extends('...layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Заголовок</div>

                <div class="panel-body">
                @include('...common.messages')
                <?php
                $roles_rus_name = [
                    1=>'Администратор',
                    2=>'Модератор',
                    3=>'Редактор минералов',
                    4=>'Редактор новостей',
                    5=>'Простой пользователь'
                ];
                ?>

                    @foreach ($users as $user)
                        <div>{{ $user->name . ' ( ' . $roles_rus_name[$user->role_id] . ' )'}}</div>
                        <div class="btn-group" role="group">
                            <div class="btn-group" role="group">
                              <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Назначить <span class="caret"></span>
                              </button>
                              <ul class="dropdown-menu">
                                <li><a href="#" id="change_role" data-user-id="{{ $user->id }}" data-role-id="1">Администратором</a></li>
                                <li><a href="#" id="change_role" data-user-id="{{ $user->id }}" data-role-id="2">Модератором</a></li>
                                <li><a href="#" id="change_role" data-user-id="{{ $user->id }}" data-role-id="3">Редактором минералов</a></li>
                                <li><a href="#" id="change_role" data-user-id="{{ $user->id }}" data-role-id="4">Редактором новостей</a></li>
                                <li><a href="#" id="change_role" data-user-id="{{ $user->id }}" data-role-id="5">Простым пользователем</a></li>
                              </ul>
                            </div>
                            <button id="delete_user" class="btn btn-danger" data-user-id="{{ $user->id }}"><i class="fa fa-trash-o"></i> Удалить</button>
                        </div>
                        <hr>
                    @endforeach
                    {!! $users->render() !!}
                    <script>
                    $(document).ready(function(){
                        $(document).on("click", '#change_role',function(e) {
                            $.ajax({
                               type: 'POST',
                               url: '/admin/users/change-user-role',
                               data: 'user_id='+$(this).data('userId')+'&role_id='+$(this).data('roleId'),
                               // Функция удачного ответа с сервера
                               success: function(result) {
                                   if (result.success === 1) {
                                       messages.fromJson(result);
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
                        $(document).on("click", '#delete_user',function(e) {
                           $.ajax({
                               type: 'POST',
                               url: '/admin/users/delete-user',
                               data: 'user_id='+$(this).data('userId'),
                               // Функция удачного ответа с сервера
                               success: function(result) {
                                   if (result.success === 1) {
                                       messages.fromJson(result);
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
                    });
                     </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
