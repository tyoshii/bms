// switch batter
$("span[role=switch-batter]").click(function(){
  var type  = $(this).attr("type"),
      $root = $(this).parents("div.batter-stats-container"),
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

$("div.batter-stats-post button").click(function(){
  var post_type = $(this).attr('post_type');

  var data = [];

  $("div.batter-stats-container").each(function(){
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
      seiseki: stats, // TODO: key name change to `stats'
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
