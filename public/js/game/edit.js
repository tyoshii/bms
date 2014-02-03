function add_order(self, kind) {

  // select2 destroy
  // コピーするために機能削除
  // 生成後にもう一度有効にする
  $('.select2').each(function(){
    $(this).select2('destroy');
  });

  var $tr = $($('tr.stamen')[0]);
  var $clone = $tr.clone(true);

  // init order
  if ( kind === 'last' ) {
    var last_order = $('tr.stamen:last td.order').text();
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
  var $stamen = $("table#stamen td.stamen");
  // console.log($stamen);

  // get post data
  var data = [];
  var i = -1;
  $stamen.each(function(){

    var $this = $(this);    

    if ( $this.get(0).tagName === 'TD') {
      // console.log('order - ' + $this.text());
      data.push( {order: $this.text()} );
      i++;
    }
    else {
      if ( $this.hasClass('member_id') ) {
        // console.log('member_id - ' + $this.val());
        data[i].member_id = $this.val();
      }
      else {
        // console.log('position - ' + $this.val());
        if ( typeof data[i].position === undefined ) {
          data[i].position = [];
        }
        
        data[i].position.push($this.val());
      }
    }
  });
  // console.log(data);

  // get parameter
  var params = location.href.split("?")[1].split("&");
  var game_id, team_id;
  for ( i = 0; i < params.length; i++ ) {
    var kv = params[i].split("=");
    if ( kv[0] === "game_id" ) {
      game_id = kv[1];
    }
    else if ( kv[0] === "team_id" ) {
      team_id = kv[1];
    }
  }
  // console.log(game_id);
  // console.log(team_id);

  // ajax
  $.ajax({
    url: '/game/edit',
    type: 'POST',
    data: {
      game_id: game_id,
      team_id: team_id,
      stamen: data
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
