<?php

namespace Drupal\mcl_votes\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityManagerInterface;

/**
 * Provides a 'RedeemCodesFormBlock' block.
 *
 * @Block(
 *  id = "redeem_mcl_codes_form_block",
 *  admin_label = @Translation("Redeem Mcl Codes Form Block"),
 * )
 */
class RedeemMclCodesFormBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Routing\CurrentRouteMatch definition.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $currentRouteMatch;

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
   * Constructs a new GenerateMclVoteFormBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $current_route_match
   *   The CurrentRouteMatch definition.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The AccountProxyInterface definition.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The EntityManagerInterface definition.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    CurrentRouteMatch $current_route_match,
    AccountProxyInterface $current_user,
    EntityManagerInterface $entity_manager
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentRouteMatch = $current_route_match;
    $this->currentUser = $current_user;
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
      $container->get('current_user'),
      $container->get('entity.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\mcl_votes\Form\RedeemMclCodesForm');
    
    // $build = [];
    // $build['#theme'] = 'generate_mcl_vote_form_block';
    // $build['generate_mcl_vote_form_block']['#markup'] = 'Implement GenerateMclVoteFormBlock.';

    return $form;
  }

}
