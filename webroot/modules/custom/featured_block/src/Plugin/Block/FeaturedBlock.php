<?php

namespace Drupal\featured_block\Plugin\Block;
use Drupal\file\Entity\File;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'FeaturedBlock' block.
 *
 * @Block(
 *  id = "featured_block",
 *  admin_label = @Translation("Featured block"),
 * )
 */
class FeaturedBlock extends BlockBase {

    public function buildContent () {
        $nodeid = intval(\Drupal::routeMatch()->getParameter('node')->Id());

        $query = \Drupal::database()->select('node__field_transmission','f' );
        $query->condition('f.entity_id', $nodeid, '=' );
        $query->addField('f', 'field_transmission_target_id', 'tid' );
        $result = $query->execute()->fetchField();

        $transmission = null;

        if(isset($result)) {
            $transmission = intval($result);
        }

        $query = \Drupal::database()->select( 'node_field_data', 'n' );
        $query->condition( 'n.type', 'car', '=' );
        $query->condition( 'n.nid', $nodeid, '!=' );

        $query->distinct();

        $query->innerJoin('node__field_transmission', 'ft',
            'ft.entity_id = n.nid' );
        if(isset($transmission)) {
            $query->condition('ft.field_transmission_target_id', $transmission, '=');
        }
        $query->innerJoin('taxonomy_term_field_data', 'tfd',
            'tfd.tid = ft.field_transmission_target_id' );

        $query->innerJoin('node__field_fue', 'fft',
            'fft.entity_id = n.nid' );
        $query->innerJoin('taxonomy_term_field_data', 'tfd2',
            'tfd2.tid = fft.field_fue_target_id' );

        $query->innerJoin('node__field_car_model_year', 'fcmy',
            'fcmy.entity_id = n.nid' );
        $query->innerJoin('taxonomy_term_field_data', 'tfd3',
            'tfd3.tid = fcmy.field_car_model_year_target_id' );


        $query->leftJoin('node__field_car_image','fki',
                'fki.entity_id = n.nid' );
        $query->condition('fki.delta', 0, '=' );

        $query->innerJoin('node__field_millage','nfm',
            'nfm.entity_id = n.nid');
        $query->innerJoin('node__field_engine_capacity','nfec',
            'nfec.entity_id = n.nid');
        $query->innerJoin('node__field_city','nfcc',
            'nfcc.entity_id = n.nid');
        $query->innerJoin('node__field_city','nfcc',
            'nfcc.entity_id = n.nid');
        $query->innerJoin('node__field_price','nfp',
            'nfp.entity_id = n.nid');
        $query->orderBy('n.created' , 'DESC');

        $query->addField( 'n', 'title' );
        $query->addField( 'n', 'status' );
        $query->addField( 'n', 'nid' );
        $query->addField( 'nfm', 'field_millage_value' );
        $query->addField( 'nfec', 'field_engine_capacity_value' );
        $query->addField( 'nfcc', 'field_city_value' );
        $query->addField( 'nfp', 'field_price_value' );
        $query->addField( 'fki', 'field_car_image_target_id','image_id' );
        $query->addField( 'tfd', 'name','transmission' );
        $query->addField( 'tfd2', 'name','fuel_type' );
        $query->addField( 'tfd3', 'name','model_year' );
        $query->addField( 'n','changed' );
        $query->range(0,3);

//        $query = $query->extend( 'Drupal\Core\Database\Query\PagerSelectExtender' )->limit( 3 );





        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ( $results as $result ) {
            $file = File::load($result->image_id);
            $url = \Drupal\image\Entity\ImageStyle::load('car_block_image_155x105_')->buildUrl($file->getFileUri());
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$result->nid);
            $data[] = [
                'nid'       => $alias,
                'title'     => $result->title,
                'millage'   => $result->field_millage_value,
                'engine_capacity' => $result->field_engine_capacity_value,
                'transmission'  => $result->transmission,
                'fuel_type' => $result->fuel_type,
                'model_year' => $result->model_year,
                'city' => $result->field_city_value,
                'changed' => \Drupal::service('date.formatter')->formatInterval(time() - $result->changed),
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
			'#theme'    => 'featured_block',
			'#content'  => $this->buildContent(),
			'#cache'    => [
				'max-age' => 0,
			],
		);
	}
}
