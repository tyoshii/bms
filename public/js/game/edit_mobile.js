// switch batter
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

// add/delete batter detail
$("button.detail-add").click(function(){
  $table = $(this).parents("table#batter-detail");
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
  $table = $(this).parents("table#batter-detail");
  $base  = $table.find("tr.batter-detail-data:last");
  index  = $base.attr("index");

  if ( parseInt(index) === 1 ) {
    return true;
  }

  $base.remove();
});
