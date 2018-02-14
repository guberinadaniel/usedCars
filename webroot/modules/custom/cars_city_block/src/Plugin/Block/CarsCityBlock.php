<?php

namespace Drupal\cars_city_block\Plugin\Block;
use Drupal\file\Entity\File;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'CarsCityBlock' block.
 *
 * @Block(
 *  id = "cars_city_block",
 *  admin_label = @Translation("CarsCity block"),
 * )
 */

class CarsCityBlock extends BlockBase {

    public function buildContent () {

        $query = \Drupal::database()->select( 'node_field_data', 'n' );
        $query->condition( 'n.type', 'car', '=' );


        $query->innerJoin('node__field_registered_city', 'rc',
            'rc.entity_id = n.nid' );
        $query->innerJoin('taxonomy_term_field_data', 'tfd',
            'tfd.tid = rc.field_registered_city_target_id' );

        $query->addExpression( 'rc.field_registered_city_target_id', 'city_id' );

        $query->addExpression( 'tfd.name','city' );
        $query->addExpression( 'rc.field_registered_city_target_id', 'tid');






        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ( $results as $result ) {

            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/taxonomy/term/'.$result->tid);

            $data[$result->city_id]['city'] = $result->city;
            $data[$result->city_id]['tid'] = $alias;
            if(!isset($data[$result->city_id]['count'])) {
                $data[$result->city_id]['count'] = 1;
            } else {
                $data[$result->city_id]['count']++;
            }

        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function build () {

        return array(
            '#theme'    => 'cars_city_block',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }
}
