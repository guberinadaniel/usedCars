<?php

namespace Drupal\form_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DefaultForm.
 */
class DefaultForm extends FormBase {

  /**
   * {@inheritdoc}
   */


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'customer_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

      $nodeid = intval(\Drupal::routeMatch()->getParameter('node')->Id());
      $query = \Drupal::database()->select( 'node_field_data', 'n' );
      $query->condition( 'n.type', 'car', '=' );
      $query->condition( 'n.nid', $nodeid, '=' );


      $query->innerJoin('users_field_data', 'ufd',
          'ufd.uid = n.uid' );

      $query->addField('ufd', 'mail', 'usermail');

      $result = $query->execute()->fetchField();


//    $config = $this->config('form_module.default');
    $form['your_name'] = [
      '#type' => 'textfield',
      '#placeholder' => $this->t('Your Name'),
        '#required' => true,
//      '#default_value' => $config->get('your_name'),
    ];
    $form['your_email'] = [
      '#type' => 'email',
      '#placeholder' => $this->t('Your Email'),
        '#required' => true,
//      '#default_value' => $config->get('your_email'),
    ];
    $form['phone'] = [
      '#type' => 'tel',
      '#placeholder' => $this->t('Phone'),
        '#required' => true,
//      '#default_value' => $config->get('phone'),
    ];
    $form['subject'] = [
      '#type' => 'textfield',
      '#placeholder' => $result,
        '#required' => true,
//      '#default_value' => $config->get('subject'),
    ];
    $form['message'] = [
      '#type' => 'textarea',
       '#required' => true,
      '#placeholder' => $this->t('Message'),
//      '#default_value' => $config->get('message'),
    ];

    $form['submit'] = [
      '#type' => 'button',
      '#title' => $this->t('submit'),
//      '#default_value' => $config->get('submit'),
        '#value' => $this->t('SUBMIT')
    ];
//    return parent::buildForm($form, $form_state);

      return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
//      $form_values = $form_state->getValues();
//      $to = $form_values['email'];

    $this->config('form_module.default')
      ->set('your_name', $form_state->getValue('your_name'))
      ->set('your_email', $form_state->getValue('your_email'))
      ->set('phone', $form_state->getValue('phone'))
      ->set('subject', $form_state->getValue('subject'))
      ->set('message', $form_state->getValue('message'))
      ->set('submit', $form_state->getValue('submit'))

      ->save();
  }

}
