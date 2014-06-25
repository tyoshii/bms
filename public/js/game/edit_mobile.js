$("span[role=switch-batter]").click(function(){
  $root = $(this).parents("div.batter-result-wrapper");
  index = $root.attr("index");
  next  = parseInt(index) + 1;

  $("div[index="+index+"]").hide();
  $("div[index="+next+"]").show();
});
