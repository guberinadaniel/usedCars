<?php

namespace Drupal\blog_imgs\Plugin\Block;
use Drupal\file\Entity\File;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'BlogImgs' block.
 *
 * @Block(
 *  id = "blog_imgs",
 *  admin_label = @Translation("Blog imgs"),
 * )
 */

class BlogImgs extends BlockBase {

    public function buildContent () {

        $query = \Drupal::database()->select( 'node_field_data', 'n' );
        $query->condition( 'n.type', 'blog', '=' );

//        $query->distinct();


        $query->leftJoin('node__field_blog_image','fki',
            'fki.entity_id = n.nid' );

        $query->leftJoin('node__field_tag','nft',
            'nft.entity_id = n.nid' );
        $query->innerJoin('taxonomy_term_field_data', 'tfd',
            'tfd.tid = nft.field_tag_target_id' );



        $query->orderBy('n.created' , 'DESC');
        $query->addField( 'n', 'nid' );
        $query->addField( 'fki', 'field_blog_image_target_id','image_id' );
        $query->addField( 'tfd', 'name' );
        $query->addField( 'tfd', 'tid' );

        $query->range(0,8);

//        $query = $query->extend( 'Drupal\Core\Database\Query\PagerSelectExtender' )->limit( 3 );





        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ( $results as $result ) {
            $file = File::load($result->image_id);
            $url = \Drupal\image\Entity\ImageStyle::load('blog_side_small')->buildUrl($file->getFileUri());
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$result->nid);
            $txalias = \Drupal::service('path.alias_manager')->getAliasByPath('/taxonomy/term/'.$result->tid);

            $data[] = [
                'nid' => $alias,
                'image' => $url,
                'tag'  => $result->name,
                'tx_url'    => $txalias,
            ];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function build () {

        return array(
            '#theme'    => 'blog_imgs',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }
}
