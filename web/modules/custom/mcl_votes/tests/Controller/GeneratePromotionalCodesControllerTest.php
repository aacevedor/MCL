<?php

namespace Drupal\mcl_votes\Tests;

use Drupal\simpletest\WebTestBase;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Provides automated tests for the mcl_votes module.
 */
class GeneratePromotionalCodesControllerTest extends WebTestBase {

  /**
   * Drupal\Core\Session\AccountProxyInterface definition.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Drupal\Core\Entity\EntityManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "mcl_votes GeneratePromotionalCodesController's controller functionality",
      'description' => 'Test Unit for module mcl_votes and controller GeneratePromotionalCodesController.',
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
  public function testGeneratePromotionalCodesController() {
    // Check that the basic functions of module mcl_votes.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
