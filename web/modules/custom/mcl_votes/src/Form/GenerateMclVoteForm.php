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

/**
 * Class GenerateMclVoteForm.
 */
class GenerateMclVoteForm extends FormBase {

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
    return 'generate_mcl_vote_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->node = $this->currentRouteMatch->getParameter('node');

    $form['no_votes'] = [
      '#type' => 'hidden',
      '#title' => $this->t('No Votes'),
      '#description' => $this->t('Just a text input'),
      '#default_value' => '1',
    ];

    $form['message'] = [
      '#type' => 'markup',
      '#markup' => '<div class="result"></div>'
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Vote'),
      '#ajax' => [
        'callback' => '::setVote'
      ]
    ];

    $form['label'] = [
      '#type' => 'markup',
      '#markup' => '<div class="entity-name"> Vota por '. $this->node->title->value .' como tu favorita</div>'
    ];

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
  }


  public function setVote(  array $form, FormStateInterface $form_state ){

    $entity = $this->currentRouteMatch->getParameter('node');

    $service = \Drupal::service('mcl_votes.generate_vote');
    $response_service = $service->GenerateMclVote();
    
    if((int)$response_service){
      $response_service = $this->t('Thanks for your vote');
    }

    $response  = new AjaxResponse();
    $response->addCommand(
      new HtmlCommand(
        '.result',
        '<div class="my_top_message">' . $this->t('@result', ['@result' => $response_service]) . '</div>'
        )
    );

    return $response;

  }

}
