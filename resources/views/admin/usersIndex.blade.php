@extends('...layouts.admin')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Заголовок</div>

                <div class="panel-body">
                @include('...common.messages')

                    @foreach ($users as $user)
                        <div>{{ $user->name . ' (' . $user->role->name . ')'}}</div>
                        <div class="btn-group">
                          <button id="delete_user" class="btn btn-danger" data-user-id="{{ $user->id }}">Удалить</button>
                          <button id="change_role" class="btn btn-primary" data-user-id="{{ $user->id }}" data-role-id="3">Назначить редактором</button>
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
