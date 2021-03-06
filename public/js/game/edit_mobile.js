// object: stats
var STATS = {
  data: [],
  post: {
    status: '',
    ajax: function(path, is_alert) {

      is_alert = typeof is_alert === 'undefined' ? true : is_alert;

      $.ajax({
        url: "/api/game/" + path,
        type: "POST",
        data: {
          game_id: $("data#game_id").text(),
          team_id: $("data#team_id").text(),
          stats: STATS.data,
          status: STATS.post.status,
        },
        success: function(html) {
          if (is_alert)
          {
            alert('成績が保存/登録されました。');
          }
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
    },
  },
};

// object: player
var player = {
  clone: function(args) {
    
    // cloneする前にselect2の機能を落とす
    // 仕様っぽい
    $(".select2").each(function() {
      $(this).select2('destroy');
    });
  
    // clone
    var $base = $("tr[played=starter]:last");
    $clone = $base.clone(true);
    
    // clear data
    // - order
    if ( args.order !== undefined ) {
      $clone.find("td[role=order]").text(args.order);
      $clone.attr("order", args.order);
    }
    else {
      var order = parseInt($base.attr("order")) + 1;
      $clone.find("td[role=order]").text(order);
      $clone.attr("order", order);
    }

    // - position
    $clone.find("div.player-position").each(function() {
      if ( $(this).attr("index_attr") == 'last' ) {
        $(this).attr("index", 1);
      }
      else {
        $(this).remove();
      }
    });

    // - player_id
    $clone.find("select[role=player_id]").val(0);
  
    // append
    $clone.insertAfter(args.append_to);

    // select2 available
    $(".select2").select2({
      width: "100%",
    });

    return $clone;
  }
};

// position add/delete
$("div.player-position select[role=position]").change(function(){
  var $base = $(this).parent("div");
  var index = $base.attr("index");
  var attr  = $base.attr("index_attr");

  // delete select box
  if ( $(this).val() == '' ) {
    if ( attr != 'last' ) {
      // console.log('delete');      

      // paretn cache
      var $td = $base.parent("td");

      // remove
      $base.remove();

      // re-index
      var index = 1;
      $td.find("div.player-position").each(function() {
        $(this).attr('index', index);
        index++;
      });
    }
  }
  // add
  else {
    if ( index != 6 && attr == 'last' ) {
      // console.log('add');      
      var $clone = $base.clone(true);

      $clone.attr('index', parseInt($base.attr('index')) + 1);
      $clone.attr('index_attr', 'last'); 
      $base.removeAttr('index_attr');

      $clone.find("select[role=position]").val('');

      $clone.insertAfter($base);
    }
  }
});

// switch player
$("div[role=switch-player] button").click(function(){

  var $clone = player.clone({
    order: '',
    append_to: $(this).parents("tr")
  });

  // played attr remove
  $clone.removeAttr("played");

  // delete-player button enable
  $clone.find("div[role=delete-player]").show();
});

// delete switch player
$("div[role=delete-player] button").click(function() {
  $(this).parents("tr").remove();
});

// add player
$("button[role=player-add]").click(function() {

  var $clone = player.clone({
    append_to: $("table.player-table tr:last")
  });
});

// delete player
$("button[role=player-del]").click(function() {
  var $tr = $("tr[played=starter]:last");

  if ( $tr.attr("order") > 9 ) {
    $tr.remove();
  }
});

// switch batter
$("span[role=switch-batter]").click(function(){
  var type  = $(this).attr("type"),
      $root = $(this).parents("div.stats-container"),
      hide_index = $root.attr("index"),
      show_index = 0;

  if ( type === 'next' ) {
    show_index = parseInt(hide_index) + 1;
  }
  else if ( type === 'prev' ) {
    show_index = parseInt(hide_index) - 1;
  }
  else if ( type === 'first' ) {
    show_index = 1;
  }
  else if ( type === 'last' ) {
    show_index = $("div.stats-container:last").attr('index');
  }

  $("div[index="+hide_index+"]").hide();
  $("div[index="+show_index+"]").show();
});

// add/delete batter detail
$("button.detail-add").click(function(){
  $table = $(this).parents("table.batter-detail");
  $base  = $table.find("tr.batter-detail-data:last");

  // クローンして初期化
  var $clone = $base.clone(true);  

  // 打席数
  var PA = parseInt($base.attr("index")) + 1;

  $clone.find("td.PA").text("第"+PA+"打席");
  $clone.attr("index", PA);

  // select 初期化
  $clone.find("select").each(function(){
    $(this).val(0);
  });

  // append
  $clone.insertAfter($base);
});

$("button.detail-del").click(function(){
  $table = $(this).parents("table.batter-detail");
  $base  = $table.find("tr.batter-detail-data:last");
  index  = $base.attr("index");

  if ( parseInt(index) === 1 ) {
    return true;
  }

  $base.remove();
});

// save/decide stats

/**
 * 試合概要登録
 */
$("div.stats-post[role=score] button").click(function() {

  // othersを先に
  // TODO: 1リクエストで処理するように
  STATS.data = {
    mvp:        $("select#mvp").val(),
    second_mvp: $("select#second_mvp").val(),
    stadium:    $("input#stadium").val(),
    memo:       $("textarea#memo").val()
  };

  STATS.post.ajax('updateOther', false);

  // score data
  STATS.data = {};

  // each inning score
  $("table[role=score] tbody tr").each(function() {
    var i = $(this).find("[data-type=inning]").text();
    STATS.data['t'+i] = $(this).find("[data-type=score_top]").val();
    STATS.data['b'+i] = $(this).find("[data-type=score_bottom]").val();
  });

  // sum score
  STATS.data['tsum'] = $("[data-type=score_top_sum]").text(); 
  STATS.data['bsum'] = $("[data-type=score_bottom_sum]").text(); 

  // console.log(STATS.data);
  
  // post
  STATS.post.ajax('updateScore');
});

$("div.stats-post[role=player] button").click(function() {
  STATS.data = [];
  STATS.post.status = $(this).attr("data-status");
 
  $("table.player-table tbody tr").each(function() {

    var position = [];
    $(this).find("select[role=position]").each(function() {
      if ( $(this).val() != 0 )
        position.push($(this).val());
    });

    STATS.data.push({
      order: $(this).find("td[role=order]").text(),
      player_id: $(this).find("select[role=player_id]").val(),
      position: position,
    });
  });
  // console.log(STATS.data);

  STATS.post.ajax('updatePlayer');
});

/**
 * 投手成績登録
 */
$("div.stats-post[role=pitching] button").click(function(){
  STATS.data= [];
  STATS.post.status   = $(this).attr("data-status");

  $("table.pitching-stats").each(function(order) {

    STATS.data[order] = {
      player_id : $(this).attr("player_id"),
      result    : $(this).find("select[role=result]").val(),
      IP        : $(this).find("select[role=IP]").val(),
      IP_frac   : $(this).find("select[role=IP_frac]").val(),
      H         : $(this).find("select[role=H]").val(),
      SO        : $(this).find("select[role=SO]").val(),
      BB        : $(this).find("select[role=BB]").val(),
      HB        : $(this).find("select[role=HB]").val(),
      ER        : $(this).find("select[role=ER]").val(),
      R         : $(this).find("select[role=R]").val()
    };
  });
  // console.log(STATS);

  STATS.post.ajax('updatePitcher');
});

/**
 * 野手成績登録
 */
$("div.stats-post[role=hitting] button").click(function(){
  STATS.data = {};
  STATS.post.status   = $(this).attr("data-status");
  
  $("div.stats-container").each(function(){
    var player_id = $(this).find("data.player-id").text();
    var data_key  = 'player_id:'+player_id;

    // stats
    var stats = {
      'TPA': 0, 'AB':  0,
      'H':   0, '2B':  0, '3B':  0, 'HR':  0,
      'SO':  0, 'BB':  0, 'HBP': 0,
      'SAC': 0, 'SF':  0,
    };
    $(this).find("table.batter-stats select").each(function() {
      var role = $(this).attr('role');
      var val  = $(this).val();

      stats[role] = parseInt(val);
    });

    // detail
    var detail = [];
    $(this).find("tr.batter-detail-data[player_id="+player_id+"]").each(function() {

      detail.push({
        direction: $(this).find("select[role=direction]").val(),
        kind:      $(this).find("select[role=kind]").val(),
        result:    $(this).find("select[role=result]").val(),
      });
    });

    // set
    STATS.data[data_key] = {
      player_id: player_id,
      stats:     stats,
      detail:    detail,
    };
  });
  // console.log(STATS.data);

  STATS.post.ajax('updateBatter');
});
