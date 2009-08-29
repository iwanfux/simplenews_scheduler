/**
 * Set scheduler info's display attribute to hide and show based on the option value.
 */
Drupal.behaviors.simplenewsScheduler = function (context) {
  var simplenewsScheduler = function () {
    if($(".simplenews-command-send :radio:checked").val() == '3') {
        $('.schedule_info').css({display: "block"});
    } else {
    	$('.schedule_info').css({display: "none"});
    }
  }
  
   // Update scheduler info's display at page load and when a send option is selected.
  $(function() { simplenewsScheduler(); });
  $(".simplenews-command-send").click( function() { simplenewsScheduler(); });
}