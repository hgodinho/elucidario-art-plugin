# Acervo
visualização do acervo Ema Klabin
versão `0.8`

# Changelog
[keep a changelog](https://keepachangelog.com/en/1.0.0/)

## `0.8`
## Changed
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

