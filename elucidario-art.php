<?php
/**
 * Plugin Name:  Elucidário.art
 * Plugin URI:   https://emaklabin.org.br/explore
 * Description:  Visualização da Coleção Ema Klabin
 * Version:      0.29
 * Author:       hgodinho
 * Author URI:   https://hgodinho.com/
 * Text Domain:  eludidario-art-plugin
 * GitHub Plugin URI: https://github.com/hgodinho/wiki-ema
 */

/**
 * Requires
 */
require_once dirname(__FILE__) . '/lib/class-tgm-plugin-activation.php';
require_once dirname(__FILE__) . '/acf/acf.php';

/**
 * Constantes
 */

const PLUGIN_VERSION = "0.29";
const PLUGIN_NAME = "Elucidário.art";
const PLUGIN_URI = "elucidario-art";
const TEXT_DOMAIN = "eludidario-art-plugin";

/**
 * Classe principal
 *
 * Cria custom-post-types, custom-taxonomies, invoca os plugins requeridos e mais...
 */
class Elucidario_Art_Emak
{
    private static $instance;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Construtor do Wordpress
     */
    private function __construct()
    {
        /**
         *
         * @version 0.8
         * action reativada para versão `0.8` para que seja possível importar
         * o meta-box na ativação do plugin.
         *
         * */
        add_action('tgmpa_register', array($this, 'elucidario_art_check_required_plugins'));

        /**
         * adiciona menu item para organização no admin
         */
        add_action('admin_menu', array($this, 'elucidario_art_custom_menu_admin_page'));

        /** adiciona as actions ds post-types e das taxonomies */
        add_action('init', 'Elucidario_Art_Emak::elucidario_art_register_post_type');
        add_action('init', 'Elucidario_Art_Emak::elucidario_art_register_taxonomies');

        /**
         * Arruma admin columns
         */
        add_action('manage_obras_posts_custom_column', array($this, 'elucidario_art_custom_columns'));
        add_filter('manage_edit-obras_columns', array($this, 'elucidario_art_obras_columns'));
        add_filter('manage_edit-obras_sortable_columns', array($this, 'elucidario_art_obras_sortable_columns'));

        /**
         * @version 0.8
         * filtros reativado para versão `0.8` para que seja possível clonar os campos.
         * não é possível clonar os campos no ACF sem que se tenha a versão premium ( 25 USD )
         * */
        add_filter('rwmb_meta_boxes', array($this, 'elucidario_art_obra_metabox'));
        add_filter('rwmb_meta_boxes', array($this, 'elucidario_art_autor_metabox'));

        /**
         * Cria relações usando meta-box
         */
        add_action('mb_relationships_init', array($this, 'elucidario_art_cria_relacoes'));

        /**
         * Configuração do ACF
         *
         * @since 0.7
         *
         */
        add_filter('acf/settings/path', array($this, 'elucidario_art_acf_settings_path'));
        //add_filter('acf/settings/dir', array($this, 'elucidario_art_acf_settings_dir'));
        //add_filter('acf/settings/show_admin', '__return_false');
        add_filter('acf/settings/save_json', array($this, 'elucidario_art_acf_json_save_point'));
        add_filter('acf/settings/load_json', array($this, 'elucidario_art_acf_json_load_point'));

        /** Cria botão de editar na barra do admin */
        add_action('wp_before_admin_bar_render', array($this, 'elucidario_art_admin_bar_link'));

        //add_action( 'admin_post_elucidario_art_sincroniza_autor_obra', array($this,'elucidario_art_sincroniza_autor_obra' ));
        add_action('wp_ajax_elucidario_art_sincroniza_autor_obra', array($this, 'elucidario_art_sincroniza_autor_obra'));
        add_action('wp_ajax_elucidario_art_atualiza_todos_cpts', array($this, 'elucidario_art_atualiza_todos_cpts'));

        /**
         * enqueue dos scripts ajax do admin
         */
        add_action('admin_enqueue_scripts', array($this, 'elucidario_art_load_scripts'));

        /**
         * Hook para chamar função que executa ações na ativação e desativação do plugin
         * @source https://github.com/hgodinho/wiki-ema/issues/4#issue-408596258
         *
         * @since 0.16
         */
        register_activation_hook(__FILE__, array($this, 'elucidario_art_activacao'));
        register_deactivation_hook(__FILE__, array($this, 'elucidario_art_desativacao'));

        /**
         * Adiciona template
         * @since 0.9
         *
         * Template será usado um tema específico, adaptado do wp-bootstrap-starter
         * @source https://br.wordpress.org/themes/wp-bootstrap-starter/
         *
         * Para fazer a integração será usado o plugin multiple-themes
         * @source https://br.wordpress.org/plugins/jonradio-multiple-themes/
         */

    }

