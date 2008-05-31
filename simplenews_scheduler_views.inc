<?php
// $Id$

/**
 * @file simplenews_scheduler_views.inc
 * Views support for simplenews
 */

/**
 * Implementation of hook_views_tables
 */
function simplenews_scheduler_views_tables() {
  $tables['simplenews_scheduler'] = array(
    'name' => 'simplenews_scheduler',
    'filters' => array(
      'node.created' => array(
        'name' => t('Simplenews schedule: node created'),
        'list' => 'simplenews_scheduler_handler_newsletters',
        'list-type' => 'list',
        'handler' => 'simplenews_scheduler_filter_value',
        'operator' => 'simplenews_scheduler_handler_operator_ca',
        'value-type' => 'array',
        'help' => t('Select the newsletter to filter against, this will filter nodes that have been created AFTER the latest <i>edition</i> of this newsletter'),
      ),
    ),
  );
  return $tables;
}


function simplenews_scheduler_handler_newsletters() {
  
  $list = array();
  
  $result=db_query("SELECT * FROM {simplenews_scheduler} s LEFT JOIN {node} n ON s.snid=n.nid");
  while($row = db_fetch_array($result)) {
    $list[$row['nid']]="Newsletter: ".$row['title'];
  }
  
  
  return $list;
}


function simplenews_scheduler_filter_value($op, $filter, $filterinfo, &$query) {
  
    // get oldest last_run time of the selected newsletter
    $result=db_query("SELECT * from {simplenews_scheduler} WHERE snid IN (%s) ORDER BY last_run limit 0,1",implode(',',$filter['value']));
    $last_run = db_fetch_array($result);  
    $query->add_where("node.created %s %d", $filter['operator'], $last_run['last_run']);
}


function simplenews_scheduler_handler_operator_ca() {
  return array('>=' => t("After Most Recent Edition Of"),'<' => t("Before Most Recent Edition Of"));
}
