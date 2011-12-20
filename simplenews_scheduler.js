
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
 * Set scheduler info's display attribute to hide and show dependent on the selected stop option.
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

/**
 * Set text of Save button dependent if scheduled sending is selected.
 */
Drupal.behaviors.simplenewsSchedulerCommandSend = function (context) {
  var simplenewsSchedulerSendButton = function () {
    switch ($(".simplenews-command-send :radio:checked").val()) {
      case '3':
        $('#simplenews-node-tab-send-form #edit-submit').attr({value: Drupal.t('Save and send as scheduled')});
        break;
      default:
        $('#simplenews-node-tab-send-form #edit-submit').attr({value: Drupal.t('Submit')});
        break;
      break;
    }
  }

  // Update send button at page load and when a send option is selected.
  $(function() { simplenewsSchedulerSendButton(); });
  $(".simplenews-command-send").click( function() { simplenewsSchedulerSendButton(); });
}
