<?php

/**
 * @file
 * Simplenews scheduler node creation test functions.
 *
 * @ingroup simplenews_scheduler
 */

namespace Drupal\simplenews_scheduler\Tests;

/**
 * Testing generation of newsletters.
 *
 * @group simplenews_scheduler
 */
class SimplenewsSchedulerNodeCreationTest extends SimplenewsSchedulerWebTestBase {

  function setUp() {
    parent::setUp();

    $admin_user = $this->drupalCreateUser(array(
      'access content',
      'administer nodes',
      'create simplenews content',
      'edit own simplenews content',
      'send newsletter',
      'send scheduled newsletters',
      'overview scheduled newsletters',
    ));
    $this->drupalLogin($admin_user);

    // Subscribe a user to simplenews.
    $this->mail = 'test@example.org';
    simplenews_subscribe($this->mail, 'default', FALSE, 'test');
  }

  /**
   * Basic simplenews newsletter generation test
   * create a simplenews node,
   */
  function testNewsletterGeneration() {
    $title ="newsletter " . $this->randomMachineName(8);

    $edit = array(
      'title[0][value]' => $title,
      'body[0][value]' => $this->randomMachineName(16),
      'simplenews_issue' => 'default',
    );
    $this->drupalPostForm('node/add/simplenews', $edit, t('Save and publish'));
    $this->assertText($title);

    preg_match('|node/(\d+)$|', $this->getUrl(), $matches);
    $node = \Drupal::entityManager()->getStorage('node')->load($matches[1]);

    // Make sure that the editions tab is not visible as long as it's not a
    // scheduled newsletter.
    $this->drupalGet("node/{$node->id()}/editions");
    $this->assertResponse(403, t('Editions tab not accessible'));

    // Now create the simplenews schedule configuration.
    $this->drupalGet("node/{$node->id()}/simplenews");
    $this->assertText(t("Send newsletter according to schedule"));

    $edit = array();
    $edit["send"] = '4';
    $edit["interval"] = "hour";

    // Specify a start time 30 minutes in the past to be able to have a known
    // edition creation time that can be checked.
    $date = new \DateTime();
    $date->sub(new \DateInterval('PT30M'));

    $edit["start_date[date]"] = $date->format('Y-m-d');
    $edit["start_date[time]"] = $date->format('H:i:s');
    $edit["title"] = "Custom title [site:name]";

    $this->drupalPostForm("node/{$node->id()}/simplenews", $edit, t('Submit'));


    // Make sure it knows no editions created yet.
    $this->drupalGet("node/{$node->id()}/editions");
    $this->assertText(t("No scheduled newsletter editions have been sent."));

    // Execute cron.
    \Drupal::service('cron')->run();

    // See if it was created.
    $this->drupalGet("node/{$node->id()}/editions");
    $this->assertText("Custom title");
    $this->assertNoText(t("No scheduled newsletter editions have been sent."));

    $this->assertText($title); // original real node title

    // Go to node and verify creation time and token for custom title
    // @todo: make this real token integration
    $site_config = \Drupal::config('system.site');
    $this->clickLink("Custom title ". $site_config->get('name'));

    //$this->assertText(t('This is a newsletter edition. View the master template of this newsletter here'));
    $this->assertText(t('on @date', array('@date' => format_date($date->getTimestamp()))));

    // Check sent mails.
    $mails = $this->drupalGetMails();
    $this->assertEqual(1, count($mails), t('Newsletter mail has been sent.'));

    $this->clickLink(t('Newsletter'));
    $this->assertText(t('This node is part of a scheduled newsletter configuration.'));
    $this->clickLink(t('here'));
    $url = $node->url('canonical', ['absolute' => TRUE]);

    $this->assertEqual($url, $this->getUrl());

    // Test the tab on a sent newsletter, schedule details should not be shown.
    $title ="newsletter " . $this->randomMachineName(8);

    $edit = array(
      'title[0][value]' => $title,
      'body[0][value]' => $this->randomMachineName(16),
      'simplenews_issue' => 'default',
    );
    $this->drupalPostForm('node/add/simplenews', $edit, t('Save and publish'));
    $this->assertText($title);

    preg_match('|node/(\d+)$|', $this->getUrl(), $matches);
    $node = \Drupal::entityManager()->getStorage('node')->load($matches[1]);

    $edit = array();
    $edit["send"] = SIMPLENEWS_COMMAND_SEND_NOW;
    $this->drupalPostForm("node/{$node->id()}/simplenews", $edit, t('Submit'));
    $this->assertNoText(t('Schedule details'));

    // Check sent mails.
    $mails = $this->drupalGetMails();
    $this->assertEqual(1, count($mails), t('Newsletter mail has been sent.'));
  }

}