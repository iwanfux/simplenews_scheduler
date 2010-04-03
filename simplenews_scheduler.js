// $Id$

/**
 * @file
 * jQuery helper functions for the Simplenews Scheduler module interface on node edit page.
 */

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

/**
 * Set scheduler info's display attribute to hide and show based on the stop option value.
 */
Drupal.behaviors.simplenewsSchedulerStop = function (context) {
  var simplenewsSchedulerStop = function () {
    if($(".simplenews-command-stop :radio:checked").val() == '1') {
        $('#edit-simplenews-scheduler-stop-date-wrapper').css({display: "block"});
    } else {
    	$('#edit-simplenews-scheduler-stop-date-wrapper').css({display: "none"});
    }
    if($(".simplenews-command-stop :radio:checked").val() == '2') {
        $('#edit-simplenews-scheduler-stop-edition-wrapper').css({display: "block"});
    } else {
    	$('#edit-simplenews-scheduler-stop-edition-wrapper').css({display: "none"});
    }
  }
  
  // Update scheduler info's display at page load and when a stop option is selected.
  $(function() { simplenewsSchedulerStop(); });
  $(".simplenews-command-stop").click( function() { simplenewsSchedulerStop(); });
}