    /**
     * Verifica plugins requeridos
     *
     * *obs: função reativada para versao `0.8`
     */
    public function elucidario_art_check_required_plugins()
    {
        /** Plugins */
        $plugins = array(
            /* Meta-Box */
            array(
                'name' => 'Meta Box',
                'slug' => 'meta-box',
                'required' => true,
                'force_activation' => true,
                'dismissable' => false,
            ),
            /** MB Relationships @since 0.8 */
            array(
                'name' => 'Meta Box Relationships',
                'slug' => 'mb-relationships',
                'required' => true,
                'force_activation' => true,
                'dismissable' => false,
            ),

            /** Plugins recomendados para importação dos dados @since 0.15 */
            array(
                'name' => 'Really Simple CSV Importer',
                'slug' => 'really-simple-csv-importer',
                'required' => false,
                'force_activation' => false,
                'dismissable' => true,
            ),
            array(
                'name' => 'WP Taxonomy Import',
                'slug' => 'wp-taxonomy-import',
                'required' => false,
                'force_activation' => false,
                'dismissable' => true,
            ),
            array(
                'name' => 'ADD From Server',
                'slug' => 'add-from-server',
                'required' => false,
                'force_activation' => false,
                'dismissable' => true,
            ),
            array(
                'name' => 'All-in-One WP Migration',
                'slug' => 'all-in-one-wp-migration',
                'required' => false,
                'force_activation' => false,
                'dismissable' => true,
            ),

            /** plugins recomendados para debug @since 0.15 */
            array(
                'name' => 'Query Monitor',
                'slug' => 'query-monitor',
                'required' => false,
                'force_activation' => false,
                'dismissable' => true,
            ),
            array(
                'name' => 'Really Simple CSV Importer Debugger ADD-ON',
                'source' => 'https://gist.github.com/hissy/7175656/archive/41da06a8450a994377dd34e6022500d2239aa7c6.zip',
                'required' => false,
                'force_activation' => false,
                'dismissable' => true,
            ),

            /** plugins recomendados para exportação dos dados @since 0.15 */
            array(
                'name' => 'WP All Export',
                'slug' => 'wp-all-export',
                'required' => false,
                'force_activation' => false,
                'dismissable' => true,
            ),

            /** Plugin exigidos para a busca */
            array(
                'name' => 'Relevanssi – A Better Search',
                'slug' => 'relevanssi',
                'required' => true,
                'force_activation' => true,
                'dismissable' => false,
            ),
            array(
                'name' => 'ACF: Better Search',
                'slug' => 'acf-better-search',
                'required' => true,
                'force_activation' => true,
                'dismissable' => false,
            ),
        );

        /** Config */
        $config = array(
            'domain' => TEXT_DOMAIN,
            'default_path' => '',
            'parent_slug' => 'plugins.php',
            'capability' => 'update_plugins',
            'menu' => 'install-required-plugins',
            'has_notices' => true,
            'is_automatic' => false,
            'message' => '',
            'strings' => array(
                'page_title' => __('Instalar Plugins Requeridos', TEXT_DOMAIN),
                'menu_title' => __('Instalar Plugins', TEXT_DOMAIN),
                'installing' => __('Instalando Plugins: %s', TEXT_DOMAIN),
                'oops' => __('Alguma coisa deu errado com a API do plugin.', TEXT_DOMAIN),
                'notice_can_install_required' => _n_noop('A ' . PLUGIN_NAME . ' depende do plugin: %1$s.', 'A ' . PLUGIN_NAME . ' depende destes plugins: %1$s.'),
                'notice_cannot_install' => _n_noop('Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.'),
                'notice_can_activate_required' => _n_noop('O seguinte plugin está inativo: %1$s.', 'Os seguintes plugins estão inativos: %1$s.'),
                'notice_cannot_activate' => _n_noop('Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.'),
                'notice_ask_to_update' => _n_noop('The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.'),
                'notice_cannot_update' => _n_noop('Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.'),
                'install_link' => _n_noop('Instalar plugin requerido', 'Instalar plugins requeridos'),
                'activate_link' => _n_noop('Ativar plugin instalado', 'Ativar plugins instalados'),
                'return' => __('Voltar para o instalador de plugins requeridos', TEXT_DOMAIN),
                'plugin_activated' => __('Plugin ativado com sucesso.', TEXT_DOMAIN),
                'complete' => __('Todos os plugins instalados e ativados com sucesso. %s', TEXT_DOMAIN),
                'nag_type' => 'updated',
            ),
        );

        /** Function */
        tgmpa($plugins, $config);
    }

    /**
     * Funções de configurações do ACF
     *
     * chamadas no construtor
     *
     */
    /** 1. customize ACF path */
    public function elucidario_art_acf_settings_path($path)
    {
        $path = plugin_dir_path(__FILE__) . 'acf/';
        return $path;
    }
    public function elucidario_art_acf_settings_dir($dir)
    {
        $dir = plugin_dir_path(__FILE__) . 'acf/';
        return $dir;
    }
    public function elucidario_art_acf_json_save_point($path)
    {
        $path = plugin_dir_path(__FILE__) . 'acf/acf-json';
        return $path;
    }
    public function elucidario_art_acf_json_load_point($paths)
    {
        unset($paths[0]);
        $paths[] = plugin_dir_path(__FILE__) . 'acf/acf-json';
        return $paths;
    }

