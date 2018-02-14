<?php

namespace Drupal\car_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
/**
 * Class CarSearch.
 */
class CarSearch extends FormBase
{


    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'car_search';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {

//        $op = $_GET['op'];
//
//
//        if($op == 'Clear'){
//            $_GET = [];
//        }

        $form = [];

        $form['#method'] = 'GET';


        $form['filters'] = [
            '#type' => 'fieldset',
            '#title' => t('filters'),
            '#collapsible' => true,
            '#attributes' => array('class' => array('inline')),
        ];

//        $form['filters']['reset'] = array(
//            '#type' => 'submit',
////            '#submit' => 'CarSearch::car_search_clear_form',
////            '#button_type' => 'reset',
//            '#value' => t('RESET'),
////            '#validate' => array(),
//        );

//        $form['filters']['reset'] = array(
//            '#type' => 'button',
//            '#button_type' => 'reset',
//            '#value' => t('Clear'),
////            '#validate' => array(),
//            '#attributes' => array(
////                'onclick' => 'this.form.reset(); return false;',
////                'onclick' => 'window.location.replace("/car_search");'
//                'onclick' => 'window.location.reload(); '
//            ),
//        );

        $form['filters']['reset'] = array(
            '#markup' => '<a href="/car_search" class="edit-reset-car-search">Reset</a>',
        );


        $form['filters']['container'] = array(
            '#type' => 'container',
            '#attributes' => array(
                'class' => 'item-container',
            ),
            $form['items'] = [
                '#type' => 'item',
                '#title' => t('item contain'),
            ]
        );


        $form['filters']['car_name'] = [
            '#type' => 'textfield',
            '#title' => t('Search By Keyword'),
            '#size' => 32,
            '#placeholder' => t('Keyword'),
            '#default_value' => isset($_GET['car_name']) ? $_GET['car_name'] : '',
        ];
        $form['filters']['actions']['#type'] = 'actions';
        $form['filters']['actions_go']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Go!'),
        ];


//        $form['filters']['car_links'] = [
//            '#title' => t('Examples'),
//            '#type' => 'item',
//            '#options' => $this->trans(),
//            '#markup' => '<a class="user_fb" href="http://facebook.com">LINK</a>',
//            '#allowed_tags' => ['a',],
//        ];

        $cities = NULL;

