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

  // 背番号の更新
  $("select.select2.player_id").each(function(){
    update_number(this);
  });
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

/**
 * 野手成績登録
 */
$("div.stats-post[role=batter] button").click(function(){
  post_batter(this);
});
function post_batter(self) {
  var status = $(self).attr("data-status");
  var data = {};
  var detail = [];

  // tr parse / data push
  $('tr.result').each(function() {
    var $this = $(this);
 
    var player_id = $this.children('td.player-id').text();
    var data_key  = 'player-id-'+player_id;

    if ( player_id === '0' ) {
      return true; //continue
    }

    if ( $this.hasClass("detail") ) {
      var daseki_number = $this.children('td.daseki-number').text();

      data[data_key].detail.push({
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

      data[data_key] = {
        player_id: player_id,
        stats: stats,
        detail: []
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
      stats: data,
      status: status,
    },
    success: function(html) {
      alert("成績保存に成功");
    },
    error: function(res) {
      if ( res.status === 403 ) {
        alert(res.responseText);
      }
      else {
        alert("システムエラーが発生しました");
      }
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

  // removeAttr disabled
  $clone.find('select').removeAttr('disabled');

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
  var player_id = $self.val();
  var number = $('data#number-' + player_id).text();

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

  var $tr = $("tr[played=starter]:last");
  var $clone = $tr.clone(true);

  // init number
  $clone.find('td.number').text('');

  // 打順を追加
  if ( kind === 'last' ) {
    // 最後に追加するときは、打順をインクリメント
    var order = $tr.find("td.order").text();
    $clone.find('td.order').text( parseInt(order) + 1 );

    // 元々最後だった行から削除ボタンを消す
    $tr.find('button.delete-order').remove();
  }
  // 交代の時
  else {
    $clone.removeAttr("played");
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
  $('.select2').select2({
    width: '100%',
  });
}

/**
 * 投手成績登録
 */
$("div.stats-post[role=pitching] button").click(function(){
  post_pitcher(this);
});
function post_pitcher(self) {
  var status = $(self).attr("data-status");
  var $pitcher = $("table#pitcher tbody tr");
  var data = [];

  $pitcher.each( function(order) {
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

    data[order] = {
      player_id : $name.children('data').text(),
      name      : $name.children('span').text(),
      number    : $number.text(),
      result    : $result.children('.result').val(),
      IP        : $IP.children('.IP').val(),
      IP_frac   : $IP_frac.children('.IP_frac').val(),
      H         : $H.children('.H').val(),
      SO        : $SO.children('.SO').val(),
      BB        : $BB.children('.BB').val(),
      HB        : $HB.children('.HB').val(),
      ER        : $ER.children('.ER').val(),
      R         : $R.children('.R').val()
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
      status: status,
    },
    success: function(html) {
      alert("成績保存に成功");
    },
    error: function(res) {
      if ( res.status === 403 ) {
        alert(res.responseText);
      }
      else {
        alert("システムエラーが発生しました");
      }
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
      player_id: $this.find('td.player_id').children('select').val(),
      player_id: $this.find('td.player_id').children('select').val(),
      order:     $this.find('td.order').text(),
      position:  position,
    };
    // console.log(temp);

    data.push(temp); 
  });
  // console.log(data);

  return data;
}

/**
 * 選手登録
 */
$("div.stats-post[role=player] button").click(function(){
  post_player(this);
});
function post_player(self) {
  var status  = $(self).attr("data-status");
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
    else if ( $this.hasClass('player_id') ) {
      // console.log('player_id - ' + $this.children('select').('val());
      var player_id = $this.children('select').val();
      data[i].player_id = player_id;

      // - TODO 将来的には player_id に置換したい。
      data[i].player_id = player_id;

      // 重複チェック
      if ( player_id != '0' && already[player_id] == 1 ) {
        alert('同じ選手が登録されています');
        exit = true;  
        return false;
      }
      already[player_id] = 1;

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
      stats: data,
      status: status,
    },
    success: function(html) {
      alert("成績保存に成功");
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

// 投手成績：登板順
$("div[role=pitching-order] button").click(function(){

  var target_class = '.pitching-stats-row';
  var type = $(this).attr("data-type");

  var $tr = $(this).parents(target_class);
  var $target = type === "up" ? $tr.prev(target_class) : $tr.next(target_class);

  if ($target[0])
  {
    if (type === "up") {
      $tr.insertBefore($target[0]);
    }
    else {
      $tr.insertAfter($target[0]);
    }
  }

  // 登板順の文言変更
  // TODO: いまスマホだけ
  var order = 0;
  $("table.pitching-stats td.order").each(function(){
    $(this).text( order++ === 0 ? '先発' : order + '番手' );
  });
});
