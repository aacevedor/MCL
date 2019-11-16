<?php

namespace Drupal\stripe_payments;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Stripe\Stripe as Stripe;
use Stripe\Charge as Charge;
use Drupal\node\Entity\Node as Node;


/**
 * Class StripePaymentsCreate.
 */
class StripePaymentsCreate implements StripePaymentsCreateInterface {


  /**
   * Config Settings.
   * 
   * @var string
   */

  const SETTINGS = 'stripe.settings';

  /**
   * Constructs a new StripePaymentsCreate object.
   */
  public function __construct() {


      $this->config = \Drupal::config(static::SETTINGS); 
    
      Stripe::setApiKey($this->config->get('secret_stripe_key'));

    

  }

  public function create(Node $node, $user, string $token ){
    
    $charge = Charge::create([
      'amount' => (int)$node->field_price->value * 100,
      'currency' => 'usd',
      'description' => $node->title->value,
      'source' => $token,
      'metadata' => [
        'name' => $user->getAccountName(),
        'mail' => $user->getEmail()
      ],
    ]);
    return $charge;
  }


}