        if ($cache = \Drupal::cache()->get('cities')) {
            $cities = $cache->data;
        } else {
            $cities = \Drupal::database()
                ->query('
                    SELECT 
                      COUNT(*) AS `count`, 
                      `field_city_value` as city_name
                    FROM `node__field_city` 
                    GROUP BY `field_city_value` 
                    ORDER BY `field_city_value`
                ')->fetchAll();

            \Drupal::cache()->set('cities', $cities);
        }
        $options = [];
        foreach ($cities as $city) {
            $options[$city->city_name] =  '<span class="city-name">'.$city->city_name.'</span>' . '<span class="city-count">'. $city->count . '</span>';
        }



        $form['filters']['city'] = [
            '#type' => 'checkboxes',
            '#title' => t('Cities'),
            '#options' => $options,
            '#default_value' => isset($_GET['city']) ? $_GET['city'] : [],
        ];

        $options = [
            'none' => 'Select car type',
            'New' => 'New',
            'Used' => 'Used',
        ];

        $form['filters']['car_type'] = [
            '#type' => 'select',
            '#title' => t('Car Type'),
            '#options' => $options,
            '#default_value' => isset($_GET['car_type']) ? $_GET['car_type'] : '',
        ];


        $options = [
            0 => 'Most recent',
            1 => 'Price: Low to High',
            2 => 'Price: High to Low',
        ];

        $form['filters']['sort_by'] = [
            '#type' => 'select',
            '#title' => t('Sort by'),
            '#options' => $options,
            '#default_value' => isset($_GET['sort_by']) ? $_GET['sort_by'] : '',
        ];


        $form['filters']['transmission'] = [
            '#type' => 'checkboxes',
            '#title' => t('Transmission'),
            '#options' => $this->trans(),
            '#multiple' => TRUE,
            '#default_value' => isset($_GET['transmission']) ? $_GET['transmission'] : [],
        ];

        $form['filters']['fuel_type'] = [
            '#type' => 'checkboxes',
            '#title' => t('Engine Type'),
            '#options' => $this->fuelfu(),
            '#multiple' => TRUE,
            '#default_value' => isset($_GET['fuel_type']) ? $_GET['fuel_type'] : [],
        ];

        $form['filters']['registered_city'] = [
            '#type' => 'checkboxes',
            '#title' => t('Registered City'),
            '#options' => $this->regcity(),
            '#default_value' => isset($_GET['registered_city']) ? $_GET['registered_city'] : [],
        ];


        $brands = \Drupal::database()->select('taxonomy_term_field_data', 'tfd' );
        $brands->condition('vid', 'car_brand', '=' );
        $brands->fields('tfd', ['tid', 'name']);
        $brands->orderBy('name', 'ASC' );
        $brands = $brands->execute()->fetchAll();

        $options = ['none' => 'Select car brand'];

        foreach( $brands as $brand ) {
            $options[$brand->tid] = $brand->name;
        }

        $form['filters']['car_brand'] = [
            '#type' => 'select',
            '#title' => t('Car Make'),
            '#options' => $options,
            '#default_value' => isset($_GET['car_brand']) ? $_GET['car_brand'] : 'none',
        ];


        $years = \Drupal::database()->select('taxonomy_term_field_data', 'tfd3' );
        $years->condition('vid', 'model_year', '=' );
        $years->fields('tfd3', ['tid', 'name']);
        $years->orderBy('name', 'ASC' );
        $years = $years->execute()->fetchAll();


        $options = [
            'none' => 'From'
        ];

        foreach( $years as $year ) {
            $options[$year->tid] = $year->name;
        }

        $form['filters']['from'] = [
            '#type' => 'select',
            '#title' => t('Year Range'),
            '#options' => $options,
            '#default_value' => isset($_GET['from']) ? $_GET['from'] : 'none',
        ];

        $options = [
            'none' => 'To',
        ];

        foreach( $years as $year ) {
            $options[$year->tid] = $year->name;
        }

        $form['filters']['to'] = [
            '#type' => 'select',
            '#options' => $options,
            '#default_value' => isset($_GET['to']) ? $_GET['to'] : 'none',
        ];


        $options = [];

        $query = \Drupal::database()->select('node__field_price','f' );
        $query->addField('f', 'field_price_value','price' );
        $results = $query->execute()->fetchCol();

        foreach( $results as $result ) {
            if($result >= 0 && $result < 500 ) {
                if(!isset($options[0]['count'])) {
                    $options[0]['count'] = 1;
                } else {
                    $options[0]['count']++;
                }
                $options[0]['label']='<span class="price-value">$0 to $500</span>' . '<span class="price-count">'.$options[0]['count'].'</span>';             } else if ($result >= 500 && $result <= 2000 ) {
                if(!isset($options[1]['count'])) {
                    $options[1]['count'] = 1;
                } else {
                    $options[1]['count']++;
                }
                $options[1]['label']='<span class="price-value">$500 to $2000</span>' . '<span class="price-count">'.$options[1]['count'].'</span>';             }else if ($result >= 2000 && $result <= 4000 ) {
                if(!isset($options[2]['count'])) {
                    $options[2]['count'] = 1;
                } else {
                    $options[2]['count']++;
                }
                $options[2]['label']='<span class="price-value">$2000 to $4000</span>' . '<span class="price-count">'.$options[2]['count'].'</span>';             }else if ($result >= 4000 && $result <= 8000 ) {
                if(!isset($options[3]['count'])) {
                    $options[3]['count'] = 1;
                } else {
                    $options[3]['count']++;
                }
                $options[3]['label']='<span class="price-value">$4000 to $8000</span>' . '<span class="price-count">'.$options[3]['count'].'</span>';             }else if ($result >= 8000 && $result <= 15000 ) {
                if(!isset($options[4]['count'])) {
                    $options[4]['count'] = 1;
                } else {
                    $options[4]['count']++;
                }
                $options[4]['label']='<span class="price-value">$8000 to $15000</span>' . '<span class="price-count">'.$options[4]['count'].'</span>';            }else if ($result >= 15000 && $result <= 25000 ) {
                if(!isset($options[5]['count'])) {
                    $options[5]['count'] = 1;
                } else {
                    $options[5]['count']++;
                }
                $options[5]['label']='<span class="price-value">$15000 to $25000</span>' . '<span class="price-count">'.$options[5]['count'].'</span>';            }else if ($result >= 25000) {
                if(!isset($options[6]['count'])) {
                    $options[6]['count'] = 1;
                } else {
                    $options[6]['count']++;
                }
                $options[6]['label']='<span class="price-value">Above $25000</span>' . '<span class="price-count">'.$options[6]['count'].'</span>';
            }
        }

        foreach($options as &$option ) {
            $option = $option['label'];
        }
        ksort($options);



        $form['filters']['price_range'] = [
            '#type' => 'checkboxes',
            '#title' => t('Price Range'),
            '#options' => $options,
            '#default_value' => isset($_GET['price_range']) ? $_GET['price_range'] : [],
        ];


        $form['#theme'] = 'car_search';

        $form['pager'] = array(
            '#type' => 'pager',
        );

        $form['filters']['actions']['#type'] = 'actions';
        $form['filters']['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('UPDATE RESULTS'),
        ];


        $data = $this->buildContent();

        $form['#content']['data'] = $data;

        $form['pager'] = array(
            '#type' => 'pager',
        );

        return $form;
    }


