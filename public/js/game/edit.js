var stats = {
  type: function() {
    var path = location.pathname.split('/');
    return path[3];
  },
  data: function() {
    return {};
  }
};


$(document).ready(function(){
  batter_result_update();
});

var result_map = {
  //            打席,打数,安打,二塁,三塁,本塁,三振,四球,死球,犠打,犠飛
  '凡打':       [  1,   1,   0,   0,   0,   0,   0,   0,   0,   0,   0],
  '単打':       [  1,   1,   1,   0,   0,   0,   0,   0,   0,   0,   0],
  '二塁打':     [  1,   1,   0,   1,   0,   0,   0,   0,   0,   0,   0],
  '三塁打':     [  1,   1,   0,   0,   1,   0,   0,   0,   0,   0,   0],
  '本塁打':     [  1,   1,   0,   0,   0,   1,   0,   0,   0,   0,   0],
  '犠打':       [  1,   0,   0,   0,   0,   0,   0,   0,   0,   1,   0],
  '犠飛':       [  1,   0,   0,   0,   0,   0,   0,   0,   0,   0,   1],
  '見逃し三振': [  1,   1,   0,   0,   0,   0,   1,   0,   0,   0,   0],
  '空振り三振': [  1,   1,   0,   0,   0,   0,   1,   0,   0,   0,   0],
  '四球':       [  1,   0,   0,   0,   0,   0,   0,   1,   0,   0,   0],
  '死球':       [  1,   0,   0,   0,   0,   0,   0,   0,   1,   0,   0],
  '打撃妨害':   [  1,   0,   0,   0,   0,   0,   0,   0,   0,   0,   0],
  '守備妨害':   [  1,   0,   0,   0,   0,   0,   0,   0,   0,   0,   0]
}

function batter_result_update() {
  var order = 0;

  $('tr.detail').each(function(){
    $self = $(this); 

    // resultクラスが含まれていないtrは打席詳細入力内のヘッダー
    // よってヘッダーを迎えるたびに打順をインクリメントする。
    // さらに、seisekiの数をリセット
    if ( ! $self.hasClass('result') ) {
      order++;

      $('tr.result-'+order).find('.seiseki').each(function(){
        $(this).text(0);
      });
    }

    // 結果を拾ってくる
    result = $self.find('select.result :selected').text();
// console.log(result);
// console.log(result_map[result]);

    // 特定の結果の場合、種類と方向をグレーアウト
    if ( result == '死球' ||
         result == '四球' ||
         result == '見逃し三振' ||
         result == '空振り三振' ) {
      $self.find('select.direction').attr("disabled", "disabled");
      $self.find('select.direction').val(0);
      $self.find('select.kind').attr("disabled", "disabled");
      $self.find('select.kind').val(0);
    }
    else {
      $self.find('select.direction').removeAttr("disabled");
      $self.find('select.kind').removeAttr("disabled");
    }

    // seisekiの数字をインクリメント
    // mapにあるかどうかチェック
    if ( typeof result_map[result] !== 'undefined' )
    {
      var res = result_map[result];
      var i = 0;

      // その打順のseisekiにmapの数字を加算
      $('tr.result-'+order).find('.seiseki').each(function(){
        var temp = parseInt($(this).text()); 
        temp += res[i];
        $(this).text(temp);

        i++;
      });
    } 
  });
}

function _num(val) {
  return Number(val) || 0;
}

function post_score(is_alert) {
  var data = {
    game_id: $('data#game_id').text()
  };

  // each score
  for ( var i = 1; i <= 12; i++ ) {
    var t_key = 't' + i;
    var b_key = 'b' + i;

    data[t_key] = _num( $('[name=' + t_key + ']').val() );
    data[b_key] = _num( $('[name=' + b_key + ']').val() );
  }

  // sum
  data['tsum'] = _num( $("td.tsum").text() );
  data['bsum'] = _num( $("td.bsum").text() );

//console.log(data);
  
  // ajax
  $.ajax({
    url: '/api/game/updateScore',
    type: 'POST',
    data: data,
    success: function(html) {
      if ( is_alert === true ) {
        alert("成績保存に成功");
      }
    },
    error: function(res) {
      if ( res.status === 403 ) {
        alert(res.responseText);
      }
      else {
        alert("スコア保存でエラーが発生しました");
      }
    }, 
  });
}

