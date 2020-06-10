<?php

namespace Drupal\mcl_votes\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\node\Entity\Node;

/**
 * Class GenerateMclVoteForm.
 */
class RedeemMclCodesForm extends FormBase {

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
   * Drupal\Core\Entity\EntityManagerInterface definition.
   *
   * @var \Drupal\Core\Node;
   */
  protected $node;

  /**
   * Constructs a new RedeemMclCodesForm object.
   */
  public function __construct(
    CurrentRouteMatch $current_route_match,
    AccountProxyInterface $current_user,
    EntityManagerInterface $entity_manager
  ) {
    $this->currentRouteMatch = $current_route_match;
    $this->currentUser = $current_user;
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_route_match'),
      $container->get('current_user'),
      $container->get('entity.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'redeem_mcl_codes_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->node = $this->currentRouteMatch->getParameter('node');

    $form['code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Into de promotional code'),
      // '#description' => $this->t('Into the promotional code'),
      '#default_value' => '1',
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Redeem'),
      // '#ajax' => [
      //   'callback' => '::generate'
      // ]
    ];

    // $form['label'] = [
    //   '#type' => 'markup',
    //   '#markup' => '<div class="entity-name"> Vota por '. $this->node->title->value .' como tu favorita</div>'
    // ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // foreach ($form_state->getValues() as $key => $value) {
    //   // @TODO: Validate fields.
    // }
    parent::validateForm($form, $form_state);

    $nodes = $this->entityManager
      ->getStorage('node')
      ->loadByProperties([
        'field_codigo' => $form_state->getValue('code'),
      ]);

      
    if( !count($nodes) ){
      $form_state->setError($form['code'],'The code is not valid');
    }
    
    elseif( $nodes->field_usuario ){
      $form_state->setError($form['code'],'The code are is used');
    }
    
    else{
      $form_state->{'node'} = $nodes[key($nodes)];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    // foreach ($form_state->getValues() as $key => $value) {
    //   \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
    // }
    
    try {
      $form_state->node->field_usuario = $this->currentUser->id();
      $form_state->node->save();
      $message = 'The code has been redeem';
    } catch (\Throwable $th) {
      $message = $th->getMessage();
    }
    drupal_set_message($this->t($message));
  }
}
