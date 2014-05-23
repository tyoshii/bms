$("table.games tr").click(function(){
  var $game_id = $(this).attr('gameid');
  location.href = '/game/' + $game_id;
});
