<?php

namespace Drupal\stripe_payments\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Provides a 'StatusUserStripePayments' block.
 *
 * @Block(
 *  id = "status_user_stripe_payments_block",
 *  admin_label = @Translation("Status user stripe payments"),
 * )
 */
class StatusUserStripePayments extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Session\AccountProxyInterface definition.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new StatusUserStripePayments object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The AccountProxyInterface definition.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    AccountProxyInterface $current_user
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['inputtext'] = [
      '#type' => 'text_format',
      '#title' => $this->t('InputText'),
      '#description' => $this->t('Just an input text'),
      '#default_value' => $this->configuration['inputtext'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['inputtext'] = $form_state->getValue('inputtext')['value'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $entities = [];
    $transaccions = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties(['type' => 'transaccion', 'field_usuario' => $this->currentUser->id()]);
    
    foreach($transaccions as $key => $value){
      $tax_name = $value->get('field_tipo_de_transaccion')->referencedEntities()[0]->name->value;
      $entities[$tax_name][] = $value;
    }

    $total = [];
    if( count($entities) ){
      $array_keys = array_keys($entities);
      foreach($array_keys as $key => $value){
        foreach($entities[$value] as $key => $entity){
          $total[$value] += $entity->field_cantidad->value;
        }
      }
      $total['diference'] = self::array_subtract( $total );
    }else{  
      $total = 0;
    }

    $build = [];
    $build['#theme'] = 'status_user_stripe_payments_block';
    $build['#content']['data'] = $this->configuration['inputtext'];
    $build['#content']['user'] = $this->currentUser->getDisplayName();
    $build['#content']['total'] = $total;
    
    return $build;
  }


  public function array_subtract(array $input) {
    $result = reset($input);                            // First element of the array
    foreach (array_slice($input, 1) as $value) {        // Use array_slice to avoid subtracting the first element twice
        $result -= $value;                          // Subtract the value
    }
    return $result;                                // Return the result
  }

}