function post_other(is_alert) {

  var data = { 
    mip2: $("select#mip2").val(),
    mip1: $("select#mip1").val(),
    place: $("input#place").val(),
    memo: $("textarea#memo").val(),
    status: $("select#status").val()
  };
//console.log(data);

  // ajax
  $.ajax({
    url: '/api/game/updateOther',
    type: 'POST',
    data: {
      game_id: $('data#game_id').text(),
      team_id: $('data#team_id').text(),
      other:   data
    },
    success: function(html) {
      if ( is_alert === true ) {
        alert("成績保存に成功");
      }
    },
    error: function(res) {
      if ( res.status === 403 ) {
        alert(res.responseText);
      }
      else {
        alert("成績保存でエラーが発生しました");
      }
    }, 
  });
}

function post_batter(is_alert, is_comp) {

  var data = [];
  var detail = [];

  // tr parse / data push
  $('tr.result').each(function() {
    var $this = $(this);
 
    var id = $this.children('td.member-id').text();

    if ( id === '0' ) {
      return true; //continue
    }

    if ( $this.hasClass("detail") ) {
      var daseki_number = $this.children('td.daseki-number').text();

      data[id].detail.push({
        direction: $this.find('select.direction').val(), 
        kind: $this.find('select.kind').val(), 
        result: $this.find('select.result').val() 
      });
    }
    else {
      var stats = {
        'TPA': 0, 'AB':  0,
        'H':   0, '2B':  0, '3B':  0, 'HR':  0,
        'SO':  0, 'BB':  0, 'HBP': 0,
        'SAC': 0, 'SF':  0,
      };

      // input number
      var category2 = [
        'RBI', 'R', 'SB', 'E' 
      ];
      for ( var i in category2 ) {
        var key = category2[i];
        var val = $this.find('td.' + key + ' input').val();

        if ( val == '' ) val = 0;

        stats[key] = val;
      }

      data[id] = {
        stats: stats,
        detail: [],
      };
    }
    
  });
  // console.log(data);

  // ajax
  $.ajax({
    url: '/api/game/updateBatter',
    type: 'POST',
    data: {
      game_id: $('data#game_id').text(),
      team_id: $('data#team_id').text(),
      batter: data,
      complete: is_comp
    },
    success: function(html) {
      if ( is_alert === true ) {
        alert("成績保存に成功");
      }
    },
    error: function(html) {
      alert("成績保存でエラーが発生しました");
    }, 
  });
  
}

function delete_daseki(self, daseki) {
  if ( daseki == 1 ) {
    return true;
  }

  var $self = $(self);
  var $tr   = $self.parent().parent();

  // 1つ前の打席に追加削除を表示
  var prev = $tr.prevAll('tr')[0];

  $(prev).find('button').each(function(){
    $(this).removeClass('disable');
  });

  // remove target
  $tr.remove();

  // 打席結果の削除なので、updateをかける
  batter_result_update();
}

function add_daseki(self, daseki) {
  var $self  = $(self);
  var $tr    = $self.parent().parent();
  var $clone = $tr.clone(true);

  var next_daseki = daseki + 1;

  // 元のdomから追加/削除を非表示
  $tr.find('button').each(function(){
    $(this).addClass('disable');
  });

  // clone dom
  // add button / delete button
  var $add = $clone.find('button.add');
  $add.removeClass('disable');
  $add.attr('onClick', 'add_daseki(this, '+next_daseki+');');

  var $del = $clone.find('button.delete');
  $del.removeClass('disable');
  $del.attr('onClick', 'delete_daseki(this, '+next_daseki+');');

  // 打席
  $clone.find('td.daseki-number').text(next_daseki);
  $clone.find('th.daseki-number-text').text('第' + next_daseki + '打席');

  // select default
  $clone.find('select').each(function(){
    $(this).val(0);
  });
  
  $clone.hide();
  $clone.insertAfter($tr);
  $clone.fadeIn();
}

