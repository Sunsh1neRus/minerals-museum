<div id="messages_block">
@if (count($errors) > 0)
  <!-- Список ошибок формы -->
  <div class="alert alert-danger">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Упс! Что-то пошло не так!</strong>

    <br>

    <ul>
      @foreach ($errors->all() as $error)
        <li>{!! clean($error,'links_only') !!}</li>
      @endforeach
    </ul>
  </div>
@endif
@if (Session::has('warning') AND is_array(Session::get('warning')) AND count(Session::get('warning')) > 0)
  <!-- Список предупреждений формы -->
  <div class="alert alert-warning">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Внимание, предупреждаем!</strong>

    <br>

    <ul>
      @foreach (Session::get('warning') as $v)
        <li>{!! clean($v,'links_only') !!}</li>
      @endforeach
    </ul>
  </div>
@endif
@if (Session::has('success') AND is_array(Session::get('success')) AND count(Session::get('success')) > 0)
  <!-- Список подтверждений формы -->
  <div class="alert alert-success">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Успешно!</strong>

    <br>

    <ul>
      @foreach (Session::get('success') as $v)
        <li>{!! clean($v,'links_only') !!}</li>
      @endforeach
    </ul>
  </div>
@endif
@if (Session::has('info') AND is_array(Session::get('info')) AND count(Session::get('info')) > 0)
  <!-- Список доп информаций формы -->
  <div class="alert alert-info">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Информация:</strong>

    <br>

    <ul>
      @foreach (Session::get('info') as $v)
        <li>{!! clean($v,'links_only') !!}</li>
      @endforeach
    </ul>
  </div>
@endif

</div>

<script>
function Messages(element_id) {
  this.element_id = element_id;
  this.messages_block = $('#' + this.element_id);

  this.removeAll = function() {
    this.messages_block.text('');
  };

  this.appendError = function(text) {
    if (!this.messages_block.find('.alert-danger').length) {
        // создать новый объект
        createBlock(this.messages_block,'danger','Упс! Что-то пошло не так!');
    }
    append(this.messages_block.find('.alert-danger'),text);
  };

  this.appendWarning = function(text) {
    if (!this.messages_block.find('.alert-warning').length) {
        // создать новый объект
        createBlock(this.messages_block,'warning','Внимание, предупреждаем!');
    }
    append(this.messages_block.find('.alert-warning'),text);
  };

  this.appendSuccess = function(text) {
    if (!this.messages_block.find('.alert-success').length) {
        // создать новый объект
        createBlock(this.messages_block,'success','Успешно!');
    }
    append(this.messages_block.find('.alert-success'),text);
  };

  this.appendInfo = function(text) {
    if (!this.messages_block.find('.alert-info').length) {
        // создать новый объект
        createBlock(this.messages_block,'info','Информация:');
    }
    append(this.messages_block.find('.alert-info'),text);
  };

  this.removeAllError = function() {
    this.messages_block.find('.alert-danger').remove();
  };

  this.removeAllWarning = function() {
    this.messages_block.find('.alert-warning').remove();
  };

  this.removeAllSuccess = function() {
    this.messages_block.find('.alert-success').remove();
  };

  this.removeAllInfo = function() {
    this.messages_block.find('.alert-info').remove();
  };

  this.fromJson = function(JSON) {
    var arr = JSON.msgs.error;
    for (var i = 0, len = arr.length; i < len; i++) {
      this.appendError(arr[i]);
    }
    arr = JSON.msgs.warning;
    for (i = 0, len = arr.length; i < len; i++) {
      this.appendWarning(arr[i]);
    }
    arr = JSON.msgs.success;
    for (i = 0, len = arr.length; i < len; i++) {
      this.appendSuccess(arr[i]);
    }
    arr = JSON.msgs.info;
    for (i = 0, len = arr.length; i < len; i++) {
      this.appendInfo(arr[i]);
    }
  };

  function createBlock(messages_block,type,title) {
      messages_block.append('<div class="alert alert-' + type + '"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>' + title + '</strong><br><ul></ul></div>');
    }

  function append(e,text) {
    e.find('ul').append('<li>' + text + '</li>');
  }
}

var messages = new Messages('messages_block');
</script>