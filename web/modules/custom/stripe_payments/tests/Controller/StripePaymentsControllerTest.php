<?php

namespace Drupal\stripe_payments\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Provides automated tests for the stripe_payments module.
 */
class StripePaymentsControllerTest extends WebTestBase {

  /**
   * Drupal\Core\Session\AccountProxyInterface definition.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "stripe_payments StripePaymentsController's controller functionality",
      'description' => 'Test Unit for module stripe_payments and controller StripePaymentsController.',
      'group' => 'Other',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests stripe_payments functionality.
   */
  public function testStripePaymentsController() {
    // Check that the basic functions of module stripe_payments.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