function toggle_detail(id) {

  if ( id === 'all' ) {
    if ( $("tr.detail-1").is(":hidden") ) {
      $("tr.detail").show();
    }
    else {
      $("tr.detail").hide();
    }
  }
  else {
    $('tr.detail-'+id).toggle();
  }
}

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

  // init number
  $clone.find('td.number').text('');

  // init order
  if ( kind === 'last' ) {
    // 最後に追加するときは、打順をインクリメント
    var last_order = $('.player-tr:last td.order').text();
    $clone.find('td.order').text(++last_order);

    // 元々最後だった行から削除ボタンを消す
    var $last = $('.player-tr:last');
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

function post_pitcher(is_alert, is_comp) {
  var $pitcher = $("table#pitcher tbody tr");
  var data = [];

  $pitcher.each( function() {
    var $this = $(this);
    var $name    = $this.children("td.name"),
        $number  = $this.children("td.number"),
        $result  = $this.children("td.result"),
        $IP      = $this.children("td.IP"),
        $IP_frac = $this.children("td.IP_frac"),
        $H       = $this.children("td.H"),
        $SO      = $this.children("td.SO"),
        $BB      = $this.children("td.BB"),
        $HB      = $this.children("td.HB"),
        $ER      = $this.children("td.ER"),
        $R       = $this.children("td.R");
  
    var player_id = $name.children('data').text();

    data[player_id] = {
      name    : $name.children('span').text(),
      number  : $number.text(),
      result  : $result.children('.result').val(),
      IP      : $IP.children('.IP').val(),
      IP_frac : $IP_frac.children('.IP_frac').val(),
      H       : $H.children('.H').val(),
      SO      : $SO.children('.SO').val(),
      BB      : $BB.children('.BB').val(),
      HB      : $HB.children('.HB').val(),
      ER      : $ER.children('.ER').val(),
      R       : $R.children('.R').val()
    };
  });
  // console.log(data);
  
  // ajax
  $.ajax({
    url: '/api/game/updatePitcher',
    type: 'POST',
    data: {
      game_id: $('data#game_id').text(),
      team_id: $('data#team_id').text(),
      stats: data,
      complete: is_comp
    },
    success: function(html) {
      if ( is_alert === true ) {
        alert("成績保存に成功");
      }
    },
    error: function(html) {
      alert("成績保存でエラーが発生しました");
    }, 
  });
}

function post_player2(is_alert) {
  
  var data = [];

  $("table#player tbody tr.player-tr").each(function() {
    $this = $(this);

    var position = [];
    $this.find('td.position').each(function() {
      position.push($(this).children('select').val());
    });

    var temp = {
      player_id: $this.find('td.member_id').children('select').val(),
      member_id: $this.find('td.member_id').children('select').val(),
      order:     $this.find('td.order').text(),
      position:  position,
    };
    // console.log(temp);

    data.push(temp); 
  });
  // console.log(data);

  return data;
}

function post_player(is_alert) {
  var $player = $("table#player td");
  // console.log($player);

  // get post data
  var data = [];
  var already = {};
  var exit = false;
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
      var member_id = $this.children('select').val();
      data[i].member_id = member_id;

      // - TODO 将来的には player_id に置換したい。
      data[i].player_id = member_id;

      // 重複チェック
      if ( member_id != '0' && already[member_id] == 1 ) {
        if ( is_alert ) alert('同じ選手が登録されています');
        exit = true;  
        return false;
      }
      already[member_id] = 1;

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
  // console.log(already);
  
  // 将来的にこちらからとる
  // var data = post_player2();

  if ( exit ) return false;

  // ajax
  $.ajax({
    url: '/api/game/updatePlayer',
    type: 'POST',
    data: {
      game_id: $('data#game_id').text(),
      team_id: $('data#team_id').text(),
      stats: data
    },
    success: function(html) {
      if ( is_alert === true ) {
        alert("成績保存に成功");
      }
    },
    error: function(res) {
      if ( res.status === 403 ) {
        alert(res.responseText);
      }
      else {
        alert("成績保存でエラーが発生しました");
      }
    }, 
  });
}

function autosave(kind) {
  // 自動保存機能を停止
  return false;

  if ( kind === 'player' ) {
    post_player(false);
  } else if ( kind === 'pitcher' ) {
    post_pitcher(false);
  } else if ( kind === 'batter' ) {
    post_batter(false);
  }

  setTimeout('autosave("'+kind+'")', 30000);
}
