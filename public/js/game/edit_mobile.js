// object
var stats = {
  data: [],
  post: {
    complete: false, 
    ajax: function(path) {
      $.ajax({
        url: "/api/game/" + path,
        type: "POST",
        data: {
          game_id: $("data#game_id").text(),
          team_id: $("data#team_id").text(),
          stats: stats.data,
          complete: stats.post.complete
        },
        success: function(html) {
          alert('成績保存に成功しまし');
        },
        error: function(html) {
          alert('エラーが発生しました');
        }
      });
    },
  },
}

// common function
function clean_position_select($dom) {

  $dom.find("div.player-position").each(function() {
    if ( $(this).attr("index_attr") == 'last' ) {
      $(this).attr("index", 1);
    }
    else {
      $(this).remove();
    }
  });
}


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

  // cloneする前にselect2の機能を落とす
  // 仕様っぽい
  $(".select2").each(function() {
    $(this).select2('destroy');
  });

  // clone
  var $base = $("tr[played=starter]:last");
  $clone = $base.clone(true);

  // clear data
  $clone.removeAttr("played");
  $clone.find("td[role=order]").text('');

  clean_position_select($clone);

  $clone.find("select[role=player_id]").val(0);
  $clone.find("div[role=delete-player]").show();

  // append_to dom
  var $append_to = $(this).parents("tr");
  $clone.insertAfter($append_to);

  // select2 available
  $(".select2").select2({
    width: "100%",
  });
});

// delete switch player
$("div[role=delete-player] button").click(function() {
  $(this).parents("tr").remove();
});

// add player
$("button[role=player-add]").click(function() {

  // cloneする前にselect2の機能を落とす
  // 仕様っぽい
  $(".select2").each(function() {
    $(this).select2('destroy');
  });

  // clone
  $base = $("tr[played=starter]").last();
  $clone = $base.clone(true);

  // clear data
  var $order = $clone.find("td[role=order]");
  $order.text( parseInt($order.text()) + 1 );
  $clone.attr("order", $order.text() );

  clean_position_select($clone);

  $clone.find("select[role=player_id]").val(0);

  // append
  $clone.insertAfter($("table.player-table tr:last"));
  
  // select2 available
  $(".select2").select2({
    width: "100%",
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
  else {
    show_index = parseInt(hide_index) - 1;
  }

  $("div[index="+hide_index+"]").hide();
  $("div[index="+show_index+"]").show();
});

// add/delete batter detail
$("button.detail-add").click(function(){
  $table = $(this).parents("table.batter-detail");
  $base  = $table.find("tr.batter-detail-data:last");
  index  = $base.attr("index");
  PA = parseInt(index) + 1;

  // クローンして初期化
  var $clone = $base.clone(true);  

  $clone.find("td.PA").text("第"+PA+"打席");
  $clone.find("select").each(function(){
    $(this).val(0);
  });

  // index increment
  $clone.attr("index", PA);

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
$("div.stats-post[role=pitching] button").click(function(){
  stats.data= [];
  stats.post.complete = $(this).attr("post_type") === 'complete';

  $("table.pitching-stats").each(function() {
    var player_id = $(this).attr("player_id");

    stats.data[player_id] = {
      result  : $(this).find("select[role=result]").val(),
      IP      : $(this).find("select[role=IP]").val(),
      IP_frac : $(this).find("select[role=IP_frac]").val(),
      H       : $(this).find("select[role=H]").val(),
      SO      : $(this).find("select[role=SO]").val(),
      BB      : $(this).find("select[role=BB]").val(),
      HB      : $(this).find("select[role=HB]").val(),
      ER      : $(this).find("select[role=ER]").val(),
      R       : $(this).find("select[role=R]").val()
    };
  });
  // console.log(stats);

  stats.post.ajax('updatePitcher');
});

$("div.stats-post[role=hitting] button").click(function(){
  var post_type = $(this).attr("post_type");

  var data = [];

  $("div.stats-container").each(function(){
    var player_id = $(this).find("data.player-id").text();

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
    data[player_id] = {
      stats: stats,
      detail: detail,
    };
  });
  // console.log(data);


  // ajax
  $.ajax({
    url: "/api/game/updateBatter",
    type: "POST",
    data: {
      game_id: $("data#game_id").text(),
      team_id: $("data#team_id").text(),
      batter: data,
      complete: post_type === "complete",
    },
    success: function(html) {
      alert("野手成績の保存に成功しました。");
    },
    error: function(html) {
      alert("エラーが発生しました。");
    },
  });
});