    /**
     * Registra custom-post types
     *
     * @return void
     */
    public static function elucidario_art_register_post_type()
    {
        /** registra elucidario-art */
        register_post_type(
            'elucidario_art',
            array(
                'labels' => array(
                    'name' => __(PLUGIN_NAME),
                    'singular_name' => __(PLUGIN_NAME),

                    'name_admin_bar' => __(PLUGIN_NAME, 'text_domain'),

                    'attributes' => __('Item Attributes', 'text_domain'),
                    'parent_item_colon' => __('Parent Item:', 'text_domain'),
                    'all_items' => __('Todas as Páginas Especiais', 'text_domain'),
                    'add_new_item' => __('Adicionar Nova Página especial', 'text_domain'),
                    'add_new' => __('Adicionar nova página especial', 'text_domain'),
                    'new_item' => __('Nova Página Especial', 'text_domain'),
                    'edit_item' => __('Editar Página Especial', 'text_domain'),
                    'update_item' => __('Atualizar Página Especial', 'text_domain'),
                    'view_item' => __('Ver Página Especial', 'text_domain'),
                    'view_items' => __('Ver Páginas Especiais', 'text_domain'),
                    'search_items' => __('Pesquisar Página Especial', 'text_domain'),
                    'not_found' => __('Não Encontrada', 'text_domain'),
                    'not_found_in_trash' => __('Nada encontrado na lixeira', 'text_domain'),
                    'featured_image' => __('Imagem destacada', 'text_domain'),
                    'set_featured_image' => __('Inserir imagem destacada', 'text_domain'),
                    'remove_featured_image' => __('Remover imagem destacada', 'text_domain'),
                    'use_featured_image' => __('Usar como imagem destacada', 'text_domain'),
                    'insert_into_item' => __('Insert into item', 'text_domain'),
                    'uploaded_to_this_item' => __('Uploaded to this item', 'text_domain'),
                    'items_list' => __('Items list', 'text_domain'),
                    'items_list_navigation' => __('Items list navigation', 'text_domain'),
                    'filter_items_list' => __('Filter items list', 'text_domain'),
                ),
                'description' => 'Páginas especiais ' . PLUGIN_NAME,
                'supports' => array(
                    'title',
                    'editor',
                    'excerpt',
                    'author',
                    'revisions',
                    'thumbnail',
                    'page-attributes',
                ),
                'public' => true,
                'publicly_queryable' => true,
                'show_in_menu' => false,
                'has_archive' => true,
                'hierarchical' => true,
                'rewrite' => array(
                    'slug' => 'pag',
                    'with_front' => true,
                ),
            )
        );

        /** registra obras */
        register_post_type(
            'obras',
            array(
                'labels' => array(
                    'name' => __('Obras'),
                    'singular_name' => __('Obras'),
                    'menu_name' => __('Obras', 'text_domain'),
                    'name_admin_bar' => __('Obras', 'text_domain'),
                    'archives' => __('Arquivo de Obras', 'text_domain'),
                    'attributes' => __('Item Attributes', 'text_domain'),
                    'parent_item_colon' => __('Parent Item:', 'text_domain'),
                    'all_items' => __('Todas as Obras', 'text_domain'),
                    'add_new_item' => __('Adicionar Nova Obra', 'text_domain'),
                    'add_new' => __('Adicionar nova obra', 'text_domain'),
                    'new_item' => __('Nova Obra', 'text_domain'),
                    'edit_item' => __('Editar Obra', 'text_domain'),
                    'update_item' => __('Atualizar Obra', 'text_domain'),
                    'view_item' => __('Ver Obra', 'text_domain'),
                    'view_items' => __('Ver Obras', 'text_domain'),
                    'search_items' => __('Pesquisar Obra', 'text_domain'),
                    'not_found' => __('Não Encontrada', 'text_domain'),
                    'not_found_in_trash' => __('Nada encontrado na lixeira', 'text_domain'),
                    'featured_image' => __('Imagem destacada', 'text_domain'),
                    'set_featured_image' => __('Inserir imagem destacada', 'text_domain'),
                    'remove_featured_image' => __('Remover imagem destacada', 'text_domain'),
                    'use_featured_image' => __('Usar como imagem destacada', 'text_domain'),
                    'insert_into_item' => __('Insert into item', 'text_domain'),
                    'uploaded_to_this_item' => __('Uploaded to this item', 'text_domain'),
                    'items_list' => __('Items list', 'text_domain'),
                    'items_list_navigation' => __('Items list navigation', 'text_domain'),
                    'filter_items_list' => __('Filter items list', 'text_domain'),

                ),
                'description' => 'Post para cadastro de obras',
                'supports' => array(
                    'title',
                    'author',
                    'revisions',
                    'thumbnail',
                    'comments',
                ),
                'public' => true,
                'publicly_queryable' => true,
                'show_in_menu' => false,
                'has_archive' => true,
                'rewrite' => array(
                    'slug' => 'obras',
                    'with_front' => true,
                ),
            )
        );

        /** registra autores */
        /**
         * @since 0.8
         */
        register_post_type(
            'autores',
            array(
                'labels' => array(
                    'name' => __('Autores'),
                    'singular_name' => __('Autor'),
                    'menu_name' => __('Autores', 'text_domain'),
                    'name_admin_bar' => __('Autores', 'text_domain'),
                    'archives' => __('Arquivo de Autores', 'text_domain'),
                    'attributes' => __('Item Attributes', 'text_domain'),
                    'parent_item_colon' => __('Parent Item:', 'text_domain'),
                    'all_items' => __('Todos os Autores', 'text_domain'),
                    'add_new_item' => __('Adicionar Novo Autor', 'text_domain'),
                    'add_new' => __('Adicionar novo autor', 'text_domain'),
                    'new_item' => __('Novo Autor', 'text_domain'),
                    'edit_item' => __('Editar Autor', 'text_domain'),
                    'update_item' => __('Atualizar Autor', 'text_domain'),
                    'view_item' => __('Ver Autor', 'text_domain'),
                    'view_items' => __('Ver Autores', 'text_domain'),
                    'search_items' => __('Pesquisar Autor', 'text_domain'),
                    'not_found' => __('Não Encontrado', 'text_domain'),
                    'not_found_in_trash' => __('Nada encontrado na lixeira', 'text_domain'),
                    'featured_image' => __('Imagem destacada', 'text_domain'),
                    'set_featured_image' => __('Inserir imagem destacada', 'text_domain'),
                    'remove_featured_image' => __('Remover imagem destacada', 'text_domain'),
                    'use_featured_image' => __('Usar como imagem destacada', 'text_domain'),
                    'insert_into_item' => __('Insert into item', 'text_domain'),
                    'uploaded_to_this_item' => __('Uploaded to this item', 'text_domain'),
                    'items_list' => __('Items list', 'text_domain'),
                    'items_list_navigation' => __('Items list navigation', 'text_domain'),
                    'filter_items_list' => __('Filter items list', 'text_domain'),
                ),
                'description' => 'Post para cadastro de Autores',
                'supports' => array(
                    'title',
                    'revisions',
                    'comments',
                ),
                'public' => true,
                'publicly_queryable' => true,
                'show_in_menu' => false,
                'has_archive' => true,
                'hierarchical' => true,
                'rewrite' => array(
                    'slug' => 'autores',
                    'with_front' => false,
                    'pages' => true,
                ),
            )
        );

    }

