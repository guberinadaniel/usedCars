<?php

namespace Drupal\contact_us\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ContactForm.
 */
class ContactForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'contact_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['full_name'] = [
      '#type' => 'textfield',
      '#maxlength' => 64,
      '#placeholder' =>t('Full Name'),
      '#size' => 64,
    ];
    $form['phone'] = [
      '#type' => 'tel',
        '#placeholder' =>t('Phone Number'),
    ];
    $form['email'] = [
      '#type' => 'email',
        '#placeholder' =>t('Email'),
    ];
    $form['address'] = [
      '#type' => 'textfield',
      '#maxlength' => 64,
        '#placeholder' =>t('Address'),
      '#size' => 64,
    ];
    $form['details'] = [
      '#type' => 'textarea',
        '#placeholder' =>t('Details'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('SUBMIT NOW'),
    ];

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
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      drupal_set_message($key . ': ' . $value);
    }

  }

}
