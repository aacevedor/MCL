<?php

namespace Drupal\stripe_payments\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;

use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\stripe_payments\StripePaymentsCreateInterface;
use \Drupal\node\Entity\Node;
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
    $this->node = NULL;
    $this->submission = NULL;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('current_route_match'),
      $container->get('entity_type.manager'),
      $container->get('stripe_payments')
    );
  }

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function pay() {
    $request = (object)\Drupal::request()->request->all();
    $this->node       = ($nid = $this->currentRoute->getParameter('nid')) ? $this->entityManager->getStorage('node')->load($nid):NULL;
    $this->submission = ($sid = $this->currentRoute->getParameter('sid')) ? $this->entityManager->getStorage('eform_submission')->load($sid):NULL;
    if( isset($request->stripeToken) && !is_null($this->node)){
      try {
        $charge = $this->stripePayments->createCharge($this->node, $this->submission, $this->currentUser, $request->stripeToken);
      } catch (\Throwable $th) {
        drupal_set_message($th->getMessage(), 'error');
      }
    }
    if($charge->status == 'succeeded'){
      try {
        self::createTrasaction();
        if(isset($charge->metadata->inscription)) {
          self::createUser($charge->customer);
          self::createCandidata();
        }
      } catch (\Throwable $th) {
        drupal_set_message($th->getMessage(), 'error');
      }
      return [
          '#type' => 'markup',
          '#markup' => $this->t('Buy Complete!!')
        ];
      }
    else{
      throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    }
  }


  private function createTrasaction(){
    $node = Node::create(
      [
        'type'                      => 'transaccion',
        'title'                     => $this->t('purchase'). ' - ' .$this->node->title->value. ' - ' .$this->currentUser->getAccountName(),
        'field_cantidad'            => $this->node->field_cantidad->value,
        'field_producto'            => $this->node->id(),
        'field_tipo_de_transaccion' => $this->node->field_tipo_de_producto->target_id,
        'field_price'               => $this->node->field_price->value,
        'field_usuario'             => $this->currentUser->id(),
      ]
    );
    if($node){
      $node->save();
      return true;
    }
    return false;
  }

  private function createCandidata(){
    $node = Node::create(
      [
        'type' => 'candidata',
        'body' => 'asd',
        'field_busto' => NULL,
        'field_caderas' => NULL,
        'field_cintura' => NULL,
        'field_color_de_ojos' => NULL,
        'field_estatura' => NULL,
        'field_fecha' => NULL,
        'field_imagen' => NULL,
        'field_pais' => NULL,
        'field_temporada' => NULL,
        'field_usuario' => $this->user->id(),
        'title' => $this->user->field_nombres->value. ' ' .$this->user->field_apellidos->value,
      ]
    );
    if($node){
      $node->save();
    }
    return true;
  }

  private function createUser($customer_id){
    $lang = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $user = \Drupal\user\Entity\User::create();
    $user->setUsername($this->submission->field_mail->value); // You could also just set this to "Bob" or something...
    $user->setPassword('123456789');
    $user->setEmail($this->submission->field_mail->value);
    $user->enforceIsNew();  // Set this to FALSE if you want to edit (resave) an existing user object

    $user->set("init", $this->submission->field_mail->value);
    $user->set("langcode", $lang);
    $user->set("preferred_langcode", $lang);
    $user->set("preferred_admin_langcode", $lang);
    $user->set("field_nombres", $this->submission->field_nombres->value);
    $user->set("field_apellidos", $this->submission->field_apellidos->value);
    $user->set("field_informacion_stripe", $customer_id);
    $user->save();
    $this->user = $user;
  }

}


