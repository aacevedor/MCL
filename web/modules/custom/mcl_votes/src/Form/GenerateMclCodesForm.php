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
class GenerateMclCodesForm extends FormBase {

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
   * Constructs a new GenerateMclVoteForm object.
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
    return 'generate_mcl_codes_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->node = $this->currentRouteMatch->getParameter('node');

    $form['no_codes'] = [
      '#type' => 'number',
      '#title' => $this->t('No. Codes'),
      '#description' => $this->t('Into de number of codes to generate'),
      '#default_value' => '1',
    ];


    $form['no_vodes_per_node'] = [
      '#type' => 'number',
      '#title' => $this->t('No. Votes Per Node'),
      '#description' => $this->t('Into the number of votes per code'),
      '#default_value' => '1',
    ];


    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Generate'),
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
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    // foreach ($form_state->getValues() as $key => $value) {
    //   \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
    // }

    $no_nodes = $form_state->getValue('no_codes');
    $no_votes_per_node = $form_state->getValue('no_vodes_per_node');

    for ($i=1; $i <= $no_nodes ; $i++) { 

      $str_random = substr(md5(mt_rand()), 0, 7);
      $node = Node::create([
        'type'                      => 'transaccion',
        'title'                     => t('code'). ' - ' .$str_random. ' - ' .$no_votes_per_node,
        'field_cantidad'            => $no_votes_per_node,
        'field_tipo_de_transaccion' => 21,
        'field_codigo'              => $str_random,
        // 'field_usuario'             => $this->currentUser->id(),
        // 'field_candidata'           => $this->node->id(),
        // 'field_reto'                => $this->reto->id()
      ]);

      try {
        $node->save();
        $message = $this->t('Node(s) has been successful');
      } catch (\Throwable $th) {
        $message = $this->t($th->getMessage());
      }

      drupal_set_message($message);
    }
  }
}
