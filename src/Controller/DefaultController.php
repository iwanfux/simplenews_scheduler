<?php /**
 * @file
 * Contains \Drupal\simplenews_scheduler\Controller\DefaultController.
 */

namespace Drupal\simplenews_scheduler\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Default controller for the simplenews_scheduler module.
 */
class DefaultController extends ControllerBase {


  public function _simplenews_scheduler_tab_permission($node, Drupal\Core\Session\AccountInterface $account) {
    // Check if this is a simplenews node type and permission.
    if (simplenews_check_node_types($node->type) && \Drupal::currentUser()->hasPermission('overview scheduled newsletters')) {
    // Check if this is either a scheduler newsletter or an edition.
    return \Drupal\Core\Access\AccessResult::allowedIf(!empty($node->simplenews_scheduler) || !empty($node->is_edition));
  }
  }

  public function simplenews_scheduler_node_page($node) {
    // @FIXME: drupal_set_title() has been removed in Drupal 8. Setting the title is now done in different ways depending on the context. For more information, see https://www.drupal.org/node/2067859
// drupal_set_title(t('Scheduled newsletter editions'));

    $nid = _simplenews_scheduler_get_pid($node);
    $output = '';
    $rows = array();

    if ($nid == $node->nid) { // This is the template newsletter.
    $output .= '<p>' . t('This is a newsletter template node of which all corresponding editions nodes are based on.') . '</p>';
  }
  else { // This is a newsletter edition.
    // $output .= '<p>' . t('This node is part of a scheduled newsletter configuration. View the original newsletter <a href="@parent">here</a>.', array('@parent' => url('node/' . $nid))) . '</p>';

  }

    // Load the corresponding editions from the database to further process.
    $result = db_select('simplenews_scheduler_editions', 's')
    ->extend('PagerDefault')
    ->limit(20)
    ->fields('s')
    ->condition('s.pid', $nid)
    ->execute()
    ->fetchAll();

    foreach ($result as $row) {
    $node = \Drupal::entityManager()->getStorage('node')->load($row->eid);
    // @FIXME Provide a valid URL, generated from a route name, as the second argument to l(). See https://www.drupal.org/node/2346779 for more information.
// $rows[] = array(l($node->title, 'node/' . $row->eid), format_date($row->date_issued, 'custom', 'Y-m-d H:i'));

  }

    // Display a table with all editions.
    $tablecontent = array(
    'header' => array(t('Edition Node'), t('Date sent')),
    'rows' => $rows,
    'attributes' => array('class' => array('schedule-history')),
    'empty' => '<p>' . t('No scheduled newsletter editions have been sent.') . '</p>',
  );
    $output .= _theme('table', $tablecontent);
    $output .= _theme('pager', array('tags' => 20));

    return $output;
  }
}
