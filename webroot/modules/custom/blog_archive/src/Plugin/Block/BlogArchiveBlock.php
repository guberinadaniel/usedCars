<?php
/**
 * Created by PhpStorm.
 * User: stefan
 * Date: 6.12.17.
 * Time: 11.14
 */

namespace Drupal\blog_archive\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a custom block
 *
 * @Block(
 *   id = "blog_archive",
 *   admin_label = @Translation("Blog Archive block"),
 *   category = @Translation("Custom block"),
 * )
 */
class BlogArchiveBlock extends BlockBase
{
    public function buildContent()
    {

        $query = \Drupal::database()->select( 'node_field_data', 'n' );
        $query->condition( 'n.type', 'blog', '=' );

//        $query->addExpression('COUNT(n.created)', 'count');
//        $query->groupBy('nid');
        $query->addExpression('n.created', 'date');
        $query->addExpression('n.nid', 'nid');


//        $query->orderBy( 'created', 'DESC' );

        $results    = $query->execute()->fetchAll();

        $output = [];

        foreach ( $results as $result ) {

//            $timestamp = strtotime($result->date);
            $date = date('F Y');

            $output[$date]['date']= $date;
            if(!isset($output[$date]['count'])) {
                $output[$date]['count'] = 0;
            }
            if(empty($output[$date]['url'])){
                $output[$date]['url'] = '?ids=';
            }
            $output[$date]['count']++;
            $output[$date]['url'] .= $result->nid.',';
        }
        $output = array_values($output);

        return $output;
    }

    public function build()
    {

        return array(
            '#theme' => 'blog_archive',
            '#content' => $this->buildContent(),
            '#cache' => [
                'max-age' => 0,
            ],
        );
    }
}