    /**
     * Cria colunas personalizadas no admin cpt Obras
     *
     * @param [type] $columns
     * @return void
     */
    public static function elucidario_art_obras_columns($columns)
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'thumbnail' => 'Imagem',
            'title' => 'Título',
            'az' => 'a-z',
            'autor' => 'Autor',
            'tombo' => 'Tombo',
            'datacao' => 'Data',
        );
        return $columns;
    }

    /**
     * Faz com que as colunas personalizadas sejam classificaveis
     *
     * @param [type] $columns
     * @return void
     */
    public static function elucidario_art_obras_sortable_columns($columns)
    {
        $columns = array(
            'title' => 'Título',
            'az' => 'a-z',
            'tombo' => 'Tombo',
            'autor' => 'Autor',
        );
        return $columns;
    }

    /**
     * Gera o conteúdo para as colunas classificaveis
     *
     * @param [type] $column
     * @return void
     */
    public static function elucidario_art_custom_columns($column)
    {
        global $post;
        if ($column == 'thumbnail') {
            the_post_thumbnail('admin-thumbnail');
        } elseif ($column == 'az') {

            $obra_glossary = wp_get_object_terms($post->ID, 'obra_az', array(
                'fields' => 'slugs',
            ));
            //var_dump($obra_glossary);;
            echo $obra_glossary[0];

        } elseif ($column == 'autor') {
            $autor_name = get_post_meta($post->ID, 'ficha_autor', true);
            $autor = get_page_by_title($autor_name, 'OBJECT', 'autores');
            $autor_link = get_edit_post_link($autor->ID);
            echo '<a href="' . $autor_link . '">';
            echo $autor_name;
            echo '</a>';
        } elseif ($column == 'tombo') {
            $tombo = get_field('ficha_tecnica_tombo');
            echo $tombo;
        } elseif ($column == 'datacao') {
            $data = get_field('ficha_tecnica_dataperiodo');
            echo $data;
        }
    }

    /**
     * Registra custom taxonomy
     *
     * @return void
     */
    public static function elucidario_art_register_taxonomies()
    {
        /** registra Classificação para Obras */
        register_taxonomy(
            'classificacao',
            array('Elucidario_Art_Emak'),
            array(
                'labels' => array(
                    'name' => __('Classificação'),
                    'singular_name' => __('Classificação'),
                    'menu_name' => __('Classificação', 'text_domain'),
                    'all_items' => __('Todos as classificações', 'text_domain'),
                    'parent_item' => __('Classificação ascendente', 'text_domain'),
                    'parent_item_colon' => __('Classificação ascendente:', 'text_domain'),
                    'new_item_name' => __('Nova Classificação', 'text_domain'),
                    'add_new_item' => __('Adicione nova Classificação', 'text_domain'),
                    'edit_item' => __('Editar classificação', 'text_domain'),
                    'update_item' => __('Atualizar classificação', 'text_domain'),
                    'view_item' => __('Ver classificação', 'text_domain'),
                    'separate_items_with_commas' => __('Separe as classificações com vírgulas', 'text_domain'),
                    'add_or_remove_items' => __('Adicione ou remova classificações', 'text_domain'),
                    'choose_from_most_used' => __('Escolha das mais usadas', 'text_domain'),
                    'popular_items' => __('Classificações poupulares', 'text_domain'),
                    'search_items' => __('Buscar em Classificação', 'text_domain'),
                    'not_found' => __('Não encontrado', 'text_domain'),
                    'no_terms' => __('Sem classificação', 'text_domain'),
                    'items_list' => __('Lista de Classificações', 'text_domain'),
                ),
                'public' => true,
                'hierarchical' => true,
                'rewrite' => array('slug' => 'classificacao'),
            )
        );
        register_taxonomy_for_object_type('classificacao', 'obras');

        /** registra Núcleos para Obras */
        register_taxonomy(
            'nucleo',
            array('Elucidario_Art_Emak'),
            array(
                'labels' => array(
                    'name' => __('Núcleos'),
                    'singular_name' => __('Núcleo'),
                    'menu_name' => __('Núcleo', 'text_domain'),
                    'all_items' => __('Todos os núcleos', 'text_domain'),
                    'parent_item' => __('Núcleo ascendente', 'text_domain'),
                    'parent_item_colon' => __('Núcleo ascendente:', 'text_domain'),
                    'new_item_name' => __('Novo Núcleo', 'text_domain'),
                    'add_new_item' => __('Adicione novo Núcleo', 'text_domain'),
                    'edit_item' => __('Editar núcleo', 'text_domain'),
                    'update_item' => __('Atualizar núcleo', 'text_domain'),
                    'view_item' => __('Ver núcleo', 'text_domain'),
                    'separate_items_with_commas' => __('Separe os núcleo com vírgulas', 'text_domain'),
                    'add_or_remove_items' => __('Adicione ou remova núcleos', 'text_domain'),
                    'choose_from_most_used' => __('Escolha dos mais usadas', 'text_domain'),
                    'popular_items' => __('Núcleos poupulares', 'text_domain'),
                    'search_items' => __('Buscar em Núcleos', 'text_domain'),
                    'not_found' => __('Não encontrado', 'text_domain'),
                    'no_terms' => __('Sem núcleo', 'text_domain'),
                    'items_list' => __('Lista de Núcleos', 'text_domain'),
                ),
                'public' => true,
                'hierarchical' => true,
                'rewrite' => array('slug' => 'nucleo'),
            )
        );
        register_taxonomy_for_object_type('nucleo', 'obras');

        /** registra Ambientes para Obras */
        register_taxonomy(
            'ambiente',
            array('Elucidario_Art_Emak'),
            array(
                'labels' => array(
                    'name' => __('Ambientes'),
                    'singular_name' => __('Ambiente'),
                    'menu_name' => __('Ambientes', 'text_domain'),
                    'all_items' => __('Todos os ambientes', 'text_domain'),
                    'parent_item' => __('Ambiente ascendente', 'text_domain'),
                    'parent_item_colon' => __('Ambientes ascendente:', 'text_domain'),
                    'new_item_name' => __('Novo Ambiente', 'text_domain'),
                    'add_new_item' => __('Adicione novo Ambiente', 'text_domain'),
                    'edit_item' => __('Editar ambiente', 'text_domain'),
                    'update_item' => __('Atualizar ambiente', 'text_domain'),
                    'view_item' => __('Ver ambiente', 'text_domain'),
                    'separate_items_with_commas' => __('Separe os ambientes com vírgulas', 'text_domain'),
                    'add_or_remove_items' => __('Adicione ou remova ambientes', 'text_domain'),
                    'choose_from_most_used' => __('Escolha dos mais usadas', 'text_domain'),
                    'popular_items' => __('Ambientes poupulares', 'text_domain'),
                    'search_items' => __('Buscar em Ambientes', 'text_domain'),
                    'not_found' => __('Não encontrado', 'text_domain'),
                    'no_terms' => __('Sem ambiente', 'text_domain'),
                    'items_list' => __('Lista de Ambientes', 'text_domain'),
                ),
                'public' => true,
                'hierarchical' => true,
                'rewrite' => array('slug' => 'ambiente'),
            )
        );
        register_taxonomy_for_object_type('ambiente', 'obras');
    }

    /**
     * Registra um custom menu no admin.
     *
     * @since 0.13
     */
    public static function elucidario_art_custom_menu_admin_page()
    {
        $page_title = __(PLUGIN_NAME, TEXT_DOMAIN);
        $menu_title = __(PLUGIN_NAME, TEXT_DOMAIN);
        $capability = 'manage_options';
        $menu_slug = PLUGIN_URI . '/elucidario-art-admin';
        $function = array($this, 'elucidario_art_template_plugin_admin');
        $dashicon = 'dashicons-admin-customizer';
        $position = 3;

        global $elucidario_art_admin;

        $elucidario_art_admin = add_menu_page(
            $page_title,
            $menu_title,
            $capability,
            $menu_slug,
            $function,
            $dashicon,
            $position
        );

        add_submenu_page($menu_slug, 'Páginas Especiais', 'Páginas especiais', 'edit_posts', 'edit.php?post_type=elucidario_art');
        add_submenu_page($menu_slug, 'Obras', 'Obras', 'edit_posts', 'edit.php?post_type=obras');
        add_submenu_page($menu_slug, 'Autores', 'Autores', 'edit_posts', 'edit.php?post_type=autores');
        add_submenu_page($menu_slug, 'Classificação', 'Classificações', 'manage_categories', 'edit-tags.php?taxonomy=classificacao&post_type=obras');
        add_submenu_page($menu_slug, 'Núcleo', 'Núcleos', 'manage_categories', 'edit-tags.php?taxonomy=nucleo&post_type=obras');
        add_submenu_page($menu_slug, 'Ambiente', 'Ambientes', 'manage_categories', 'edit-tags.php?taxonomy=ambiente&post_type=obras');

    }

    /**
     * Chama o Elucidário.art Admin
     *
     * @return void
     */
    public static function elucidario_art_template_plugin_admin()
    {
        include 'elucidario-art-admin.php';
    }

    /**
     * Realiza a sincronia entre obras e autores programaticamente
     *
     * @since 0.15
     * @version 0.1
     */
    public function elucidario_art_sincroniza_autor_obra()
    {
        if (!isset($_POST['elucidario_art_sync_nonce']) || !wp_verify_nonce($_POST['elucidario_art_sync_nonce'], 'elucidario_art_sync_nonce')) {
            wp_die('Sem autorização');
        }

        if (current_user_can('elucidario_art_capabilities')) {
            $args = array(
                'post_type' => 'obras',
                'posts_per_page' => -1,
            );
            $obras_sync = new WP_Query($args);
            echo '<ol>';
            while ($obras_sync->have_posts()): $obras_sync->the_post();
                //recupera ID dos posts
                $from = get_the_ID();

                //recupera ID dos autores
                $meta_autor = get_post_meta(get_the_ID(), 'ficha_autor');
                $to = get_page_by_title($meta_autor[0], OBJECT, 'autores');

                //verifica se já estão sincronizados, se não realiza a sincronia
                $has_connection = MB_Relationships_API::has($from, $to->ID, 'obras_to_autores');
                if ($has_connection) {
                    echo '<li>' . $from . ' -> ' . $to->ID . ' ok' . '</li>';
                } else {
                    MB_Relationships_API::add($from, $to->ID, 'obras_to_autores');
                    echo '<li>from ' . $from . ' to ' . $to->ID . '</li>';
                }
            endwhile;
            echo '</ol>';
            wp_die();
        } else {
            wp_die('Você não tem permissão para executar essa função.');

        }
    }

    /**
     * Função para atualizar as obras quando importar, não necessitando de atualização manual
     * para que os custom fields acf aparecem no front-end
     *
     * @since 0.15
     * @version 0.1
     */
    public function elucidario_art_atualiza_todos_cpts()
    {
        if (!isset($_POST['elucidario_art_update_nonce']) || !wp_verify_nonce($_POST['elucidario_art_update_nonce'], 'elucidario_art_update_nonce')) {
            wp_die('Sem autorização');
        }
        if (current_user_can('elucidario_art_capabilities')) {
            $obrasargs = array(
                'post_type' => 'obras',
                'posts_per_page' => -1,
            );
            $obras_update = new WP_Query($obrasargs);

            if ($obras_update->have_posts()): echo '<h2>OBRAS:</h2>';
                while ($obras_update->have_posts()): $obras_update->the_post();

                    //var_dump(get_post_meta(get_the_ID(  )));
                    $obra_id = get_the_ID();
                    echo the_title( '<h3>', '</h3>');
                    echo '<ol>';

                    $tombo_in = get_field('ficha_tecnica_tombo');
                    $tombo = get_field_object('field_5bfd4663b4645');
                    $tombo_key = $tombo['key'];
                    $tombo_value = $tombo['value'];
                    //var_dump($tombo_key);

                    $origem_in = get_field('ficha_tecnica_origem');
                    $origem = get_field_object('field_5bfd46adb4646');
                    $origem_key = $origem['key'];
                    $origem_value = $origem['value'];
                    //var_dump($origem_key);

                    $dataperiodo_in = get_field('ficha_tecnica_dataperiodo');
                    $dataperiodo = get_field_object('field_5bfd46cab4647');
                    $dataperiodo_key = $dataperiodo['key'];
                    $dataperiodo_value = $dataperiodo['value'];
                    //var_dump($dataperiodo);

                    $material_in = get_field('ficha_tecnica_material');
                    $material = get_field_object('field_5bfd46fcb4648');
                    $material_key = $material['key'];
                    $material_value = $material['value'];
                    //var_dump($material_key);

                    $dimensoes_in = get_field('ficha_tecnica_dimensoes');
                    $dimensoes = get_field_object('field_5bfd47ebb4649');
                    $dimensoes_key = $dimensoes['key'];
                    $dimensoes_value = $dimensoes['value'];
                    //var_dump($dimensoes_key);

                    $fotografo_in = get_field('fotografo');
                    $fotografo = get_field_object('field_5c0ec52b96602');
                    $fotografo_key = $fotografo['key'];
                    $fotografo_value = $fotografo['value'];
                    //var_dump($fotografo_key);

                    $descricao_in = get_field('descricao');
                    $descricao = get_field_object('field_5bfdeeb084777');
                    $descricao_key = $descricao['key'];
                    $descricao_value = $descricao['value'];
                    //var_dump($descricao_key);

                    if ($tombo_in != $tombo_value): update_field($tombo_key, $tombo_in);
                    elseif ($origem_in != $origem_value): update_field($origem_key, $origem_in);
                    elseif ($dataperiodo_in != $dataperiodo_value): update_field($dataperiodo_key, $dataperiodo_in);
                    elseif ($material_in != $material_value): update_field($material_key, $material_in);
                    elseif ($dimensoes_in != $dimensoes_value): update_field($dimensoes_key, $dimensoes_in);
                    elseif ($fotografo_in != $fotografo_value): update_field($fotografo_key, $fotografo_in);
                    elseif ($descricao_in != $descricao_value): update_field($descricao_key, $descricao_in);
                    endif;

                    //wp_update_post(array('ID' => $obra_id), true);

                    echo '<li>id: ' . $obra_id . ' -> ' . $tombo['value'] . ' = ' . $tombo_in . '</li>';
                    echo '<li>id: ' . $obra_id . ' -> ' . $origem['value'] . ' = ' . $origem_in . '</li>';
                    echo '<li>id: ' . $obra_id . ' -> ' . $dataperiodo['value'] . ' = ' . $dataperiodo_in . '</li>';
                    echo '<li>id: ' . $obra_id . ' -> ' . $material['value'] . ' = ' . $material_in . '</li>';
                    echo '<li>id: ' . $obra_id . ' -> ' . $dimensoes['value'] . ' = ' . $dimensoes_in . '</li>';
                    echo '<li>id: ' . $obra_id . ' -> ' . $fotografo['value'] . ' = ' . $fotografo_in . '</li>';
                    echo '<li>id: ' . $obra_id . ' -> ' . $descricao['value'] . ' = ' . $descricao_in . '</li>';

                    echo '</ol>';

                endwhile;
            endif;

            wp_reset_query();

            $autoresargs = array(
                'post_type' => 'autores',
                'posts_per_page' => -1,
            );
            $autores_update = new WP_Query($autoresargs);

            if ($autores_update->have_posts()): echo '<h2>AUTORES:</h2>';
                while ($autores_update->have_posts()): $autores_update->the_post();
                    
                    $autor_id = get_the_ID();
                    echo the_title( '<h3>', '</h3>');
                    echo '<ol>'; 

                    $datainicio_in = get_field('dataperiodo_inicial');
                    $datainicio = get_field_object('field_5bfdfe04be13f');
                    $datainicio_key = $datainicio['key'];
                    $datainicio_value = $datainicio['value'];
                    //var_dump($datainicio_value);

                    $datafinal_in = get_field('dataperiodo_final');
                    $datafinal = get_field_object('field_5bfdfe43be140');
                    $datafinal_key = $datafinal['key'];
                    $datafinal_value = $datafinal['value'];
                    //var_dump($datafinal);

                    $descricao_in = get_field('descricao');
                    $descricao = get_field_object('field_5bfdfe72be142');
                    $descricao_key = $descricao['key'];
                    $descricao_value = $descricao['value'];
                    //var_dump($descricao);

                    if ($datainicio_in != $datainicio_value): update_field($datainicio_key, $datainicio_in);
                    elseif ($datafinal_in != $datafinal_value): update_field($datafinal_key, $datafinal_in);
                    elseif ($descricao_in != $descricao_value): update_field($descricao_key, $descricao_in);
                    endif;

                    wp_update_post(array('ID' => $autor_id), true);

                    if (is_wp_error($autor_id)) {
                        $errors = $autor_id->get_error_messages();
                        foreach ($errors as $error) {
                            echo $error;
                        }
                    }

                    echo '<li>id: ' . $autor_id . ' -> ' . $datainicio['value'] . ' = ' . $datainicio_in . '</li>';
                    echo '<li>id: ' . $autor_id . ' -> ' . $datafinal['value'] . ' = ' . $datafinal_in . '</li>';
                    echo '<li>id: ' . $autor_id . ' -> ' . $descricao['value'] . ' = ' . $descricao_in . '</li>';

                    echo '</ol>';
                endwhile;
            endif;

            wp_reset_query();

            wp_die('concluído');
        } else {
            wp_die('Você não tem permissão para executar essa função.');

        }
    }

    /**
     * Load dos scripts para funcionamento adequado do plugin elucidario-art
     *
     * @param [type] $hook
     * @return void
     */
    public function elucidario_art_load_scripts($hook)
    {
        global $elucidario_art_admin;

        if ($hook != $elucidario_art_admin) {
            return;
        }

        $version = rand(0, 999);
        wp_enqueue_script('elucidario_art_admin', plugin_dir_url(__FILE__) . 'js/elucidario-art-admin.js', 'jquery', $version, true);
        wp_localize_script('elucidario_art_admin', 'elucidario_art_sync_vars', array(
            'elucidario_art_sync_nonce' => wp_create_nonce('elucidario_art_sync_nonce'),
        ));
        wp_localize_script('elucidario_art_admin', 'elucidario_art_update_vars', array(
            'elucidario_art_update_nonce' => wp_create_nonce('elucidario_art_update_nonce'),
        ));

        wp_register_style('elucidario_art_styles', plugins_url('elucidario-art/css/styles.css'));
        wp_enqueue_style('elucidario_art_styles');

    }

    /**
     * Cria metaboxes com Meta-box plugin
     *
     * @version 0.8 função volta para o plugin, mas de maneira diferente.
     * criando somente os campos de referências, ligações externas e exposições
     * isso porque o acf não permite clonar campos sem que se tenha o plugin premium
     *
     * @return $meta-boxes
     */
    /** Obra metabox */
    public function elucidario_art_obra_metabox($meta_boxes)
    {
        $prefix = 'obra-metabox_';
        $meta_boxes[] =
        array(
            'id' => 'mb-obras_',
            'title' => esc_html__('Links', 'metabox-emak'),
            'post_types' => array('obras'),
            'context' => 'normal',
            'priority' => 'high',
            'autosave' => 'true',
            'fields' => array(

                //referencia
                array(
                    'id' => $prefix . 'referencias',
                    'type' => 'fieldset_text',
                    'name' => esc_html__('Referências', 'metabox-emak'),
                    'desc' => esc_html__('Referência:', 'metabox-emak'),
                    'options' => array(
                        'titulo' => 'Título',
                        'url' => 'URL',
                        'data-de-consulta' => 'Data de consulta',
                    ),
                    'clone' => true,
                    'sort_clone' => true,
                ),

                //ligacoes externas
                array(
                    'type' => 'divider',
                ),
                array(
                    'id' => $prefix . 'externo',
                    'type' => 'fieldset_text',
                    'name' => esc_html__('Ligações Externas', 'metabox-emak'),
                    'desc' => esc_html__('Citação:', 'metabox-emak'),
                    'options' => array(
                        'titulo' => 'Título',
                        'autor' => 'Autor',
                        'ano' => 'Ano',
                        'url' => 'URL',
                    ),
                    'clone' => true,
                    'sort_clone' => true,
                ),

                //exposições
                array(
                    'type' => 'divider',
                ),
                array(
                    'id' => $prefix . 'exposicoes',
                    'type' => 'fieldset_text',
                    'name' => esc_html__('Exposições', 'metabox-emak'),
                    'desc' => esc_html__('Exposição que participou:', 'metabox-emak'),
                    'options' => array(
                        'titulo' => 'Título',
                        'local' => 'local',
                        'ano' => 'ano',
                        'url' => 'URL',
                    ),
                    'clone' => true,
                    'sort_clone' => true,
                ),
            ),
        );
        return $meta_boxes;
    }
    /** Autor metabox */
    public function elucidario_art_autor_metabox($meta_boxes)
    {
        $prefix = 'autor-metabox_';
        $meta_boxes[] =
        array(
            'id' => 'mb-autor',
            'title' => esc_html__('Links', 'metabox-emak'),
            'post_types' => array('autores'),
            'context' => 'normal',
            'priority' => 'high',
            'autosave' => 'true',
            'fields' => array(
                //referencia
                array(
                    'id' => $prefix . 'referencias',
                    'type' => 'fieldset_text',
                    'name' => esc_html__('Referências', 'metabox-emak'),
                    'desc' => esc_html__('Referência:', 'metabox-emak'),
                    'options' => array(
                        'titulo' => 'Título',
                        'url' => 'URL',
                        'data-de-consulta' => 'Data de consulta',
                    ),
                    'clone' => true,
                    'sort_clone' => true,
                ),

                //ligacoes externas
                array(
                    'type' => 'divider',
                ),
                array(
                    'id' => $prefix . 'externo',
                    'type' => 'fieldset_text',
                    'name' => esc_html__('Ligações Externas', 'metabox-emak'),
                    'desc' => esc_html__('Citação:', 'metabox-emak'),
                    'options' => array(
                        'titulo' => 'Título',
                        'autor' => 'Autor',
                        'ano' => 'Ano',
                        'url' => 'URL',
                    ),
                    'clone' => true,
                    'sort_clone' => true,
                ),
            ),
        );
        return $meta_boxes;
    }

    /**
     * Cria relações com MB_Relationships_API
     *
     * @since 0.8
     */
    public function elucidario_art_cria_relacoes()
    {
        MB_Relationships_API::register(
            array(
                'id' => 'obras_to_autores',
                'from' => array(
                    'object_type' => 'post',
                    'post_type' => 'obras',
                    'admin_column' => true,
                    'meta_box' => array(
                        'title' => 'Autoria da obra',
                        'context' => 'advanced',
                    ),
                ),
                'to' => array(
                    'object_type' => 'post',
                    'post_type' => 'autores',
                    'meta_box' => array(
                        'title' => 'Obras na coleção',
                        'context' => 'advanced',
                    ),
                ),
            )
        );

        MB_Relationships_API::register(
            array(
                'id' => 'obra_em_destaque',
                'from' => array(
                    'object_type' => 'post',
                    'post_type' => 'autores',
                    'meta_box' => array(
                        'title' => 'Obra em Destaque',
                        'context' => 'advanced',
                    ),
                ),
                'to' => array(
                    'object_type' => 'post',
                    'post_type' => 'obras',
                    'meta_box' => array(
                        'title' => 'Obra em destaque de',
                        'context' => 'advanced',
                    ),
                ),
            )
        );
    }

    /**
     * Insere botão de editar custom posts na admin bar
     *
     * @since 0.14
     * @return void
     */
    public function elucidario_art_admin_bar_link()
    {
        global $wp_admin_bar;
        global $post;
        if (!is_super_admin() || !is_admin_bar_showing()) {
            return;
        }

        if (is_single()) {
            $wp_admin_bar->add_menu(array(
                'id' => 'edit_fixed',
                'parent' => false,
                'title' => __('Editar'),
                'href' => get_edit_post_link($post->id),
            ));
        }

    }

    /**
     * faz a validação se as páginas existem ou não
     * @return boolean
     */
    public function elucidario_art_the_slug_exists($post_name, $post_type)
    {
        global $wpdb;
        if ($wpdb->get_row("SELECT post_name FROM wp_posts WHERE post_name = '" . $post_name . "' AND post_type = '" . $post_type . "'", 'ARRAY_A')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Cria páginas especiais
     *
     * Função chamada no hook de ativação
     *
     * @return void
     */
    public function elucidario_art_cria_paginas()
    {
        /**
         * Verifica se Ambientes exitem nas páginas especiais do CPT elucidario_art, se não existir
         * cria a página.
         */
        $current_user = wp_get_current_user();

        /**
         * Cria página Ambientes
         */
        if (!$this->elucidario_art_the_slug_exists('ambientes', 'elucidario_art')) {
            $pag_ambientes = array(
                'post_title' => 'Ambientes',
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => $current_user->ID,
                'post_type' => 'elucidario_art',
            );
            wp_insert_post($pag_ambientes);
        }

        /**
         * Cria página Classificações
         */
        if (!$this->elucidario_art_the_slug_exists('classificacoes', 'elucidario_art')) {
            $pag_classificacoes = array(
                'post_title' => 'Classificações',
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => $current_user->ID,
                'post_type' => 'elucidario_art',
            );
            wp_insert_post($pag_classificacoes);
        }

        /**
         * Cria páginas Núcleos
         */
        if (!$this->elucidario_art_the_slug_exists('nucleos', 'elucidario_art')) {
            echo 'criando nova página.';
            $pag_nucleos = array(
                'post_title' => 'Núcleos',
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => $current_user->ID,
                'post_type' => 'elucidario_art',
            );
            wp_insert_post($pag_nucleos);
        }

        /**
         * Cria página Ema-Klabin
         */
        if (!$this->elucidario_art_the_slug_exists('ema-klabin', 'elucidario_art')) {
            $pag_emaklabin = array(
                'post_title' => 'Ema Klabin',
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => $current_user->ID,
                'post_type' => 'elucidario_art',
            );
            wp_insert_post($pag_emaklabin);
        }
    }

    /**
     * Deleta Páginas na desativação do plugin
     */
    public function elucidario_art_deleta_paginas()
    {
        if ($this->elucidario_art_the_slug_exists('ambientes', 'elucidario_art')) {
            $pag_ambientes_rmv = get_page_by_path('ambientes', 'OBJECT', 'elucidario_art');
            wp_delete_post($pag_ambientes_rmv->ID, true);
        }
        if ($this->elucidario_art_the_slug_exists('classificacoes', 'elucidario_art')) {
            $pag_classificacoes_rmv = get_page_by_path('classificacoes', 'OBJECT', 'elucidario_art');
            wp_delete_post($pag_classificacoes_rmv->ID, true);
        }
        if ($this->elucidario_art_the_slug_exists('nucleos', 'elucidario_art')) {
            $pag_nucleos_rmv = get_page_by_path('nucleos', 'OBJECT', 'elucidario_art');
            wp_delete_post($pag_nucleos_rmv->ID, true);
        }
        if ($this->elucidario_art_the_slug_exists('ema-klabin', 'elucidario_art')) {
            $pag_emaklabin_rmv = get_page_by_path('ema-klabin', 'OBJECT', 'elucidario_art');
            wp_delete_post($pag_emaklabin_rmv->ID, true);
        }
    }

    /**
     * Registra novas capacidades aos usuários
     * editor e administrador
     *
     * @return void
     */
    public function elucidario_art_registra_capacidades()
    {
        $editor = get_role('editor');
        $admin = get_role('administrator');
        $editor->add_cap('elucidario_art_capabilities');
        $admin->add_cap('elucidario_art_capabilities');
    }

    /**
     * Deleta as capacidades dos usuários
     *
     * editor e administrador
     *
     * @return void
     */
    public function elucidario_art_deleta_capacidades()
    {
        $editor = get_role('editor');
        $admin = get_role('administrator');
        $editor->remove_cap('elucidario_art_capabilities');
        $admin->remove_cap('elucidario_art_capabilities');
    }

    /**
     * Função chamada na ativação do plugin
     *
     * @return void
     */
    public function elucidario_art_activacao()
    {
        /**
         * Função para Criar Páginas na ativação do plugin
         */
        self::elucidario_art_cria_paginas();

        /**
         * Função para registrar as capacidades nos usuários
         */
        self::elucidario_art_registra_capacidades();

        /**
         * Função para registrar os CPTs
         */
        self::elucidario_art_register_post_type();

        /**
         * Função para registrar as taxonomias
         */
        self::elucidario_art_register_taxonomies();
        
        flush_rewrite_rules();
    }

    /**
     * Função chamada na desativação do plugin
     *
     * @return void
     */
    public function elucidario_art_desativacao()
    {

        /**
         * Função para Deleta Páginas na desativação do plugin
         */
        self::elucidario_art_deleta_paginas();

        /**
         * Função para deletar capacidades dos usuários na desativação do plugin
         */
        self::elucidario_art_deleta_capacidades();

        /**
         * rewrite
         */
        flush_rewrite_rules();
    }
}

/**
 * instancias
 */
Elucidario_Art_Emak::getInstance();