    public function trans()
    {
        $query = \Drupal::database()->select('taxonomy_term_field_data', 'tfd4');
        $query->condition('tfd4.vid', 'transmission', '=');
        $query->addField('tfd4', 'name');
        $query->addField('tfd4', 'tid');

        $results= $query->execute()->fetchAll();
        $term = [];

        foreach ($results as $result) {
            $query=\Drupal::database()->select('taxonomy_index', 'ti');
            $query->condition('ti.tid',$result->tid,'=');
            $query->addField('ti','nid');
            $query->execute()->fetchAll();
            $cntResult=$query->execute()->fetchAll();
            $count = count($cntResult);

            $term[$result->tid] = '<span class="transmission-name">'.$result->name.'</span>'.'<span class="transmission-count">'.$count.'</span>';
        }
        return $term;

    }

    public function fuelfu()
    {
        $query = \Drupal::database()->select('taxonomy_term_field_data', 'tfd5');
        $query->condition('tfd5.vid', 'fuel_type', '=');
        $query->addField('tfd5', 'name');
        $query->addField('tfd5', 'tid');

        $results= $query->execute()->fetchAll();
        $term = [];

        foreach ($results as $result) {
            $query=\Drupal::database()->select('taxonomy_index', 'ti');
            $query->condition('ti.tid',$result->tid,'=');
            $query->addField('ti','nid');
            $query->execute()->fetchAll();
            $cntResult=$query->execute()->fetchAll();
            $count = count($cntResult);

            $term[$result->tid] = '<span class="fuel-type-name">'.$result->name.'</span>'.'<span class="fuel-type-count">'.$count.'</span>';
        }
        return $term;

    }

    public function regcity()
    {
        $query = \Drupal::database()->select('taxonomy_term_field_data', 'tfd7');
        $query->condition('tfd7.vid', 'registered_city', '=');
        $query->addField('tfd7', 'name');
        $query->addField('tfd7', 'tid');

        $results= $query->execute()->fetchAll();
        $term = [];

        foreach ($results as $result) {
            $query=\Drupal::database()->select('taxonomy_index', 'ti');
            $query->condition('ti.tid',$result->tid,'=');
            $query->addField('ti','nid');
            $query->execute()->fetchAll();
            $cntResult=$query->execute()->fetchAll();
            $count = count($cntResult);
            $term[$result->tid] = '<span class="reg-city-name">'.$result->name.'</span>'.'<span class="reg-city-count">'.$count.'</span>';
        }
        return $term;

    }

