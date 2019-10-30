<?php

namespace Drupal\stripe_payments\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;

use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\stripe_payments\StripePaymentsCreateInterface;
/**
 * Class StripePaymentsChargeController.
 */
class StripePaymentsChargeController extends ControllerBase {

  /**
   * Drupal\Core\Session\AccountProxyInterface definition.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  protected $currentRoute;

  protected $entityManager;

  protected $stripePayments;


  /**
   * Constructs a new StripePaymentsChargeController object.
   */
  public function __construct(
    AccountProxyInterface $current_user, 
    CurrentRouteMatch $current_route, 
    EntityTypeManager $entity_manager, 
    StripePaymentsCreateInterface $stripe_payments ) {
    $this->currentUser = $current_user;
    $this->currentRoute = $current_route;
    $this->entityManager = $entity_manager;
    $this->stripePayments = $stripe_payments;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('current_route_match'),
      $container->get('entity_type.manager'),
      $container->get('stripe_payments.create')
    );
  }

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function create_charge() {

    
    $request = (object)\Drupal::request()->request->all();
    $nid = $this->currentRoute->getParameter('nid');
    
    if( $request->stripeToken && $nid){
      $node = $this->entityManager->getStorage('node')->load($this->currentRoute->getParameter('nid'));
      if( $node ){
        $charge = $this->stripePayments->create($node, $this->currentUser, $request->stripeToken);
      }else{
        $this->redirect('/node/'.$nid);
      }
    }else{
      $this->redirect('/');
    }

    if($charge->status == 'succeeded'){
      return [
        '#type' => 'markup',
        '#markup' => $this->t('Buy Complete!!')
      ];
    }else{
      return [
        '#type' => 'markup',
        '#markup' => $this->t('Buy Failed!!')
      ];
    }

    
  }

}
