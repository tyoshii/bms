function calcsum() {

  var tsum = 0,
      bsum = 0;

  $("table[role=score] tbody tr").each(function() {

    var t = parseInt( $(this).find("[data-type=score_top]").val()    );
    tsum += isNaN(t) ? 0 : t;

    var b = parseInt( $(this).find("[data-type=score_bottom]").val() );
    bsum += isNaN(b) ? 0 : b;
  });

  $("[data-type=score_top_sum]").text(tsum); 
  $("[data-type=score_bottom_sum]").text(bsum); 
}

// calcsum trigger
$(document).ready(function(){
  calcsum();

  $("table[role=score] tbody select").change(function() {
    calcsum();
  });
});


// score add/delete
$("button[type=score-add]").click(function() {

  var $last = $("table[role=score] tbody tr:last");
  var inning = $last.find("[data-type=inning]").text();

  var $clone = $last.clone(true);

  $clone.find("[data-type=inning]").text(parseInt(inning) + 1);
  $clone.find("[data-type=score_top]").val('');
  $clone.find("[data-type=score_bottom]").val('');

  $clone.insertAfter($last);
});

$("button[type=score-del]").click(function() {

  var $last = $("table[role=score] tbody tr:last");
  var inning = $last.find("[data-type=inning]").text();

  // 初回は消さない
  if ( inning == 1 ) {
    return false;
  }

  $last.remove();
});
