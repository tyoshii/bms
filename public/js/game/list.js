// status update
function changeGameStatus(s, game_id, team_id) {
  var status = typeof(s) === 'object' ? $(s).val() : s;

  if ( status === -1 ) {
    if ( ! window.confirm("無効試合としてよいですか？") ) {
      return false;
    }
  }
    
  $.ajax({
    type: 'POST',
    url: '/api/game/updateStatus',
    data: {
      game_id: game_id,
      team_id: team_id,
      status: status
    },
    success: function() {
      location.reload();
    },
    error: function() {
      alert("ステータスのアップデートに失敗しました");
    }
  });
}

$(document).ready(function() {
  $('input.form-datepicker').each(function() {
    $(this).datepicker();
  });
});

// search
$("#search").keyup(function(){

  if ( ! $(this).val()) {
    $("#game-list tbody tr").show();
  }
  else {
    $("#game-list tbody tr").hide();
    $("#game-list tbody tr:contains(" + this.value + ")").show();
  }
});

// filter
function filter(type) {
  if ( type === 'all' ) {
    $("#game-list tbody tr").show();
  }
  else if ( type === 'own' ) {
    $("#game-list tbody tr").hide();
    $("#game-list tbody tr.own").show();
  }
  else if ( type === 'win' ) {
    $("#game-list tbody tr").hide();
    $("#game-list tbody tr.win").show();
  }
  else if ( type === 'lose' ) {
    $("#game-list tbody tr").hide();
    $("#game-list tbody tr.lose").show();
  }
  else if ( type === 'play' ) {
    $("#game-list tbody tr").hide();
    $("#game-list tbody tr[play='true']").show();
  }
}
