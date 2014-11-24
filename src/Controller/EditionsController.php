<?php /**
 * @file
 * Contains \Drupal\simplenews_scheduler\Controller\EditionsController.
 */

namespace Drupal\simplenews_scheduler\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

/**
 * Default controller for the simplenews_scheduler module.
 */
class EditionsController extends ControllerBase {

  /**
   * Check whether to display the Scheduled Newsletter tab.
   */
  public function checkAccess(NodeInterface $node) {
    // Check if this is a simplenews node type and permission.
    if ($node->hasField('simplenews_issue') && $node->simplenews_issue->target_id != NULL && \Drupal::currentUser()->hasPermission('overview scheduled newsletters')) {
      // Check if this is either a scheduler newsletter or an edition.
      return \Drupal\Core\Access\AccessResult::allowedIf(!empty($node->simplenews_scheduler) || !empty($node->is_edition));
    }
  }

  /**
   * Helper function to get the identifier of newsletter.
   *
   * @param $node
   *  The node object for the newsletter.
   *
   * @return
   *  If the node is a newsletter edition, the node id of its parent template
   *  newsletter; if the node is a template newsletter, its own node id; and
   *  FALSE if the node is not part of a scheduled newsletter set.
   */
  function getPid(NodeInterface $node) {
    // First assume this is a newsletter edition,
    if (isset($node->simplenews_scheduler_edition)) {
      return $node->simplenews_scheduler_edition->pid;
    }
    // or this itself is the parent newsletter.
    elseif (isset($node->simplenews_scheduler)) {
      return $node->id();
    }

    return FALSE;
  }

  public function nodeEditionsPage(NodeInterface $node) {
    $nid = $this->getPid($node);
    $output = '';
    $rows = array();

    if ($nid == $node->id()) { // This is the template newsletter.
      $output .= '<p>' . t('This is a newsletter template node of which all corresponding editions nodes are based on.') . '</p>';
    }
    else { // This is a newsletter edition.
      // $output .= '<p>' . t('This node is part of a scheduled newsletter configuration. View the original newsletter <a href="@parent">here</a>.', array('@parent' => url('node/' . $nid))) . '</p>';

    }

    // Load the corresponding editions from the database to further process.
    $result = db_select('simplenews_scheduler_editions', 's')
      ->extend('Drupal\Core\Database\Query\PagerSelectExtender')
      ->limit(20)
      ->fields('s')
      ->condition('s.pid', $nid)
      ->execute()
      ->fetchAll();

    foreach ($result as $row) {
      $node = \Drupal::entityManager()->getStorage('node')->load($row->eid);
      $rows[] = array(\Drupal::l($node->getTitle(), $node->url()), format_date($row->date_issued, 'custom', 'Y-m-d H:i'));
    }

    // Display a table with all editions.
    // @todo to renderarray
    $tablecontent = array(
      'header' => array(t('Edition Node'), t('Date sent')),
      'rows' => $rows,
      'attributes' => array('class' => array('schedule-history')),
      'empty' => t('No scheduled newsletter editions have been sent.'),
    );
    $output .= _theme('table', $tablecontent);
    $output .= _theme('pager', array('tags' => 20));

    return $output;
  }
}
