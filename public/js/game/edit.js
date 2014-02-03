function add_order(self) {

  // select2 destroy
  // コピーするために機能削除
  // 生成後にもう一度有効にする
  $('.select2').each(function(){
    $(this).select2('destroy');
  });

  var $tr    = $(self).parent().parent();
  var $clone = $tr.clone(true);

  // 入っている値を全部初期化
  $clone.find('td.order').text('');
  $clone.find('select').each(function(){
    $(this).val(0);
  });

  // disp to fadeIn
  $clone.hide();
  $clone.insertAfter($tr);
  $clone.fadeIn();
  
  // select2 available
  $('.select2').select2();
}

function post_data() {
  var $stamen = $("table#stamen .stamen");
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
        if ( data[i].position === undefined ) {
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
