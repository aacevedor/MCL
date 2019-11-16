<?php

namespace Drupal\mcl_votes\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Entity\EntityManagerInterface;

/**
 * Provides automated tests for the mcl_votes module.
 */
class MclApiControllerTest extends WebTestBase {

  /**
   * Drupal\Core\Session\AccountProxyInterface definition.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Drupal\Core\Routing\CurrentRouteMatch definition.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

  /**
   * Drupal\Core\Entity\EntityManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "mcl_votes MclApiController's controller functionality",
      'description' => 'Test Unit for module mcl_votes and controller MclApiController.',
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
   * Tests mcl_votes functionality.
   */
  public function testMclApiController() {
    // Check that the basic functions of module mcl_votes.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
