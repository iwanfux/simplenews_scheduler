<?php

/**
 * @file
 * Hooks provided by the Simplenews scheduler module.
 */

/**
 * Alter the node object that was cloned from the template node before it gets saved.
 *
 * The node is passed as node object and therefore passed by reference. This hook
 * is for example usefull if you have fields in the template node that contain
 * information about data that should get rendered dynamically into the edition
 * node depenedent on the current schedule date.
 */
function hook_simplenews_scheduler_cloned_node_alter($node) {
  $node->title = 'Your newsletter from ' . REQUEST_TIME;
}
