<?php

namespace Drupal\mcl_votes\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class GeneratePromotionalCodesController.
 */
class MclController extends ControllerBase {

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
   * Constructs a new GeneratePromotionalCodesController object.
   */
  public function __construct(AccountProxyInterface $current_user, EntityManagerInterface $entity_manager, EntityTypeManagerInterface $entity_type_manager) {
    $this->currentUser = $current_user;
    $this->entityManager = $entity_manager;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('entity.manager'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Get.
   *
   * @return string
   *   Return Hello string.
   */
  public function generateCodes() {
    $form = \Drupal::formBuilder()->getForm('Drupal\mcl_votes\Form\GenerateMclCodesForm');
    return $form;
  }

}
