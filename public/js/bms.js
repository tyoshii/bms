// init jquery plugins
$(document).ready(function() {
  // select2
  $('.select2').select2({
    width: "100%",
  });

  //datepicker
  $('input.form-datepicker').each(function() {
    $self = $(this);
    $self.datepicker().on('changeDate', function(ev) {
      $self.datepicker('hide');
    });
  });
});
