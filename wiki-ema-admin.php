<?php
/**
 * Página de administração do plugin.
 * 
 * @since 0.13
 * @version 0.1
 */ 

echo '<h1>Wiki-Ema</h1>';

//submit_button('Sincronizar obras com autores');
/**
 * Realiza a sincronia entre obras e autores programaticamente
 * 
 * @since 0.15
 * @version 0.1
 */
echo '<h2>obras -> autor</h2>';
$args = array(
    'post_type' => 'obras',
    'posts_per_page' => -1
);
$obras_sync = new WP_Query( $args );


while ($obras_sync->have_posts()): $obras_sync->the_post();

//recupera ID dos posts
$from = get_the_ID();

//recupera ID dos autores
$meta_autor = get_post_meta( get_the_ID(), 'ficha_autor' );
$to = get_page_by_title( $meta_autor[0], OBJECT, 'autores' );

//verifica se já estão sincronizados, se não realiza a sincronia
$has_connection = MB_Relationships_API::has( $from, $to->ID, 'obras_to_autores');
if ( $has_connection ) {
   echo $from . ' -> ' . $to->ID . ' ok';
} else {
    MB_Relationships_API::add( $from, $to->ID, 'obras_to_autores' );
    echo 'from '. $from . ' to ' . $to->ID;
}

echo '<br>';

endwhile;

?>