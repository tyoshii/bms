function update_number(self) {
  var $self = $(self);
  var member_id = $self.val();
  var number = $('data#number-' + member_id).text();

  $self.parent().parent().children('td.number').text(number);
}

function append_delete_button($tr) {

  var $change = $tr.find('td.change')

  // 既にあったらスキップ
  if ( $change.find('button.delete-order').length > 0 ) {
    return false;
  }
  // 追加先が９番だったらスキップ
  if ( $tr.find('td.order').text() == 9 ) {
    return false;
  }

  $change.append(
    $('<button></button>')
        .attr('onClick', 'delete_order(this, 1);')
        .addClass('btn btn-danger btn-xs delete-order')
        .text('削除')
  );
}

function delete_order(self, last) {
  var $tr = $(self).parent().parent();
  
  // 交代選手を全部削除
  $tr.nextAll('tr.player-tr').each(function() {
    var order = $(this).find('td.order');
    if ( order.text() == '' ) {
      $(this).remove();
    }
    else {
      return false;
    }
  });

  // ターゲットtrを削除
  $tr.fadeOut('normal', function() {
    $tr.remove();

    // 最終打順だった場合は、削除した後に最終打順になる箇所に削除ボタンを付ける
    if ( last ) {
      var $last = $('.player-tr:last');
    
      // 交代で追加した行だったらスキップ
      if ( $last.find('td.order').text() == '' ) {
        $last.prevAll('.player-tr').each(function() {
          if ( $(this).find('td.order').text() != '' ) {
            $last = $(this);
            return false;
          }
        });
      }
  
      append_delete_button($last);
    }
  });
}

function add_order(self, kind) {

  // select2 destroy
  // コピーするために機能削除
  // 生成後にもう一度有効にする
  $('.select2').each(function(){
    $(this).select2('destroy');
  });

  var $tr = $($('.player-tr')[0]);
  var $clone = $tr.clone(true);

  // init order
  if ( kind === 'last' ) {
    var $last = $('.player-tr:last');
    // 最後に追加するときは、打順をインクリメント
    var last_order = $('.player-tr:last td.order').text();
    $clone.find('td.order').text(++last_order);
    // 元々最後だった行から削除ボタンを消す
    $last.find('button.delete-order').remove();
  }
  else {
    $clone.find('td.order').text('');
  }

  // init selects
  $clone.find('select').each(function(){
    $(this).val(0);
  });

  // 削除ボタンを追加
  append_delete_button($clone);

  // disp to fadeIn
  $clone.hide();
  var $self = $(self).parent().parent();
  if ( kind === 'last' ) {
    $clone.insertBefore($self);
  }
  else {
    $clone.insertAfter($self);
  }
  $clone.fadeIn();
  
  // select2 available
  $('.select2').select2();
}

function post_pitcher() {

}

function post_player() {
  var $player = $("table#player td");
  // console.log($player);

  // get post data
  var data = [];
  var i = -1;
  $player.each(function(){

    var $this = $(this);    
    // console.log($this);

    if ( $this.hasClass('change') ) {

    }
    else if ( $this.hasClass('order') ) {
      // console.log('order - ' + $this.text());
      data.push( {order: $this.text()} );
      i++;
    }
    else if ( $this.hasClass('member_id') ) {
      // console.log('member_id - ' + $this.children('select').('val());
      data[i].member_id = $this.children('select').val();
      if ( $this.val() != '0' ) {
        data[i].name = $this.children('select').select2('data').text;
      }
    }
    else if ( $this.hasClass('number') ) {
      // console.log('number - ' + $this.text());
      data[i].number = $this.text();
    }
    else {
      // console.log('position - ' + $this.children('select').val());
      if ( typeof data[i].position === 'undefined' ) {
        data[i].position = [];
      }
      
      data[i].position.push($this.children('select').val());
    }
  });
  // console.log(data);

  // ajax
  $.ajax({
    url: '/game/edit',
    type: 'POST',
    data: {
      game_id: $('data#game_id').text(),
      team_id: $('data#team_id').text(),
      players: data
    },
    success: function(html) {
      alert("成績保存に成功");
    },
    error: function(html) {
      alert("成績保存でエラーが発生しました");
    }, 
  });
}

$(document).ready(function(){
  // post_data();
});
