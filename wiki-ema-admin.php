<?php
/**
 * Página de administração do plugin.
 *
 * @since 0.13
 * @version 0.1
 */
?>
<h1>Wiki-Ema</h1>
<div class="wrap">
<?php

echo '<h2>atualiza posts</h2>';

$args = array(
    'post_type' => 'obras',
    'posts_per_page' => 10,
);

$obras_update = new WP_Query($args);

while ($obras_update->have_posts()): $obras_update->the_post();
    $tombo = get_field('ficha_tecnica_tombo');
    $obra_id = get_the_ID();
    echo 'id: ' . $obra_id . '   tombo: ' . $tombo . '<br>';
    $obra = array(
        'ID' => $obra_id,
        'ficha_tecnica_tombo' => $tombo
    );
    wp_update_post($obra);
endwhile;

/**
 * Realiza a sincronia entre obras e autores programaticamente
 *
 * @since 0.15
 * @version 0.1
 */
/*
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
 */

?>
</div>


