# Elucidário.art
Visualização do acervo Ema Klabin
versão `0.35`

# Changelog

[keep a changelog](https://keepachangelog.com/en/1.0.0/)
[versionamento semântico](https://semver.org/lang/pt-BR/)

## `0.35`
- correção bug método estático

## `0.34`
- show_in_rest true nos post-types

## `0.33`
- atualização na url de autor em todos os arquivos (hgod.in)

## `0.32`
- bug fix na chamada do style.css do admin.php

## `0.31`
- Correção do campo de imagem obra do mês nos custom fields de obra

## `0.30`
- acréscimo de campo para embed do Google Arts&Culture nos custom fields de obras

## `0.29`
- mudança de nome para Eludicidário.art completada.

## `0.28`
- início da mudança de nome para Elucidário.art

## `0.27`
- const PLUGIN_NAME e PLUGIN_URI adicionadas
- All in One WP Migration adicionado como plugin sugerido.

## `0.25`
- ver `0.24`.

## `0.24`
- Remoção da função que criava a página Wiki-Ema na ativação, função não necessário após atualização `0.21`

## `0.23`
- atualização do plugin header no `acervo.php` para compatibilidade com o plugin `GitHub Updater`

## `0.22`
- correção do Slug nos custom post types, após a atualização `0.21` estava gerando erro 404 no arquivo dos cpts

## `0.21`
- remoção do `PLUGIN_SLUG` do tema. Isso foi uma mudança estratégica ao perceber que a instalação da Wiki utilizando o plugin Multiple Themes geraria um monte de problemas de dificil solução, e que também, poluiria o banco de dados do Site com todas as informações do acervo que seriam acrescentadas na importação. Essa mudança estratégica é importante, pois faz com que a instalação da Wiki seja independente do site, uma nova instalação wordpress no servidor bluehost.  

## `0.20`
- https://github.com/hgodinho/wiki-ema/issues/9
- 

## `0.19`
### Added
- 2 plugins adicionados às exigencias de plugin para melhoria do sistema de busca: [Relevanssi](https://br.wordpress.org/plugins/relevanssi/) e [ACF: Better Search](https://br.wordpress.org/plugins/acf-better-search/)

## `0.18`
### added
- Campo "Trecho Descrição" com 280 caracteres para uma breve descrição dos ambientes

***
## `0.17` 
### changed
- wiki_ema_atualiza_todos_cpts() @source https://github.com/hgodinho/wiki-ema/issues/6#issue-408610122

## `0.16`
### added: 
- função que executa ações na ativação do plugin 
- função que executa ações na desativação do plugin
> @see https://github.com/hgodinho/wiki-ema/issues/4#issue-408596258

### changed:
- wp_wiki_obras_sortable_columns
- wp_wiki_custom_columns
- wp_wiki_obras_columns
- melhoria na organização do código

### removed:


***
## `0.15` [2019-02-10]
### added:
- Função que sincroniza as obras com os autores no wiki-ema-admin.php
- Colunas no admin

### deprecated:
- custom taxonomy tipo-autor

***
## `0.14` [2019-01-04]
### added:
- mb-relationships de obra-em-destaque
- cpt wiki_ema que foi removido na 0.13. volta para criar páginas especiais como ambientes, núcleos e classificação. assim gero o template dessas páginas fazendo o query pelas custom taxonomies

### removed:
- depreciados

***
## `0.13` [2019-01-03]
### removed
- cpt wiki-ema

### added
- add_menu_page e add_submenu_page


***
## `0.12 [2019-01-03]
### Changed
- modificações no custom post-type wiki-ema para wiki_ema, dando erro 404 nos singles.

### removed
- depreciados

### added
- deprecated.php

***
## `0.11` [2018-12-31]

### Changed
- `rewrite` nas taxonomias
- `rewrite -> slug` nos custom post type

***
## `0.10` [2018-12-09]

### Added

- `register_post_type` => 'wikiema'
- `add_tax_menus()` para colocar todos os custom_taxonomies no mesmo menu 'Wiki-Ema"
- `'show_in_menu' => 'edit.php?post_type=wiki-ema'` nos CTPs para ficar tudo no mesmo menu "Wiki-Ema"
- nos CPTs
    ```
    'rewrite' => array(
        'slug' => PLUGIN_SLUG . '/autor',    //e '/obra'
        'with_front' => false,
    ),
    ```
- campo fotógrafo no acf_fields obras

### Changed

- paramos de usar o tema-child, iria dificultar a integração dos dois temas. com isso passamos a usar o plugin multiple-themes assim conseguimos adaptar o tema wp-bootstrap-starter



***
## `0.9`
### Added
- optamos por criar o template usando theme-child. ver repositório: https://github.com/hgodinho/wiki-ema-template


***
## `0.8`
### Changed
- mudança no custom-post-type autor. de página para post
> @source https://woorkup.com/beware-the-100-page-wordpress-limitation/
> O wordpress tem um limite de 100 páginas e
> `hierchical => true` gera páginas ao invés de posts

- mudanças nos campos do acf (json)

### Added
- volta do plugin meta-box para criar campos clonáveis
- labels nos custom-post-types
- acréscimo do MB-Relationships na função `check_required_plugins`
- acréscimo da integração com a extensão do Meta-Box (MB-Relationships) para criar relações entre os custom-post-types (obras do autor)
- /csv e itens

### Deprecated
- retirada das metaboxes do acf: referencias, ligacoes externas e exposicoes
- `bidirectional_acf_update_value` removida para criar relações com outro plugin ver seção `added` na mesma versão
- remoção do grupo de campos ACF `autoria-bidirecional`


## `0.7`
### Changed
- Docblocks para melhor visualização e aprendizado do código

### Added
- acf no core do plugin
- configurações do acf

### Deprecated
- check-required-plugin no acf
- metaboxes by MB
- função `check-required-plugins`


## `0.6`
### Changed
- [modificado] [ambiente] de custom post type para custom taxonomy


## `0.5`
- [modificado] meta-boxes
- [`modificado`] custom post types
- [`adicionado`] relações com acf


## `0.4`
- [`modificado`] [`single-obra`] meta-boxes


## `0.3`
- [`adicionado`] [`ambiente`] custom-post


## `0.2`
- cria protótipo


***
#### dependencies:
- [ACF](https://www.advancedcustomfields.com/)
```
