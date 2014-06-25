$("span[role=switch-batter]").click(function(){
  var type  = $(this).attr("type"),
      $root = $(this).parents("div.batter-result-wrapper"),
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
