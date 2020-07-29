<?php
/**
 * Plugin Name:     Achtvier Block Manager
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     Displays all blocks, really used on the site
 * Author:          84ghz
 * Author URI:      https://84ghz.de
 * Text Domain:     achtvier-block-manager
 * Domain Path:     /languages
 * Version:         0.1
 *
 * @package         Achtvier_Block_Manager
 */

// Your code starts here.


add_action('admin_menu', 'av_block_manager_admin_menu');

function av_block_manager_admin_menu() {
    $av_block_manager_page = add_menu_page( '84GHz Block Manager', '84 GHz Block checker', 'manage_options', 'achtvier-block-manager', 'av_block_manager_admin_page' );
            // Load the JS conditionally
            add_action( 'load-' . $av_block_manager_page, 'av_block_manager_enqueue_scripts' );
}

function achtvier_add_to_blocks_render_array($block, $site_blocks, $post) {
    if(!array_key_exists($block['blockName'], $site_blocks)) {
            $site_blocks[$block['blockName']] = array('<a href ="/wp-admin/post.php?post=' . $post->ID . '&action=edit">' . $post->post_title . '</a>');
           }
           else {
               if (!(in_array('<a href ="/wp-admin/post.php?post=' . $post->ID . '&action=edit">' . $post->post_title . '</a>',$site_blocks[$block['blockName']] ))) {
                   array_push($site_blocks[$block['blockName']],'<a href ="/wp-admin/post.php?post=' . $post->ID . '&action=edit">' . $post->post_title . '</a>' );
               }
           }
    return $site_blocks;       
}


function recursive_get_blocks($blockObject, $site_blocks, $post) {
    $site_blocks = achtvier_add_to_blocks_render_array($blockObject, $site_blocks, $post);
    if (!empty($blockObject['innerBlocks'])) {
        foreach ($blockObject['innerBlocks'] as $innerBlock) {
          $site_blocks = recursive_get_blocks($innerBlock, $site_blocks, $post);

        }
    }
   // d($site_blocks);
    return $site_blocks;
}


function av_block_manager_admin_page() {
    
    $args = [
        'numberposts'   => 100,
        'post_type'     => ['page']
    ];

    $output = "<h1>84GHz Block manager</h1>";

    $postc = get_posts($args);
    $site_blocks = [];
    foreach($postc as $post){

      if ( has_blocks( $post->post_content ) ) {
        $output = "<h1>84GHz Block manager</h1>";
        $blocks = parse_blocks( $post->post_content );
        foreach($blocks as $block)
        {
        
            $beremed = recursive_get_blocks($block, [], $post);  
            $site_blocks = array_replace_recursive($beremed, $site_blocks);   
        }
      }
    }

    //resultate aggregieren
    
    $aggro_array = [];
    foreach ($site_blocks as $blockname => $link) {
        if(!(array_key_exists($link[0], $aggro_array))) {
            $aggro_array[$link[0]] = [$blockname];
        }
        else {
            array_push($aggro_array[$link[0]], $blockname );
        }
    }
    echo ("<h1>84GHz Block manager</h1>");
    echo ("<h2>Verteilung der Blöcke (alle Blöcke, nicht alle Seiten) :</h2>");    
    foreach ($aggro_array as $key => $value) {
        if (!($value == NULL)) {
            echo ('<h3>' . $key . '</h3>' );
            foreach($value as $single_blocknane){            
            echo '<span class="av-block-list-be" data-avblbe="' . $single_blocknane . '">'.$single_blocknane . "</span>, &nbsp;";
          }
        }
        
    }
}





function av_block_manager_enqueue_scripts($hook) {
  wp_enqueue_script( 'av-block-manager', plugin_dir_url( __FILE__ ) . 'js/avbm.js', ['wp-edit-post'] );    
}
