function get_dom_delete_order() {
  return $('<button></button>')
          .attr('onClick', 'delete_order(this, 1);')
          .addClass('btn btn-danger btn-xs')
          .text('削除'); 
}

function delete_order(self, last) {
  $(self).parent().parent().remove();

  if ( last ) {
    $td = $('.player-tr:last td.change'); 
    $td.append(get_dom_delete_order());
  }  
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
    var last_order = $('.player-tr:last td.order').text();
    $clone.find('td.order').text(++last_order);
  }
  else {
    $clone.find('td.order').text('');
  }

  // init selects
  $clone.find('select').each(function(){
    $(this).val(0);
  });
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

function post_data() {
  var $player = $("table#player td");
  // console.log($player);

  // get post data
  var data = [];
  var i = -1;
  $player.each(function(){

    var $this = $(this);    
    // console.log($this);

    if ( $this.hasClass('order') ) {
      // console.log('order - ' + $this.text());
      data.push( {order: $this.text()} );
      i++;
    }
    else if ( $this.hasClass('member_id') ) {
      // console.log('member_id - ' + $this.val());
      data[i].member_id = $this.val();
      if ( $this.val() != '0' ) {
        data[i].name = $this.select2('data').text;
      }
    else if ( $this.hasClass('number') ) {
    }
    else {
      // console.log('position - ' + $this.val());
      if ( typeof data[i].position === 'undefined' ) {
        data[i].position = [];
      }
      
      data[i].position.push($this.val());
    }
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
