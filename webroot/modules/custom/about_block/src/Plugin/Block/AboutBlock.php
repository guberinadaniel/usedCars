<?php

namespace Drupal\about_block\Plugin\Block;
use Drupal\file\Entity\File;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'AboutBlock' block.
 *
 * @Block(
 *  id = "About_block",
 *  admin_label = @Translation("About block"),
 * )
 */



class AboutBlock extends BlockBase {

    public function buildContent () {
        $nodeid = intval(\Drupal::routeMatch()->getParameter('node')->Id());


        $query = \Drupal::database()->select( 'node_field_data', 'n' );
        $query->condition( 'n.nid', $nodeid, '=' );

        $query->distinct();

        $query->innerJoin('node__field_transmission', 'ft',
            'ft.entity_id = n.nid' );
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

        $query->innerJoin('node__field_car_assembly', 'nfca',
            'nfca.entity_id = n.nid' );
        $query->innerJoin('taxonomy_term_field_data', 'tfd4',
            'tfd4.tid = nfca.field_car_assembly_target_id' );

        $query->innerJoin('node__field_body_ty', 'nfbt',
            'nfbt.entity_id = n.nid' );
        $query->innerJoin('taxonomy_term_field_data', 'tfd5',
            'tfd5.tid = nfbt.field_body_ty_target_id' );


        $query->leftJoin('node__field_car_image','fki',
                'fki.entity_id = n.nid' );
        $query->condition('fki.delta', 0, '=' );

        $query->innerJoin('node__field_millage','nfm',
            'nfm.entity_id = n.nid');
        $query->innerJoin('node__field_engine_capacity','nfec',
            'nfec.entity_id = n.nid');
        $query->innerJoin('node__field_city','nfcc',
            'nfcc.entity_id = n.nid');
        $query->innerJoin('node__field_car_','nfcol',
            'nfcol.entity_id = n.nid');
        $query->orderBy('n.created' , 'DESC');

//        $query->addField( 'n', 'title' );
        $query->addField( 'n', 'status' );
        $query->addField( 'n', 'nid' );
        $query->addField( 'nfm', 'field_millage_value' );
        $query->addField( 'nfec', 'field_engine_capacity_value' );
        $query->addField( 'nfcc', 'field_city_value' );
        $query->addField( 'nfcol', 'field_car__value' );
        $query->addField( 'fki', 'field_car_image_target_id','image_id' );
        $query->addField( 'tfd', 'name','transmission' );
        $query->addField( 'tfd2', 'name','fuel_type' );
        $query->addField( 'tfd3', 'name','model_year' );
        $query->addField( 'tfd4', 'name','assembly' );
        $query->addField( 'tfd5', 'name','body_type' );
        $query->addField( 'n','changed' );
        $query->range(0,1);

//        $query = $query->extend( 'Drupal\Core\Database\Query\PagerSelectExtender' )->limit( 3 );





        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ( $results as $result ) {
//            $file = File::load($result->image_id);
//            $url = \Drupal\image\Entity\ImageStyle::load('car_block_image_155x105_')->buildUrl($file->getFileUri());
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$result->nid);
            $data[] = [
                'nid'       => $alias,
                'ad_id'  => $result->nid,
//                'title'     => $result->title,
                'millage'   => $result->field_millage_value,
                'engine_capacity' => $result->field_engine_capacity_value,
                'transmission'  => $result->transmission,
                'assembly'  => $result->assembly,
                'fuel_type' => $result->fuel_type,
                'model_year' => $result->model_year,
                'body_type'  => $result->body_type,
                'city' => $result->field_city_value,
                'color' => $result->field_car__value,
                'changed' => $result->changed,
                'published' => intval( $result->status ),
//                'image' => $url,
            ];
        }

        return $data;
    }

	/**
	 * {@inheritdoc}
	 */
	public function build () {

		return array(
			'#theme'    => 'about_block',
			'#content'  => $this->buildContent(),
			'#cache'    => [
				'max-age' => 0,
			],
		);
	}
}
