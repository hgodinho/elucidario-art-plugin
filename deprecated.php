<?php
/** @deprecated 0.8 realações bidirecionais criadas com o MB-Relationships */
//add_filter('acf/update_value/name=autoria_bidirecional', 'bidirectional_acf_update_value', 10, 3);

/** @deprecated 0.7 */
/*                 // tombo
array(
'id' => $prefix . 'tombo',
'type' => 'text',
'name' => esc_html__('Tombo', 'metabox-emak'),
'desc' => esc_html__('número de tombo da obra', 'metabox-emak'),
'placeholder' => esc_html__('M-0000', 'metabox-emak'),
),

//origem
array(
'id' => $prefix . 'origem',
'type' => 'text',
'name' => esc_html__('Origem', 'metabox-emak'),
'desc' => esc_html__('origem da obra', 'metabox-emak'),
'placeholder' => esc_html__('Brasil', 'metabox-emak'),
),

//data
array(
'id' => $prefix . 'data',
'type' => 'text',
'name' => esc_html__('Data/Período', 'metabox-emak'),
'desc' => esc_html__('data ou período da obra', 'metabox-emak'),
'placeholder' => esc_html__('Séc XX', 'metabox-emak'),
),

//material
array(
'id' => $prefix . 'material',
'type' => 'text',
'name' => esc_html__('Material', 'metabox-emak'),
'desc' => esc_html__('material da obra', 'metabox-emak'),
'placeholder' => esc_html__('óleo sobre madeira', 'metabox-emak'),
),

//dimensoes
array(
'id' => $prefix . 'dimensoes',
'type' => 'text',
'name' => esc_html__('Dimensões', 'metabox-emak'),
'desc' => esc_html__('dimensões da obra', 'metabox-emak'),
'placeholder' => esc_html__('25cm X 25cm', 'metabox-emak'),
),

//descricao
//divider
array(
'type' => 'divider',
),
array(
'name' => esc_html__('Descrição', 'metabox-emak'),
'id' => $prefix . 'descricao',
'type' => 'wysiwyg',
'raw' => false,
'options' => array(
'textarea_rows' => 6,
'teeny' => true,
),
),
 */

/** @deprecated 0,7 */
/*                 //data 1
array(
'id' => $prefix . 'data-1',
'type' => 'text',
'name' => esc_html__('Data/Período inicial', 'metabox-emak'),
'desc' => esc_html__('data ou período inicial da obra', 'metabox-emak'),
'placeholder' => esc_html__('Séc XX', 'metabox-emak'),
),

//data 2
array(
'id' => $prefix . 'data-2',
'type' => 'text',
'name' => esc_html__('Data/Período final', 'metabox-emak'),
'desc' => esc_html__('data ou período da final obra', 'metabox-emak'),
'placeholder' => esc_html__('Séc XX', 'metabox-emak'),
),

//descricao
//divider
array(
'type' => 'divider',
),
array(
'name' => esc_html__('Descrição', 'metabox-emak'),
'id' => $prefix . 'descricao',
'type' => 'wysiwyg',
'raw' => false,
'options' => array(
'textarea_rows' => 6,
'teeny' => true,
),
),
 */

/**
 * Adiciona relações bidirecionais
 *
 * vincular a autoria da obra com obras do artista
 *
 * @deprecated 0.8 relações bidirecionais criadas com o MB-relationships
 *
 * @source https://www.advancedcustomfields.com/resources/bidirectional-relationships/
 */
