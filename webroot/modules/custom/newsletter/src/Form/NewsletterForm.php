<?php
/**
 * Created by PhpStorm.
 * User: veus
 * Date: 11/24/17
 * Time: 9:54 AM
 */

namespace Drupal\newsletter\Form;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;

class NewsletterForm extends FormBase
{
    public function getFormId()
    {
        return 'newsletter_form_block';

    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['details'] = [
            '#markup' => '<h3>Submit Newsletters</h3>',
        ];
        $form['subtitle'] = [
            '#markup' => '<p class="subtittle">Subscribe to our newsletter !</p>',
        ];
        $form['email'] = array(
            '#type' => 'email',
            '#placeholder' => 'Enter Your Email',
            '#size' => 32,
            '#required' => false,
            '#ajax' => [
                'callback' => array($this, 'ajaxFormSubmit'),
                'event' => 'change',
                'progress' => array(
                    'type' => 'throbber',
                    'message' => t('Verifying email...'),
                ),
            ],
        );

        $form['actions'] = array(
            '#type' => 'submit',
            '#value' => t('Subscribe'),
        );
        $form['message'] = [
            '#type' => 'container',
            '#attributes' => [
                'id' => 'newsletter-message',
            ],
        ];

//        $form['description'] = array(
//            '#markup' => '<p class="spam">Dont\'t worry we hate spams</p>',
//        );

        return $form;
    }

    /**
     * {@inheritdoc}
     */

    protected function validateEmail(array &$form, FormStateInterface $form_state) {
        if (substr($form_state->getValue('email'), -4) !== '.com') {
            return FALSE;
        }
        return TRUE;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        if (!$this->validateEmail($form, $form_state)) {
//            $form_state->setErrorByName('email', $this->t('This is not a .com email address.'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {

    }

    public function ajaxFormSubmit(array $form, FormStateInterface $form_state)
    {
        $values = $form_state->getValue('email');
        $valid = $this->validateEmail($form, $form_state);
        $response = new AjaxResponse();

        $email = \Drupal::database()->select('newsletter', 'n');
        $email->condition('n.email', $values, '=');
        $email->addField('n', 'email');
        $emails = $email->execute()->fetchAll();

        if ($valid && empty($emails)) {

            $insert = \Drupal::database()->insert('newsletter');
            $insert->fields([
                'email',
                'date',
            ]);
            $insert->values([
                $values,
                date('F d, Y'),
            ]);

            $insert->execute();

//            $item = [
//                '#type'       => 'container',
//                '#attributes' => [
//                    'id'    => 'newsletter-message',
//                    'class' => 'success',
//                ],
//                '#markup'     => "<p>Thank You! </p>",
//            ];
            $css = [
                'border' => '1px solid green',
                'text-align'=>'center',
                'color'=>'green',
            ];
            $message = $this->t('Thank you for your trust.');
        } else {
//            $item = [
//                '#type'       => 'container',
//                '#attributes' => [
//                    'id'    => 'newsletter-message',
//                    'class' => 'fail',
//                ],
//                '#markup'     => "<p>Email already exists in our database</p>",
//
//            ];
            $css = [
//                'border' => '1px solid red',
                'text-align'=>'center',
                'color'=>'red',
            ];
            $message = $this->t('Email not valid or exist in database. Please type correct address!');
        }
//        $renderer = \Drupal::service( 'renderer' );
//        $item = $renderer->render( $item );
//        $response->addCommand( new ReplaceCommand( '#newsletter-message', $item ) );
//        $response->addCommand(new \Drupal\Core\Ajax\HtmlCommand('#newsletter-message', $message));

        $response->addCommand(new CssCommand('#newsletter-message', $css));
        $response->addCommand(new HtmlCommand('#newsletter-message', $message));
        return $response;
    }
}
