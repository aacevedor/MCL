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
use Drupal\stripe_payments\Plugin\Block\StripePaymentsChargeBlock;


/**
 * Provides a 'StripePaymentsChargeBlock' block.
 *
 * @Block(
 *  id = "stripe_payments_charge_block_inscription",
 *  admin_label = @Translation("Stripe payments charge block inscription"),
 * )
 */
class StripePaymentsChargeBlockInscription extends StripePaymentsChargeBlock implements ContainerFactoryPluginInterface {

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
    return AccessResult::allowedIf( true);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();
    //dpm($this->currentRoute->getParameters());
    $build['#content']['route'] = '/node/'. $this->currentRoute->getParameter('eform_submission')->field_producto->target_id.'/'.$this->currentRoute->getParameter('eform_submission')->id().'/buy';

    return $build;
  }

}
