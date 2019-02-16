<?php
/**
 * Página de administração do plugin.
 *
 * @since 0.13
 * @version 0.1
 */

//sincroniza_autor_obra();
//atualiza_todos_as_obras() ;

/**
 * Função para atualizar as obras quando importar, não necessitando de atualização manual
 * para que os custom fields acf aparecem no front-end
 *
 * @since 0.15
 * @version 0.1
 */
function atualiza_todos_as_obras()
{
    //global $wpdb;
    echo '<h2>atualiza posts</h2>';

    /*
    $args = array(
        'post_type' => 'obras',
        'posts_per_page' => 10,
    );
    $obras_update = new WP_Query($args);
    //print_r($obras_update);
    while ($obras_update->have_posts()): $obras_update->the_post();
        $tombo = get_field('ficha_tecnica_tombo');
        $obra_id = get_the_ID();
        echo 'id: ' . $obra_id . '   tombo: ' . $tombo . '<br>';
        update_field('field_5bfd4663b4645', $tombo, $obra_id);
        wp_update_post(array('ID' => $obra_id));
    endwhile;
    */
    wp_die();
}

/**
 * Realiza a sincronia entre obras e autores programaticamente
 *
 * @since 0.15
 * @version 0.1
 */
function sincroniza_autor_obra()
{
    echo '<h2>obras -> autor</h2>';
    $args = array(
        'post_type' => 'obras',
        'posts_per_page' => -1,
    );
    $obras_sync = new WP_Query($args);

    while ($obras_sync->have_posts()): $obras_sync->the_post();
        //recupera ID dos posts
        $from = get_the_ID();

        //recupera ID dos autores
        $meta_autor = get_post_meta(get_the_ID(), 'ficha_autor');
        $to = get_page_by_title($meta_autor[0], OBJECT, 'autores');

        //verifica se já estão sincronizados, se não realiza a sincronia
        $has_connection = MB_Relationships_API::has($from, $to->ID, 'obras_to_autores');
        if ($has_connection) {
            //echo $from . ' -> ' . $to->ID . ' ok';
        } else {
            MB_Relationships_API::add($from, $to->ID, 'obras_to_autores');
            echo 'from ' . $from . ' to ' . $to->ID;
            echo '<br>';
        }
    endwhile;
}
?>


<div class="wrap">
    <h1>Wiki-Ema</h1>
    <form id="wiki-ema-form" action="" method="POST">
        <input type="submit" name="wiki-ema-submit" class="button-primary sincroniza" value="Sincroniza Autores e Obras">
    </form>
</div>


<?php
add_action('wp_ajax_sincroniza_autor_obra', 'sincroniza_autor_obra');
