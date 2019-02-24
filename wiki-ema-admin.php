<?php
/**
 * Página de administração do plugin.
 *
 * @since 0.13
 * @version 0.1
 */
global $wiki_ema_admin;
?>

<?php
?>

<div class="wrap">
    <h2>Wiki-Ema</h2>
    <div class="grid-container">
        <div class="grid-header">
            <h3 class="header">Status</h3>
            <div class="grid-sub-item">
                <h4>Itens:</h4>
            <?php
$total_obras = wp_count_posts('obras', 'readable');
//var_dump($total_obras);
echo '<strong>Publicados:</strong> ' . $total_obras->publish . '.';
echo '<br>';
echo '<strong>Em revisão:</strong> ' . $total_obras->draft . '.';
echo '<br>';

$args = array(
    'post_type' => 'obras',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => '_thumbnail_id',
            'value' => '?',
            'compare' => 'NOT EXISTS',
        ),
    ),
);
$obras_images = new WP_Query($args);
//var_dump($obras_images);
$obras_sem_imagens = $obras_images->found_posts;
if ($obras_sem_imagens > 0) {
    echo '<strong>Itens sem imagem:</strong> ' . $obras_sem_imagens . '. ->';
}

if ($obras_images->have_posts()):
    while ($obras_images->have_posts()): $obras_images->the_post();
        echo '<a href="' . get_edit_post_link( get_the_ID() ) . '">' . the_title('','',false) . '</a>, ';
    endwhile;
else:echo 'Todas apos itens já estão com imagens.';
endif;

wp_reset_query();
?>
            </div>

            <div class="grid-sub-item">
                <h4>Autores:</h4>
            <?php
$total_autores = wp_count_posts('autores', 'readable');
//var_dump($total_obras);
echo '<strong>Publicados:</strong> ' . $total_autores->publish . '.';
echo '<br>';
echo '<strong>Em revisão:</strong> ' . $total_autores->draft . '.';
?>
            </div>
        </div>

        <div class="grid-item">
            <h3>Sincroniza Autores com as Obras</h3>
            <p>Clique no botão abaixo para sincronizar os autores com as obras.
            </p>
            <p>
                Essa ação é necessária ao importar o banco de dados csv.
            </p>
            <form id="wiki-ema-sync-form-admin" method="POST">
                <div>
                    <input type="submit" id="sync_button" name="wiki-ema-sync" class="button-primary" value="Sincroniza Autores e Obras">
                    <span class="spinner" id="spinner_sync" style="float:inherit !important; visibility: unset!important; display: none;"></span>
                </div>
            </form>
            <div id="sync-log"></div>
        </div>

        <div class="grid-item">
            <h3>Atualiza todos os CPTs gerados pelo plugin</h3>
            <p>Clique no botão abaixo para atualizar todos os posts.
            </p>
            <p>
                Essa ação é necessária ao importar o banco de dados csv, pois o acf
                custom-fields não atualiza automaticamente o post, por isso é necessario
                ir manualmente em cada post dos cpts criados para atualizar para poder
                visualizar no template.
            </p>
            <form id="wiki-ema-update-form-admin" method="POST">
                <div>
                    <input type="submit" id="update_button" name="wiki-ema-update" class="button-primary" value="Atualiza todas as Obras e Autores">
                    <span class="spinner" id="spinner_update" style="float:inherit !important; visibility: unset!important; display: none;"></span>
                </div>
            </form>
            <div id="update-log"></div>
        </div>
    </div>
</div>