<?php

namespace Drupal\stripe_payments\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class StripePaymentsConfigForm.
 */
class StripePaymentsConfigForm extends FormBase {

  /**
   * Config Settings.
   * 
   * @var string
   */


  const SETTINGS = 'stripe.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'stripe_payments_config_form';
  }


  /**
   * {@inheritdoc}
   * 
   *
   */

  protected function getEditableConfigNames(){
    return [
      static::SETTINGS,
    ];
  }


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $config = $this->config(static::SETTINGS);
    
    $form['public_stripe_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Public Stripe Key'),
      '#default_value' => $config->get('public_stripe_key'),
      '#description' => $this->t('Stripe Public Key, in https://dashboard.stripe.com/test/apikeys'),
    ];

    $form['secret_stripe_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Secret Stripe Key'),
      '#default_value' => $config->get('secret_stripe_key'),
      '#description' => $this->t('Stripe Secret Key, in https://dashboard.stripe.com/test/apikeys'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
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
    \Drupal::messenger()->addMessage($this->t('This info is saved'));

    $config = $this->configFactory->getEditable(static::SETTINGS);

    foreach($form_state->getValues() as $key => $value){
      $config->set($key, $value);
    }
    
    $config->save();    
  }



}
