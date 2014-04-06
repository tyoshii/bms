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

function post_other(is_alert) {

  var data = { 
    mip: $("select#mip").val()
  };

  // ajax
  $.ajax({
    url: '/api/game/updateOther',
    type: 'POST',
    data: {
      game_id: $('data#game_id').text(),
      order:   $('data#order').text(),
      other:   data
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

function post_batter(is_alert) {

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
      var seiseki = {};

      // td
      var category1 = [
        'daseki', 'dasuu',
        'anda', 'niruida', 'sanruida', 'honruida',
        'sanshin', 'yontama', 'shikyuu',
        'gida', 'gihi'
      ];
      for ( var i in category1 ) {
        var key = category1[i];
        var val = $this.children('td.' + key).text();

        if ( val == '' ) val = 0;

        seiseki[key] = 0;
      }

      // input number
      var category2 = [
        'daten', 'tokuten', 'steal', 'error' 
      ];
      for ( var i in category2 ) {
        var key = category2[i];
        var val = $this.find('td.' + key + ' input').val();

        if ( val == '' ) val = 0;

        seiseki[key] = val;
      }

      data[id] = {
        seiseki: seiseki,
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
      order: $('data#order').text(),
      batter: data
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

function post_pitcher(is_alert) {
  var $pitcher = $("table#pitcher tbody tr");
  var data = [];

  $pitcher.each( function() {
    var $this = $(this);
    var $name = $this.children("td.name"),
        $number = $this.children("td.number"),
        $inning = $this.children("td.inning"),
        $result = $this.children("td.result"),
        $hianda = $this.children("td.hianda"),
        $sanshin = $this.children("td.sanshin"),
        $shishikyuu = $this.children("td.shishikyuu"),
        $earned_runs = $this.children("td.earned-runs"),
        $runs = $this.children("td.runs");
  
    var member_id = $name.children('data').text();

    data[member_id] = {
      name: $name.children('span').text(),
      number: $number.text(),
      inning_int: $inning.children('.inning_int').val(),
      inning_frac: $inning.children('.fraction').val(),
      result: $result.children('.result').val(),
      hianda: $hianda.children('.hianda').val(),
      sanshin: $sanshin.children('.sanshin').val(),
      shishikyuu: $shishikyuu.children('.shishikyuu').val(),
      earned_runs: $earned_runs.children('.earned-runs').val(),
      runs: $runs.children('.runs').val()
    };
  });
  // console.log(data);
  
  // ajax
  $.ajax({
    url: '/api/game/updatePitcher',
    type: 'POST',
    data: {
      game_id: $('data#game_id').text(),
      order: $('data#order').text(),
      pitcher: data
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

  if ( exit ) return false;

  // ajax
  $.ajax({
    url: '/api/game/updatePlayer',
    type: 'POST',
    data: {
      game_id: $('data#game_id').text(),
      order: $('data#order').text(),
      players: data
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