    public function buildContent()
    {

        $query = \Drupal::database()->select('node_field_data', 'n');
        $query->condition('n.type', 'car', '=');

        $query->innerJoin('node__field_car_image', 'fki',
            'fki.entity_id = n.nid');
        $query->condition('fki.delta', 0, '=' );

        $query->innerJoin('node__field_car_model_year', 'fcmy',
            'fcmy.entity_id = n.nid' );
        $query->innerJoin('taxonomy_term_field_data', 'tfd3',
            'tfd3.tid = fcmy.field_car_model_year_target_id' );

        $query->innerJoin('node__field_car_type', 'fct',
            'fct.entity_id = n.nid' );
        $query->innerJoin('node__field_price','nfp',
            'nfp.entity_id = n.nid');
        $query->innerJoin('taxonomy_term_field_data', 'tfd',
            'tfd.tid = fct.field_car_type_target_id' );
        $query->innerJoin('node__field_millage','nfm',
            'nfm.entity_id = n.nid');
        $query->innerJoin('node__field_engine_capacity','nfec',
            'nfec.entity_id = n.nid');

        $query->innerJoin('node__field_fue', 'fft',
            'fft.entity_id = n.nid' );
        $query->innerJoin('taxonomy_term_field_data', 'tfd5',
            'tfd5.tid = fft.field_fue_target_id' );

        $query->innerJoin('node__field_transmission', 'ft',
            'ft.entity_id = n.nid' );
        $query->innerJoin('taxonomy_term_field_data', 'tfd4',
            'tfd4.tid = ft.field_transmission_target_id' );

        $query->innerJoin('node__field_registered_city', 'frc',
            'frc.entity_id = n.nid' );
        $query->innerJoin('taxonomy_term_field_data', 'tfd7',
            'tfd7.tid = frc.field_registered_city_target_id' );

        $query->innerJoin('node__field_car_brand', 'fcb',
            'fcb.entity_id = n.nid' );
        $query->innerJoin('taxonomy_term_field_data', 'tfd2',
            'tfd2.tid = fcb.field_car_brand_target_id' );

        $query->innerJoin('node__field_city','nfc',
            'nfc.entity_id = n.nid');


        if (!empty($_GET['car_name'])) {

            $query->condition('n.title', '%'.$_GET['car_name'].'%', 'LIKE');
        }

        if (isset($_GET['car_type'])  && $_GET['car_type'] != 'none') {

            $query->condition('tfd.name', '%'.$_GET['car_type'].'%', 'LIKE');
        }

        if (isset($_GET['city'])) {

            $query->condition('field_city_value', $_GET['city'], 'IN');
        }

        if (isset($_GET['transmission'])) {

            $query->condition('tfd4.tid', $_GET['transmission'], 'IN');
        }
        if (isset($_GET['registered_city'])) {

            $query->condition('tfd7.tid', $_GET['registered_city'], 'IN');
        }

        if (isset($_GET['fuel_type'])) {

            $query->condition('tfd5.tid', $_GET['fuel_type'], 'IN');
        }

        if (isset($_GET['from']) > isset($_GET['to'])) {

            drupal_set_message(t('Please select correct year order (fom LOW to HIGH).'), 'warning');

        }

        if (isset($_GET['from']) && $_GET['from'] != 'none') {

            $query->condition('tfd3.tid', $_GET['from'], '>=');
        }

        if (isset($_GET['to']) && $_GET['to'] != 'none') {

            $query->condition('tfd3.tid', $_GET['to'], '<=');
        }


        // select lista uporedjivanje sa == 0 , checkbox sa [0]
        if (isset($_GET['sort_by'])) {
            if ($_GET['sort_by'] == 0) {
                $query->orderBy('n.created' , 'DESC');
            } elseif ($_GET['sort_by'] == 1) {
                $query->orderBy('nfp.field_price_value', 'ASC');
            } elseif ($_GET['sort_by'] == 2) {
                $query->orderBy('nfp.field_price_value', 'DESC');
            }
        }


        if (isset($_GET['price_range']) ) {
            if (isset($_GET['price_range'][0])) {
                $query->condition('nfp.field_price_value', 1, '>=')
                && $query->condition('nfp.field_price_value', 500, '<=');
            } elseif (isset($_GET['price_range'][1])) {
                $query->condition('nfp.field_price_value', 500, '>=')
                && $query->condition('nfp.field_price_value', 2000, '<=');
            } elseif (isset($_GET['price_range'][2])) {
                $query->condition('nfp.field_price_value', 2000, '>=')
                && $query->condition('nfp.field_price_value', 4000, '<=');
            } elseif (isset($_GET['price_range'][3])) {
                $query->condition('nfp.field_price_value', 4000, '>=')
                && $query->condition('nfp.field_price_value', 8000, '<=');
            } elseif (isset($_GET['price_range'][4])) {
                $query->condition('nfp.field_price_value', 8000, '>=')
                && $query->condition('nfp.field_price_value', 15000, '<=');
            } elseif (isset($_GET['price_range'][5])) {
                $query->condition('nfp.field_price_value', 15000, '>=')
                && $query->condition('nfp.field_price_value', 25000, '<=');
            } elseif (isset($_GET['price_range'][6])) {
                $query->condition('nfp.field_price_value', 25000, '>');
            }

        }

        if (isset($_GET['car_brand'])  && $_GET['car_brand'] != 'none') {

            $query->condition('tfd2.tid', $_GET['car_brand'], '=');
        }

        $query->addField('n', 'title');
        $query->addField('n', 'nid');
        $query->addField('n', 'changed');
        $query->addField('fki', 'field_car_image_target_id', 'image_id');
        $query->addField( 'nfp', 'field_price_value' );
        $query->addField( 'nfc', 'field_city_value' );
        $query->addField( 'tfd3', 'name','model_year' );
        $query->addField( 'nfm', 'field_millage_value' );
        $query->addField( 'nfec', 'field_engine_capacity_value' );
        $query->addField( 'tfd4', 'name','transmission' );
        $query->addField( 'tfd5', 'name','fuel_type' );
        $query->addField( 'tfd7', 'name','registered_city' );



        $query = $query->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(10);


        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ($results as $result) {
            $file = File::load($result->image_id);
            $url = \Drupal\image\Entity\ImageStyle::load('car_search_image_1000x665')->buildUrl($file->getFileUri());
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$result->nid);

            $data[] = [
//                'path' => \Drupal::service('path.current')->getPath(),
//                'query' => \Drupal::request()->getQueryString(),
//                'spec' => \Drupal::request()->get('car_name'),
                'nid' => $alias,
                'title' => $result->title,
                'price'  => $result->field_price_value,
                'image' => $url,
                'city' => $result->field_city_value,
                'model_year' => $result->model_year,
                'millage'   => $result->field_millage_value,
                'engine_capacity' => $result->field_engine_capacity_value,
                'transmission'  => $result->transmission,
                'fuel_type' => $result->fuel_type,
                'registered_city' => $result->registered_city,
                'changed' => \Drupal::service('date.formatter')->formatInterval(time() - $result->changed),

            ];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // Display result.
        foreach ($form_state->getValues() as $key => $value) {
            drupal_set_message($key . ': ' . $value);
        }

    }

    public function build()
    {

        return array(
            '#theme' => 'car_search',
//            '#content'  => $this->buildContent(),
            '#cache' => [
                'max-age' => 0,
            ],
        );
    }

}
