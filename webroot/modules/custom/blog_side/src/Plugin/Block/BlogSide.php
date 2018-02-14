<?php

namespace Drupal\blog_side\Plugin\Block;
use Drupal\file\Entity\File;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'BlogSide' block.
 *
 * @Block(
 *  id = "blog_side",
 *  admin_label = @Translation("Blog side"),
 * )
 */

class BlogSide extends BlockBase {

    public function buildContent () {

        $query = \Drupal::database()->select( 'node_field_data', 'n' );
        $query->condition( 'n.type', 'blog', '=' );

//        $query->distinct();


        $query->leftJoin('node__field_blog_image','fki',
            'fki.entity_id = n.nid' );

        $query->innerJoin('node__field_blog_text','fbt',
            'fbt.entity_id = n.nid');

        $query->orderBy('n.created' , 'DESC');

        $query->addField( 'n', 'title' );
        $query->addField( 'n','created' );
        $query->addField( 'n', 'nid' );
//        $query->addField( 'fbt', 'field_blog_text_value' );
        $query->addField( 'fki', 'field_blog_image_target_id','image_id' );

        $query->range(0,4);

//        $query = $query->extend( 'Drupal\Core\Database\Query\PagerSelectExtender' )->limit( 3 );





        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ( $results as $result ) {
            $file = File::load($result->image_id);
            $url = \Drupal\image\Entity\ImageStyle::load('blog_side_image')->buildUrl($file->getFileUri());
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$result->nid);

            $data[] = [

                'title'     => $result->title,
                'created'  => $result->created,
//                'blog_text' => $result->field_blog_text_value,
                'image' => $url,
                'nid'  => $alias,
            ];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function build () {

        return array(
            '#theme'    => 'blog_side',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }
}
