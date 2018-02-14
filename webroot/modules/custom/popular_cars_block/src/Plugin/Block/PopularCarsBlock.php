<?php

namespace Drupal\popular_cars_block\Plugin\Block;
use Drupal\file\Entity\File;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'popularCarsBlock' block.
 *
 * @Block(
 *  id = "popular_cars_block",
 *  admin_label = @Translation("popular cars block"),
 * )
 */

class PopularCarsBlock extends BlockBase {

    public function buildContent () {

        $query = \Drupal::database()->select( 'node_field_data', 'n' );
        $query->condition( 'n.type', 'car', '=');
//        $query->condition('sticky',1,'=');

        $query->distinct();



        $query->innerJoin('node_counter', 'nc',
            'nc.nid = n.nid' );
        $query->innerJoin('node__field_car_model_year', 'fcmy',
            'fcmy.entity_id = n.nid' );
        $query->innerJoin('taxonomy_term_field_data', 'tfd3',
            'tfd3.tid = fcmy.field_car_model_year_target_id' );


        $query->leftJoin('node__field_car_image','fki',
            'fki.entity_id = n.nid' );
        $query->condition('fki.delta', 0, '=' );

        $query->innerJoin('node__field_state','nfst',
            'nfst.entity_id = n.nid');

        $query->innerJoin('node__field_city','nfcc',
            'nfcc.entity_id = n.nid');
        $query->innerJoin('node__field_price','nfp',
            'nfp.entity_id = n.nid');
        $query->orderBy('nc.totalcount' , 'DESC');

        $query->addField( 'n', 'title' );
        $query->addField( 'n', 'status' );
        $query->addField( 'n', 'nid' );
        $query->addField( 'nfcc', 'field_city_value' );
        $query->addField( 'nfst', 'field_state_value' );
        $query->addField( 'nfp', 'field_price_value' );
        $query->addField( 'fki', 'field_car_image_target_id','image_id' );
        $query->addField( 'tfd3', 'name','model_year' );
//        $query->range(0,4);


        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ( $results as $result ) {
            $file = File::load($result->image_id);
            $url = \Drupal\image\Entity\ImageStyle::load('front_page_slider')->buildUrl($file->getFileUri());
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$result->nid);
            $data[] = [
                'nid'       => $alias,
                'title'     => $result->title,
                'model_year' => $result->model_year,
                'state'     => $result->field_state_value,
                'city' => $result->field_city_value,
                'price'  => $result->field_price_value,
                'published' => intval( $result->status ),
                'image' => $url,
            ];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function build () {

        return array(
            '#theme'    => 'popular_cars_block',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }
}
