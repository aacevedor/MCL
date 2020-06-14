<?php

namespace Drupal\stripe_payments;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Stripe\Stripe as Stripe;
use Stripe\Charge as Charge;
use Stripe\Customer as Customer;

use Drupal\node\Entity\Node as Node;


/**
 * Class StripePaymentsCreate.
 */
class StripePaymentsCreate implements StripePaymentsCreateInterface {

  const SETTINGS = 'stripe.settings';

  /**
   * Constructs a new StripePaymentsCreate object.
   */
  public function __construct() {
      $this->config = \Drupal::config(static::SETTINGS);
      $this->submission = NULL;
      $this->stripe = Stripe::setApiKey($this->config->get('secret_stripe_key'));
  }

  public function createCharge(Node $node, $submission, $user, string $token ){
    $data = [
      'amount' => (int)$node->field_price->value * 100,
      'currency' => 'usd',
      'description' => $node->title->value,
      'source' => $token,
      'metadata' => [
        'name' => $user->getAccountName(),
        'mail' => $user->getEmail(),
        'product' => $node->field_tipo_de_producto->referencedEntities()[0]->name->value,
      ],
    ];

    if(!is_null($submission)){
      $this->submission = $submission;
      //$customer = $this->createCustomer($token, true);
      //if( $customer){
        //$data['customer'] = $customer->id;
      //}else{
      //  throw new \Exception('No se ha encontrado un customer válido.');
      //}
      $data['metadata']['inscription'] = $this->submission->id();
    }
    $charge = Charge::create($data);
    return $charge;
  }

  public function createCustomerResource($token, $customer){
    Customer::createSource($customer->id,['source' => $token]);
  }
  public function createCustomer($token, $resource){
    try {
      $customer = Customer::create([
        'address' => '',
        'description' => 'Registr de usuario MCL',
        'email' => $this->submission->field_mail->value,
        'metadata' => [
          'name' => $this->submission->field_nombres->value,
          'surname' => $this->submission->field_apellidos->value,
        ],
        'name' =>  $this->submission->field_nombres->value . ' ' .  $this->submission->field_apellidos->value,
        //'payment_method' => '',
        //'phone' => '',
        //'shipping' => '',
      ]);
    } catch (\Throwable $th){
      drupal_set_message($th->getMessage(), 'error');
    }
    if(isset($customer->id) && !empty($customer->id)){
      if($resource){
        try {
          $this->createCustomerResource($token, $customer);
        } catch (\Throwable $th){
          drupal_set_message($th->getMessage(),'error');
        }
      }
      return $customer;
    }else{
      return false;
    }
  }
}
