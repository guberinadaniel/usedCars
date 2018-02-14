<?php

namespace Drupal\testimonials_block\Plugin\Block;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'TestimonialsBlock' block.
 *
 * @Block(
 *  id = "testimonials_block",
 *  admin_label = @Translation("Testimonials block"),
 * )
 */

class TestimonialsBlock extends BlockBase {

    public function buildContent () {

        $query = \Drupal::database()->select( 'node_field_data', 'n' );
        $query->condition( 'n.type', 'testimonials', '=' );

//        $query->distinct();



        $query->leftJoin('user__user_picture','fki',
            'fki.entity_id = n.uid' );
//        $query->condition('fki.delta', 0, '=' );

        $query->leftJoin('node__field_testimonial_comment','nftc',
            'nftc.entity_id = n.nid');
        $query->leftJoin('user__field_occupation','nfo',
            'nfo.entity_id = n.uid');

        $query->leftJoin('user__field_user_city','nfuc',
            'nfuc.entity_id = n.uid');
        $query->leftJoin('user__field_user_country','nfucc',
            'nfucc.entity_id = n.uid');
        $query->leftJoin('users_field_data','ufd',
            'ufd.uid = n.uid');
        $query->orderBy('n.created' , 'DESC');


        $query->addField( 'n', 'uid' );
        $query->addField( 'nftc', 'field_testimonial_comment_value' );
        $query->addField( 'nfuc', 'field_user_city_value' );
        $query->addField( 'nfucc', 'field_user_country_value' );
        $query->addField( 'nfo', 'field_occupation_value' );
        $query->addField('ufd', 'name');
        $query->addField( 'fki', 'user_picture_target_id','image_id' );
//        $query->range(0,3);

//        $query = $query->extend( 'Drupal\Core\Database\Query\PagerSelectExtender' )->limit( 3 );





        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ( $results as $result ) {

            if( isset($result->image_id)){
                $file = File::load($result->image_id);
                $url = \Drupal\image\Entity\ImageStyle::load('thumbnail')->buildUrl($file->getFileUri());
            } else {
                $field = \Drupal\field\Entity\FieldConfig::loadByName('user', 'user', 'user_picture');
                $default_image = $field->getSetting('default_image');
                $query = \Drupal::database()->select( 'file_managed', 'f' );
                $query->condition('uuid', $default_image['uuid'],'=' );
                $query->addField('f', 'uri' );
                $image = $query->execute()->fetchField();
                if( !empty($image)){
                    $url = \Drupal\image\Entity\ImageStyle::load('thumbnail')->buildUrl($image);
//                    $url = file_create_url($image);
                }
            }


            $bool = true;
//            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$result->nid);
            $data[] = [
                'uid'       => $result->uid,
//                'title'     => $result->title,
                'country'   => $result->field_user_country_value,
                'city'     => $result->field_user_city_value,
                'testimonial_comment' => $result->field_testimonial_comment_value,
                'occupation'     => $result->field_occupation_value,
                'name'      => $result->name,
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
            '#theme'    => 'testimonials_block',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }
}
