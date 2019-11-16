<?php

namespace Drupal\mcl_votes\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Entity\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


/**
 * Class MclApiController.
 */
class MclApiController extends ControllerBase {

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
   * Constructs a new MclApiController object.
   */
  public function __construct(AccountProxyInterface $current_user, CurrentRouteMatch $current_route_match, EntityManagerInterface $entity_manager) {
    $this->currentUser = $current_user;
    $this->currentRouteMatch = $current_route_match;
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('current_route_match'),
      $container->get('entity.manager')
    );
  }

  /**
   * Generatemclvote.
   *
   * @return string
   *   Return Hello string.
   */
  public function GenerateMclVote() {

    
    $response = \Drupal::service('mcl_votes.generate_vote');
    
    return new JsonResponse([
      'data' => $response->GenerateMclVote(),
      'method' => 'GET',
      'status' => is_numeric($response) ? true:false,
    ]);

    
  }

  
  


  

}
