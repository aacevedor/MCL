<?php

namespace Drupal\mcl_votes\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Entity\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Entity;
use \Drupal\node\Entity\Node;

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
   * Drupal\Core\Entity\EntityManagerInterface definition.
   *
   * @var \Drupal\Core\Entity
   */
  protected $node;
  
  
  
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

    $entity_type = $this->currentRouteMatch->getParameter('entity_type');
    $entity_id = $this->currentRouteMatch->getParameter('nid');
    $this->node = $this->entityManager->getStorage('node')->load($entity_id);

    
    try {

      if( !self::allowVotes() ){
        throw new \Exception($this->t('You do not have enough votes to complete the transaction'));
      }else{
        $response = self::createEntity();
      }
    
    } catch (\Throwable $th) {
      $response = $th->getMessage();
    }


    return new JsonResponse([
      'data' => $response,
      'method' => 'GET',
      'status' => is_numeric($response) ? true:false,
    ]);
    
  }

  
  private function createEntity(  ){

    $node = Node::create([
      'type'                      => 'transaccion',
      'title'                     => $this->t('vote'). ' - ' .$this->node->title->value. ' - ' .$this->currentUser->getAccountName(),
      'field_cantidad'            => 1,
      'field_tipo_de_transaccion' => 19,
      'field_usuario'             => $this->currentUser->id(),
      'field_candidata'           => $this->node->id(),
    ]);

    if($node){
      try {

        $node->save();

      } catch (\Throwable $th) {

        return $th->getMessage();

      }

      return $node->id();

    }else{

      return false;

    }
  }


  private function allowVotes(){

    $entities = [];
    $transaccions = \Drupal::entityTypeManager()->getStorage('node')
      ->loadByProperties(
        [
          'type' => 'transaccion', 
          'field_usuario' => $this->currentUser->id()
        ]
      );
    
    foreach($transaccions as $key => $value){
      $tax_name = $value->get('field_tipo_de_transaccion')->referencedEntities()[0]->name->value;
      $entities[$tax_name][] = $value;
    }

    $total= [];

    if( count($entities) ){
      $array_keys = array_keys($entities);
      foreach($array_keys as $key => $value){
        foreach($entities[$value] as $key => $entity){
          $total[$value] += $entity->field_cantidad->value;
        }
      }
      $total['diference'] = self::array_subtract( $total );
    }

    return $total['diference'] > 0 ? true:false ;

  }


  public function array_subtract(array $input) {
    $result = reset($input);                            // First element of the array
    foreach (array_slice($input, 1) as $value) {        // Use array_slice to avoid subtracting the first element twice
        $result -= $value;                          // Subtract the value
    }
    return $result;                                // Return the result
  }


}