/*
public function bidirectional_acf_update_value($value, $post_id, $field)
{

// vars
$field_name = $field['name'];
$field_key = $field['key'];
$global_name = 'is_updating_' . $field_name;

// bail early if this filter was triggered from the update_field() function called within the loop below
// - this prevents an inifinte loop
if (!empty($GLOBALS[$global_name])) {
return $value;
}

// set global variable to avoid inifite loop
// - could also remove_filter() then add_filter() again, but this is simpler
$GLOBALS[$global_name] = 1;

// loop over selected posts and add this $post_id
if (is_array($value)) {
foreach ($value as $post_id2) {

// load existing related posts
$value2 = get_field($field_name, $post_id2, false);

// allow for selected posts to not contain a value
if (empty($value2)) {
$value2 = array();
}

// bail early if the current $post_id is already found in selected post's $value2
if (in_array($post_id, $value2)) {
continue;
}

// append the current $post_id to the selected post's 'related_posts' value
$value2[] = $post_id;

// update the selected post's value (use field's key for performance)
update_field($field_key, $value2, $post_id2);
}
}

// find posts which have been removed
$old_value = get_field($field_name, $post_id, false);

if (is_array($old_value)) {
foreach ($old_value as $post_id2) {

// bail early if this value has not been removed
if (is_array($value) && in_array($post_id2, $value)) {
continue;
}

// load existing related posts
$value2 = get_field($field_name, $post_id2, false);

// bail early if no value
if (empty($value2)) {
continue;
}

// find the position of $post_id within $value2 so we can remove it
$pos = array_search($post_id, $value2);

// remove
unset($value2[$pos]);

// update the un-selected post's value (use field's key for performance)
update_field($field_key, $value2, $post_id2);
}
}

// reset global varibale to allow this filter to function as per normal
$GLOBALS[$global_name] = 0;

// return
return $value;
}
 */

/**
 * registra tipos de Autores
 *
 *
 * @deprecated 0.15
 * não viram necessidade de diferenciar os autores por tipo,
 * muito difícil de definir, uma vez que muitos autores transitam
 * por diversas linguagens.
 * */
/*
register_taxonomy(
'tipo_autor',
array('Acervo_emak'),
array(
'labels' => array(
'name' => _x('Tipos de Autores', 'Taxonomy General Name', 'Acervo_emak'),
'singular_name' => _x('Tipo de Autor', 'Taxonomy Singular Name', 'Acervo_emak'),
'menu_name' => __('Tipo de Autor', 'Acervo_emak'),
'all_items' => __('Todos os tipos', 'Acervo_emak'),
'parent_item' => __('Tipo ascendente', 'Acervo_emak'),
'parent_item_colon' => __('Tipo ascendente:', 'Acervo_emak'),
'new_item_name' => __('Novo tipo de autor', 'Acervo_emak'),
'add_new_item' => __('Adicionar novo tipo de autor', 'Acervo_emak'),
'edit_item' => __('Editar tipo de autor', 'Acervo_emak'),
'update_item' => __('Atualizar tipo de autor', 'Acervo_emak'),
'view_item' => __('Ver tipo de autor', 'Acervo_emak'),
'separate_items_with_commas' => __('Separe os tipos por vírgulas', 'Acervo_emak'),
'add_or_remove_items' => __('Adicione ou remova tipos de autores', 'Acervo_emak'),
'choose_from_most_used' => __('Escolha dos tipos de autores mais comuns', 'Acervo_emak'),
'popular_items' => __('Tipos de autores mais comuns', 'Acervo_emak'),
'search_items' => __('Procure por tipo de autor', 'Acervo_emak'),
'not_found' => __('Não encontrado', 'Acervo_emak'),
'no_terms' => __('Sem tipo de auto', 'Acervo_emak'),
'items_list' => __('Tipos de autor por lista', 'Acervo_emak'),
'items_list_navigation' => __('Navegação por lista de tipos de autor', 'Acervo_emak'),
),
'public' => true,
'hierarchical' => true,
'show_ui' => true,
'show_admin_column' => true,
'show_in_nav_menus' => true,
'show_tagcloud' => true,
'rewrite' => array('slug' => PLUGIN_SLUG . '/tipo-autor'),
)
);
register_taxonomy_for_object_type('tipo_autor', 'autores');
 */
