<?php

namespace Drupal\blog_list\Plugin\Block;
use Drupal\file\Entity\File;
use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'BlogList' block.
 *
 * @Block(
 *  id = "blog_list",
 *  admin_label = @Translation("Blog list"),
 * )
 */

class BlogList extends BlockBase {

    public function buildContent () {

        $query = \Drupal::database()->select( 'node_field_data', 'n' );
        $query->condition( 'n.type', 'blog', '=' );

        $query->distinct();



        $query->innerJoin('node__field_blog_category', 'fbc',
            'fbc.entity_id = n.nid' );
        $query->innerJoin('taxonomy_term_field_data', 'tfd',
                'tfd.tid = fbc.field_blog_category_target_id' );





        $query->leftJoin('node__field_blog_image','fki',
            'fki.entity_id = n.nid' );
        $query->condition('fki.delta', 0, '=' );

        $query->innerJoin('node__field_blog_text','fbt',
            'fbt.entity_id = n.nid');
        $query->innerJoin('users_field_data','ufd',
            'ufd.uid = n.uid');

        $query->orderBy('n.created' , 'DESC');

        $query->addField( 'n', 'title' );
        $query->addField( 'n', 'status' );
        $query->addField( 'n', 'nid' );
        $query->addField( 'n','changed' );
        $query->addField('ufd', 'name');
        $query->addField( 'fbt', 'field_blog_text_value' );
        $query->addField( 'fki', 'field_blog_image_target_id','image_id' );
        $query->addField( 'tfd', 'name','field_blog_category_target' );
        $query->addField( 'n','changed' );
        $query->addField( 'fbc','field_blog_category_target_id', 'tid');
        $query->range(0,8);

        $query = $query->extend( 'Drupal\Core\Database\Query\PagerSelectExtender' )->limit( 4 );



        $data = [];

//        $form[ 'pager' ] = array(
//            '#type' => 'pager',
//        );

        $results = $query->execute()->fetchAll();

        foreach ( $results as $result ) {
            $file = File::load($result->image_id);
            $url = \Drupal\image\Entity\ImageStyle::load('blog_teaser')->buildUrl($file->getFileUri());
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$result->nid);
            $txalias = \Drupal::service('path.alias_manager')->getAliasByPath('/taxonomy/term/'.$result->tid);
            $data[] = [
                'nid'       => $alias,
                'changed'   => $result->changed,
                'title'     => $result->title,
                'blog_text' => substr(strip_tags($result->field_blog_text_value), 0, 100) . '...',
                'category'  => $result->field_blog_category_target,
                'tx_url'    => $txalias,
                'name'      => $result->name,
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

//        return array(
//            '#theme'    => 'blog_list',
//            '#content'  => $this->buildContent(),
//            '#cache'    => [
//                'max-age' => 0,
//            ],
//        );
        $render = [];
        $render[] = [
            '#theme' => 'blog_list',
            '#content' => $this->buildContent(),
            '#cache' => [
                'max-age' => 0,
            ],
        ];

        $render[] = ['#type' => 'pager'];
        return $render;
    }

}
