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
