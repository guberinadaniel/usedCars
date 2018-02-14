<?php

namespace Drupal\price_block\Plugin\Block;
use Drupal\file\Entity\File;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'PriceBlock' block.
 *
 * @Block(
 *  id = "Price_block",
 *  admin_label = @Translation("Price block"),
 * )
 */
class PriceBlock extends BlockBase {

    public function buildContent () {
        $nodeid = intval(\Drupal::routeMatch()->getParameter('node')->Id()); //get node id from current url
        $query = \Drupal::database()->select( 'node_field_data', 'n' );
//        $query = \Drupal::database()->select( 'users_field_data', 'u' );
        $query->condition( 'n.nid', $nodeid, '=' );  //node id instead of car content type

        $query->distinct();




        $query->innerJoin('node__field_price','nfp',
            'nfp.entity_id = n.nid');

        $query->innerJoin('node__field_country','nfc',
            'nfc.entity_id = n.nid');


        $query->leftJoin('node__field_car_image','fki',
                'fki.entity_id = n.nid' );
        $query->condition('fki.delta', 0, '=' );



        $query->innerJoin('node__field_city','nfcc',
            'nfcc.entity_id = n.nid');
//        $query->innerJoin('users_field_data', 'ufd',
//            'ufd.uid = n.uid');

        $query->leftJoin('user__field_user_phone','nph',
            'nph.entity_id = n.uid');

//        $query->addField( 'n', 'title' );
//        $query->addField( 'n', 'status' );
        $query->addField( 'n', 'nid' );
        $query->addField( 'nfcc', 'field_city_value' );
        $query->addField( 'nfc', 'field_country_value' );
        $query->addField( 'nph', 'field_user_phone_value', 'phone' );
        $query->addField( 'fki', 'field_car_image_target_id','image_id' );
        $query->addField( 'n','changed' );
        $query->addField( 'nfp', 'field_price_value' );
        $query->range(0,1);




        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ( $results as $result ) {
//            $file = File::load($result->image_id);
//            $url = \Drupal\image\Entity\ImageStyle::load('car_block_image_155x105_')->buildUrl($file->getFileUri());
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$result->nid);
            $data[] = [
                'nid'       => $alias,
                'ad_id'  => $result->nid,
                'city' => $result->field_city_value,
                'country'  => $result->field_country_value,
                'changed' => $result->changed,
                'phone' => $result->phone,
                'price'  => $result->field_price_value,
//                'published' => intval( $result->status ),
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
			'#theme'    => 'price_block',
			'#content'  => $this->buildContent(),
			'#cache'    => [
				'max-age' => 0,
			],
		);
	}
}
