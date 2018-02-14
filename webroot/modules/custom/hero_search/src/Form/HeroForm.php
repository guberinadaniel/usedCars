<?php

namespace Drupal\hero_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Class HeroForm.
 */
class HeroForm extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'hero_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {


      $form['car_name'] = [
          '#type' => 'textfield',
          '#size' => 32,
          '#default_value' => isset($_GET['car_name']) ? $_GET['car_name'] : '',
      ];

    $form['price_range'] = [
      '#type' => 'select',
      '#options' => [   '' => 'Price',
                        0 => '$0 - $500',
                        1 => '$500 - $2000',
                        2 => '$2000 - $4000',
                        3 => '$4000 - $8000',
                        4 => '$8000 - $15000',
                        5 => '$15000 - $25000',
                        6 => 'Above $25000'
          ]
    ];

      $cities = \Drupal::database()
          ->query('
                    SELECT 
                      COUNT(*) AS `count`, 
                      `field_city_value` as city_name
                    FROM `node__field_city` 
                    GROUP BY `field_city_value` 
                    ORDER BY `field_city_value`
                ')->fetchAll();

$options = ['' => 'Select City'];
foreach ($cities as $city) {
$options[$city->city_name] = $city->city_name;
}



$form['city'] = [
    '#type' => 'select',
    '#options' => $options,
    '#default_value' => isset($_GET['city']) ? $_GET['city'] : [],
];


    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Search'),
      '#prefix' => '<div class="button-div">',
      '#suffix' => '</div>',
    ];

    $form['#cache'] = ['max-age' => 0,];
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

      $car_name = $form_state->getValue('car_name');
      $select_price = $form_state->getValue('price_range');
      $select_city = $form_state->getValue('city');

//      $select_city = [$select_city=>$select_city];  dupli unosi zbog konverzije iz selct liste u checkbox
      if(!empty($select_city))
          $select_city = [$select_city=>$select_city];
      else $select_city = null;

      if(!empty($select_price))
          $select_price = [$select_price=>$select_price];
      else $select_price = null;

      $option = [];

      if(isset($car_name)) {
          $option['query']['car_name'] = $car_name;
      }

      if(isset($select_price)) {
          $option['query']['price_range'] = $select_price;
      }

      if(isset($select_city)) {
          $option['query']['city'] = $select_city;
      }

//      $option = [
//          'query' =>  [
//              'car_name' => $car_name,
//              'price_range' => $select_price,
//              'city' => $select_city
//          ],
//      ];
      $url = Url::fromUri('internal:/car_search', $option);
      $form_state->setRedirectUrl($url);
  }
}
