<?php

namespace Drupal\stripe_payments\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Access\AccessResult;


/**
 * Provides a 'StripePaymentsChargeBlock' block.
 *
 * @Block(
 *  id = "stripe_payments_charge_block",
 *  admin_label = @Translation("Stripe payments charge block"),
 * )
 */
class StripePaymentsChargeBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Session\AccountProxyInterface definition.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;


  protected $currentRoute;
  /**
   * Constructs a new StripePaymentsChargeBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigManagerInterface $config_manager
   *   The ConfigManagerInterface definition.
   * @param \Drupal\Core\DependencyInjection\ContainerBuilder $service_container
   *   The ContainerBuilder definition.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The AccountProxyInterface definition.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    AccountProxyInterface $current_user,
    CurrentRouteMatch $current_route
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
    $this->currentRoute = $current_route;

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

    /**
   * {@inheritdoc}
   */

  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIf(in_array('authenticated',$account->getRoles()));
  }




  /**
   * {@inheritdoc} 
   */
  public function build() {
    
    // dpm($this->currentRoute->getParameter('node')->nid->value);
    $build = [];
    $build['#theme'] = 'stripe_payments_charge_block';
    $build['stripe_payments_charge_block']['#markup'] = 'Implement StripePaymentsChargeBlock.';
    $build['#attached']['library'][] = 'stripe_payments/stripe_payments';
    $build['#content']['current_user_name'] = $this->currentUser->getAccountName();
    $build['#content']['current_user_mail'] = $this->currentUser->getEmail();
    $build['#content']['nid']               = $this->currentRoute->getParameter('node')->nid->value;
    // $build['#cache']['max-age'] = 0;

    return $build;
  }

}
