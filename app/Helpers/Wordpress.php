<?php

namespace Helpers;

/*
 *	WPS CLASS is a class that accesses / handles all the contents of a CMS WordPress
 *	COMPATIBLE WITH WORDPRESS 4.0 OR HIGHER
 *
 *	Possibilities:
 *
 *	1 - Create multiple sites, blogs, disparate systems powered by a single bank / manager;
 *	2 - Use any layout to create a blog / website very quickly without the need to transforms it into a WordPress theme
 *	3 - Create an API and export posts / caregorys / pages / authors / etc in JSON or XML
 *	4 - View wordpress posts into any site / blog / system even without the meso be WordPress
 *	5 - Etc. The possibilities are many, just by imagining and implementing / try.
 *
 * @author Fábio Assunção <fabio@fabioassuncao.com.br>
 * @version 1.2
 * @date July 31, 2015
 */

use Core\Model;
use Helpers\Url;

class Wordpress extends Model {

	//VARIAVEL CONFIGURACOES
	private $configs = "SELECT option_name, option_value FROM wp_options WHERE autoload = 'yes'";

	//VARIAVEL SQL POSTS
	private $posts = "SELECT
		  wp.ID as id,
			wp.post_title as titulo,
			wp.post_content as conteudo,
			wp.post_name as slug,
			wp.post_type as tipo,
			wp.post_date as data_hora_publicacao_us,
			wp.post_modified data_hora_modificacao_us,
			wp.comment_count comentarios,
			wp.post_status as status,
			wt.name as categoria,
			wu.ID as id_autor,
			wu.user_nicename as slug_autor,
			wu.display_name as autor

		FROM

		    wp_terms wt
		        LEFT JOIN
		    wp_term_taxonomy wtt ON wt.term_id = wtt.term_id
		        LEFT JOIN
		    wp_term_relationships wpr ON wpr.term_taxonomy_id = wtt.term_taxonomy_id
		        LEFT JOIN
		    wp_posts wp ON ID = wpr.object_id
				LEFT JOIN

			wp_users wu ON wu.ID = wp.post_author
		WHERE
			%s GROUP BY wp.ID ORDER by wp.post_date DESC %s";

	//VARIAVEL SQL PAGES
	private $pages = "SELECT
		    wp.ID as id,
			wp.post_title as titulo,
			wp.post_content as conteudo,
			wp.post_name as slug,
			wp.post_type as tipo,
			wp.post_date as data_hora_publicacao_us,
			wp.post_modified data_hora_modificacao_us,
			wp.comment_count comentarios,
			wp.post_status as status,
			wu.display_name as autor

		FROM

			wp_posts wp
				LEFT JOIN
			wp_users wu ON wu.ID = wp.post_author
		 WHERE

			%s GROUP BY wp.ID ORDER by wp.post_date DESC %s";

	//VARIAVEL SQL CATEGORIAS
	private $categorys = "SELECT

			wt.name as categoria,
			wt.slug as slug,
			count(wt.name) as total_posts

		FROM

		    wp_terms wt
		        LEFT JOIN
		    wp_term_taxonomy wtt ON wt.term_id = wtt.term_id
		        LEFT JOIN
		    wp_term_relationships wpr ON wpr.term_taxonomy_id = wtt.term_taxonomy_id
		        LEFT JOIN
		    wp_posts wp ON wp.ID = wpr.object_id

		WHERE

		    %s GROUP BY wt.name";

	//VARIAVEL SQL AUTORES
	private $authors = "SELECT

			wu.ID as id,
			wu.display_name as autor,
			wu.user_nicename as nicename,
			wu.user_login as login,
			wu.user_url as url,
			wu.user_email as email,
			wu.user_registered data_registro,

			count(wu.user_nicename) as total_posts

	FROM
	    wp_posts wp
			LEFT JOIN
		wp_users wu ON wu.ID = wp.post_author


	WHERE

		%s

	GROUP BY wu.user_nicename ORDER by wu.display_name";

	//VARIAVEL SQL AUTORES
	private $users = "SELECT
		    wu.ID as id,
		    wu.display_name as autor,
		    wu.user_nicename as nicename,
		    wu.user_login as login,
		    wu.user_url as url,
		    wu.user_email as email,
		    wu.user_registered data_registro

		FROM

		    wp_users wu
				LEFT JOIN
			wp_usermeta wum ON wum.user_id = wu.ID

		WHERE

				%s

		GROUP BY wu.ID
		ORDER by wu.display_name";


	//VARIAVEL SQL COMENTARIOS
	private $comments = "SELECT * FROM wp_comments WHERE comment_post_ID = %s AND comment_approved = %s";

	//VARIAVEL SQL ANEXOS
	private $anexos = "SELECT
			attach.ID as id_anexo,
			attach.guid as src_anexo,
			attach.post_name as nome_anexo,
			attach.post_title as titulo_anexo,
			attach.post_mime_type as tipo_anexo,
			attach.post_date as data_hora_publicacao_us,
			attach.post_modified data_hora_modificacao_us,
			wu.ID as id_autor,
			wu.display_name as autor
		FROM
		    wp_posts wp
				LEFT JOIN
		    wp_posts attach ON wp.ID = attach.post_parent
				LEFT JOIN
			wp_users wu ON wu.ID = wp.post_author
		WHERE
			attach.post_type = 'attachment' %s";

	//VARIAVEL SQL DESTAQUE
	private $destaque = "SELECT
			attach.ID as id_img,
			attach.guid as src_img,
			attach.post_name as nome_img,
			attach.post_title as titulo_img,
			attach.post_mime_type as tipo_img
		FROM
		    wp_posts wp
		    LEFT JOIN
			wp_postmeta wpm ON wpm.post_id = wp.ID
				LEFT JOIN
		    wp_posts attach ON wpm.meta_value = attach.ID
		WHERE
		    wpm.meta_key = '_thumbnail_id' %s";

	public function configs($param = null){

		/*
		 	EXIBE UM ARRAY DE CONFIGURACOES SE NÃO TIVER NENHUM PARAMETRO INFORMADO
		 	SE TIVER UM PARAMETRO INFORMADO MOSTRA APENAS O RESULTADO SOLICITADO (STRING, INTEIRO)

		 	Ex. 1: configs() 	=> retorna um array
		 	Resultado:
						Array
						(
						    [siteurl]				=> http://localhost/wordpress
						    [home] 					=> http://localhost/wordpress
						    [blogname] 				=> Fábio Assunção
						    [blogdescription] 		=> Só mais um site WordPress
						    [users_can_register] 	=> 1
						    [admin_email] 			=> fabio@fabioassuncao.com.br
						    [date_format] 			=> j \d\e F \d\e Y
						    [time_format] 			=> H:i
						    [blog_charset] 			=> UTF-8
						    [WPLANG] 				=> pt_BR
						    ...
						)

			Ex. 2: configs('blogname') 	=> retorna uma string
		 	Resultado: "Fábio Assunção";

			Ex. 3: configs('blog_charset') 	=> retorna uma string
		 	Resultado: "UTF-8";

		 */



		$results = $this->db->select($this->configs, array(), 'fetchAll');

		$dados = array();
		foreach ( (array) $results as $result ) {
			$dados[$result['option_name']] = $this->maybe_unserialize($result['option_value']);
		}

		if(isset($param) && !empty($param)){
			return $dados[$param];
		}
		else{
			return $dados;
		}

	}

	public function postComments($id, $status){

		/*
		 	LISTA TODOS OS COMENTATIOS DE UMA PUBLICACAO

			Ex. 1: postComments(10, 1) 	=> retorna todos os comentários APROVADOS do post com ID 10
			Ex. 2: postComments(10, 0)	=> retorna todos os comentários PENDENTES do post com ID 10
			Ex. 3: postComments(10)		=> retorna TODOS os comentários do post com ID 10

			ID		=> inteiro (ID DO POST)
			STATUS	=> inteiro (1 = aprovado, 0 = pendente, etc)

		 */

		$sql = sprintf($this->comments, $this->filter($id, 'int'), $this->filter($status, 'string'));
		return $this->db->select($sql, array(), 'fetchAll');

	}


	public function imagemDestacada($posts){

		foreach ($posts as $chave => $valor){


			$result = $this->db->select(sprintf($this->destaque, "AND {$this->idPublicacao($valor['id'])}"), array(), 'fetch');

			if(!empty($result)){

				$posts[$chave]['id_destaque'] 				= $result['id_img'];
				$posts[$chave]['src_destaque_full'] 		= $result['src_img'];


				$url = explode('/', $result['src_img']);
				unset($url[8]);
				$url = join('/', $url);


				$thumbs = $this->thumbnails($result['id_img']);

				$posts[$chave]['src_destaque_thumbnail'] 	= $url	.'/'.	$thumbs['thumbnail'];
				$posts[$chave]['src_destaque_medium'] 		= $url	.'/'.	$thumbs['medium'];
				$posts[$chave]['src_destaque_normal'] 		= (!empty($thumbs['post_thumbnail'])) ? $url	.'/'.	$thumbs['post_thumbnail'] : false;

				$posts[$chave]['nome_destaque'] 			= $result['nome_img'];
				$posts[$chave]['titulo_destaque'] 			= $result['titulo_img'];
				$posts[$chave]['tipo_destaque'] 			= $result['tipo_img'];

			} else{

				$posts[$chave]['id_destaque'] 				= false;
				$posts[$chave]['src_destaque_full'] 		= false;

				$posts[$chave]['src_destaque_thumbnail'] 	= false;
				$posts[$chave]['src_destaque_medium'] 		= false;
				$posts[$chave]['src_destaque_normal'] 		= false;

				$posts[$chave]['nome_destaque'] 			= false;
				$posts[$chave]['titulo_destaque'] 			= false;
				$posts[$chave]['tipo_destaque'] 			= false;
			}
		}

		return $posts;
	}

	public function thumbnails($id){

		if($id){

			//EXIBE TODAS AS thumbnails DE UMA IMAGEM DESTACADA
			$result = $this->db->select("SELECT meta_value FROM wp_postmeta WHERE meta_key = '_wp_attachment_metadata' AND post_id = {$this->filter($id, 'int')}", array(), 'fetch');
			$thumbs = $this->maybe_unserialize($result['meta_value']);

			$thumbnail = (isset($thumbs['sizes']['thumbnail']['file'])) ? $thumbs['sizes']['thumbnail']['file'] : '';
			$medium = (isset($thumbs['sizes']['medium']['file'])) ? $thumbs['sizes']['medium']['file'] : '';
			$post_thumbnail = (isset($thumbs['sizes']['post-thumbnail']['file'])) ? $thumbs['sizes']['post-thumbnail']['file'] : '';

			return array(
					'thumbnail' => $thumbnail ,
					'medium' => $medium,
					'post_thumbnail' => $post_thumbnail
			);

		}else{

			return array(
					'thumbnail' => null ,
					'medium' => null,
					'post_thumbnail' => null
			);

		}

	}

	public function anexosPostId($id, $tipo = null){

		//EXIBE TODOS OS ANEXOS DE UMA PUBLICACAO DE ACORDO COM O ID INFORMADO

		if(isset($id) && !empty($id)){

			if($tipo != null){

				return $this->db->select(sprintf($this->anexos, "AND {$this->idPublicacao($id)} AND {$this->tipoAnexo($tipo)}"), array(), 'fetchAll');
			}

			else{

				return $this->db->select(sprintf($this->anexos, "AND {$this->idPublicacao($id)}"), array(), 'fetchAll');

			}

		}

		else{
			return array("ERRO" => "ID nao informado");
		}
	}

	public function anexosPostSlug($slug, $tipo = null){

		//EXIBE TODOS OS ANEXOS DE UMA PUBLICACAO DE ACORDO COM O SLUG INFORMADO

			if(isset($slug) && !empty($$slug)){

				if($tipo != null){

					return $this->db->select(sprintf($this->anexos, "AND {$this->slugPublicacao($slug)} AND {$this->tipoAnexo($tipo)}"), array(), 'fetchAll');
				}

				else{

					return $this->db->select(sprintf($this->anexos, "AND {$this->slugPublicacao($slug)}"), array(), 'fetchAll');

				}

		}

		else{
			return array("ERRO" => "Slug nao informado");
		}
	}

	public function posts($status = null, $quantidade = null, $pagina = null){

		/*

			LISTA TODAS OS POSTS

			Ex. 1: post() 					=> retorna todos os posts
			Ex. 2: post('publish') 			=> retorna todos os posts ativos
			Ex. 3: post('publish', 10) 		=> retorna posts ativos, limit 10.
			Ex. 4: post('publish', 10, 1)	=> retorna posts ativos, limit 10, pagina 1 de registros

			STATUS	=> string (publish, trash, auto-draft, inherit, draft, private, pending, etc)
			LIMIT	=> inteiro
			PAGINA	=> inteiro

		*/

		if(empty($status) && empty($quantidade) && empty($pagina)){

			$where = "{$this->taxonomy('category')} and {$this->tipoPublicacao('post')}";
			$sql = sprintf($this->posts, $where, "");
			$result = $this->db->select($sql, array(), 'fetchAll');

			foreach ($result as $chave => $valor){
				$result[$chave]['total_registers'] = $result[0]['row_counts'];
			}


		}
		else if(!empty($status) && empty($quantidade) && empty($pagina)){

			$where = "{$this->taxonomy('category')} and {$this->tipoPublicacao('post')} and {$this->statusPublicacao($status)}";
			$sql = sprintf($this->posts, $where, "");
			$result = $this->db->select($sql, array(), 'fetchAll');

			foreach ($result as $chave => $valor){
				$result[$chave]['total_registers'] = $result[0]['row_counts'];
			}

		}

		else{

			$where = "{$this->taxonomy('category')} and {$this->tipoPublicacao('post')} and {$this->statusPublicacao($status)}";
			$sql = sprintf($this->posts, $where, $this->limitPaginacao($quantidade, $pagina));
			$result = $this->db->select($sql, array(), 'fetchAll');

			foreach ($result as $chave => $valor){
				$result[$chave]['total_registers'] = $this->db->rowCount(sprintf($this->posts, $where, ''));
			}


		}

		$result = $this->imagemDestacada($result);
		return $result;


	}

	public function totalPosts(){

		//EXIBE TOTAL DE POSTS PUBLICADOS

		$result = $this->db->select("SELECT  count(*) as total FROM wp_posts WHERE post_type = 'post' and post_status = 'publish'", array(), 'fetch');
		return $result['total'];
	}


	public function pages($status = null, $quantidade = null, $pagina = null){

		/*

			LISTA TODAS OS POSTS

			Ex. 1: pages() 					=> retorna todas as paginas
			Ex. 2: pages('publish') 		=> retorna todas os paginas ativos
			Ex. 3: pages('publish', 10) 	=> retorna paginas ativos, limit 10.
			Ex. 4: pages('publish', 10, 1)	=> retorna paginas ativos, limit 10, pagina 1 de registros

			STATUS	=> string (publish, trash, auto-draft, inherit, draft, private, pending, etc)
			LIMIT	=> inteiro
			PAGINA	=> inteiro

		*/

		if(empty($status) && empty($quantidade) && empty($pagina)){

			$where = "{$this->tipoPublicacao('page')}";
			$sql = sprintf($this->pages, $where, "");

			$result = $this->db->select($sql, array(), 'fetchAll');
			foreach ($result as $chave => $valor){
				$result[$chave]['total_registers'] = $result[0]['row_counts'];
			}
		}

		else if(!empty($status) && empty($quantidade) && empty($pagina)){

			$where = "{$this->tipoPublicacao('page')} and {$this->statusPublicacao($status)}";
			$sql = sprintf($this->pages, $where, "");

			$result = $this->db->select($sql, array(), 'fetchAll');
			foreach ($result as $chave => $valor){
				$result[$chave]['total_registers'] = $result[0]['row_counts'];
			}

		}

		else{

			$where = "{$this->tipoPublicacao('page')} and {$this->statusPublicacao($status)}";
			$sql = sprintf($this->pages, $where, $this->limitPaginacao($quantidade, $pagina));

			$result = $this->db->select($sql, array(), 'fetchAll');

			foreach ($result as $chave => $valor){
				$result[$chave]['total_registers'] = $this->db->rowCount(sprintf($this->pages, $where, ''));
			}

		}

		return $result;

	}

	public function postSlug($slug = null, $param = null){

		/*
		 	EXIBE PUBLICACAO (POST) ATIVA MEDIANTE SLUG INFORMADO
			Ex. 1: postSlug('ola-mundo') => retorna publicacao informada
		*/

		if(empty($slug)){
			return array("ERRO" => "Slug não informado");
		}else{

			$where = "{$this->taxonomy('category')} and {$this->statusPublicacao('publish')} and {$this->slugPublicacao($slug)}";
			$sql = sprintf($this->posts, $where, '');
			$result = $this->db->select($sql, array(), 'fetch');

			if(!empty($param)){

				/**
				 * Ex. params:
				 * titulo
				 * autor
				 * data_hora_publicacao_us
				 * conteudo
				 */

				return (empty($result)) ? false : $result[$param];
			}
			else{
				return (empty($result)) ? false : $result;
			}


		}
	}

	public function postId($id = null){

		/*
		 	EXIBE PUBLICACAO (POST) ATIVA MEDIANTE ID INFORMADO
		 	Ex. 1: postId(109) => retorna publicacao informada
		*/

		if(empty($slug)){
			return array("ERRO" => "Id não informado");
		}else{

			$where = "{$this->taxonomy('category')} and {$this->statusPublicacao('publish')} and {$this->idPublicacao($id)}";
			$sql = sprintf($this->posts, $where, '');
			$result = $this->db->select($sql, array(), 'fetch');
			return (empty($result)) ? false : $result;

		}
	}


	public function titulo($param = null){

		//EXIBE O TITULO DA PÁGINA DE ACORDO COM O CONTEÚDO EM EXIBIÇÃO NA TELA

		if(App::area()){

			if(App::pagina()){

				$result = $this->db->select("
						SELECT post_title, post_date FROM wp_posts WHERE post_type = 'post'  and post_status = 'publish'  and post_name = '{$this->filter(App::pagina(), 'string')}'
						", array(), 'fetch');

				if(!empty($result['post_title'])){
					$return = utf8_encode($result['post_title']) ." &lsaquo; " . utf8_encode( $this->configs('blogname') );
				}else{
					$return = strtoupper( App::area() )." &lsaquo; " . utf8_encode( $this->configs('blogname') );
				}
			}else{
				$return = strtoupper( App::area() )." &lsaquo; " . utf8_encode( $this->configs('blogname') );
			}
		}else{
			$return = utf8_encode( $this->configs('blogname') );
		}

		return $return;
	}


	public function pageSlug($slug){

		/*
		 	EXIBE PAGINA ATIVA MEDIANTE SLUG INFORMADO
			Ex. 1: pageSlug('ola-mundo') => retorna pagina informada
		*/

		if(empty($slug)){
			return array("ERRO" => "Slug nao informado");
		}else{

			$where = "{$this->statusPublicacao('publish')} and {$this->slugPublicacao($slug)}";
			$sql = sprintf($this->pages, $where, '');
			return $this->db->select($sql, array(), 'fetch');

		}
	}

	public function pageId($id){

		/*
		 EXIBE PAGINA ATIVA MEDIANTE ID INFORMADO
		Ex. 1: pageId(109) => retorna pagina informada
		*/

		if(empty($slug)){
			return array("ERRO" => "Id nao informado");
		}else{

			$where = "{$this->statusPublicacao('publish')} and {$this->idPublicacao($id)}";
			$sql = sprintf($this->pages, $where, '');
			return $this->db->select($sql, array(), 'fetch');

		}
	}

		public function postsBusca($string, $quantidade = null, $pagina = null){

			/*
				BUSCA PUBLICACOES (POSTS) ATIVAS MEDIANTE "PALAVRA CHAVE" INFORMADA
				Ex. 1: postSlug('ola-mundo') => retorna publicacao informada
			*/

				$string = $this->filter($string, 'string');

				$busca =  "wp.post_title		like '%{$string}%'";
				$busca .= "or wp.post_content 	like '%{$string}%'";
				$busca .= "or wp.post_name  	like '%{$string}%'";
				$busca .= "or wp.post_type		like '%{$string}%'";
				$busca .= "or wp.post_date 		like '%{$string}%'";
				$busca .= "or wu.user_nicename 	like '%{$string}%'";
				$busca .= "or wt.name 			like '%{$string}%'";

			if(empty($quantidade) && empty($pagina)){

				$where = "{$this->taxonomy('category')} and {$this->statusPublicacao('publish')} and {$this->tipoPublicacao('post')} and {$busca}";
				$sql = sprintf($this->posts, $where, "");

				$result = $this->db->select($sql, array(), 'fetchAll');
				foreach ($result as $chave => $valor){
					$result[$chave]['total_registers'] = $result[0]['row_counts'];
				}

			}

			else{

				$where = "{$this->taxonomy('category')} and {$this->tipoPublicacao('post')} and {$this->statusPublicacao('publish')}  and {$busca}";
				$sql = sprintf($this->posts, $where, $this->limitPaginacao($quantidade, $pagina));

				$result = $this->db->select($sql, array(), 'fetchAll');
				foreach ($result as $chave => $valor){
					$result[$chave]['total_registers'] = $this->db->rowCount(sprintf($this->posts, $where, ''));
				}

			}

			$result = $this->imagemDestacada($result);
			return (empty($result)) ? false : $result;
		}

	public function postsAuthor($autor = null, $quantidade = null, $pagina = null){

		/*
			LISTA TODAS OS POSTS ATIVOS DE UM AUTOR

			Ex. 1: postsAuthor('fabio', 10) 	=> retorna 10 posts ativos
			Ex. 2: postsAuthor('fabio', 10, 1)	=> retorna 10 posts ativos na pagina 1

			AUTOR	=> string ('nome do autor')
			LIMIT	=> inteiro
			PAGINA	=> inteiro
		*/

		if(empty($autor)){

			return array("ERRO" => "Autor nao informado");

		}

		else if (!empty($autor) && empty($quantidade) && empty($pagina)){

			$where = "{$this->taxonomy('category')} and {$this->tipoPublicacao('post')} and {$this->statusPublicacao('publish')} and {$this->autorPublicacao($autor)}";
			$sql = sprintf($this->posts, $where, "");

			$result = $this->db->select($sql, array(), 'fetchAll');
			foreach ($result as $chave => $valor){
				$result[$chave]['total_registers'] = $result[0]['row_counts'];
			}

		}

		else{

			$where = "{$this->taxonomy('category')} and {$this->tipoPublicacao('post')} and {$this->statusPublicacao('publish')} and {$this->autorPublicacao($autor)}";
			$sql = sprintf($this->posts, $where, $this->limitPaginacao($quantidade, $pagina));

			$result = $this->db->select($sql, array(), 'fetchAll');
			foreach ($result as $chave => $valor){
				$result[$chave]['total_registers'] = $this->db->rowCount(sprintf($this->posts, $where, ''));
			}

		}

		$result = $this->imagemDestacada($result);
		return $result;

	}


	public function pagesAuthor($autor = null, $quantidade = null, $pagina = null){

		/*
			LISTA TODAS AS PAGINAS ATIVAS DE UM AUTOR

			Ex. 1: pagesAuthor('fabio', 10) 	=> retorna 10 paginas ativos
			Ex. 2: pagesAuthor('fabio', 10, 1)	=> retorna 10 paginas ativos na pagina 1

			AUTOR	=> string ('nome do autor')
			LIMIT	=> inteiro
			PAGINA	=> inteiro
		*/


		if(empty($autor)){

			return array("ERRO" => "Autor nao informado");

		}

		else if (!empty($autor) && empty($quantidade) && empty($pagina)){

			$where = "{$this->statusPublicacao('publish')} {$this->autorPublicacao($autor)}";
			$sql = sprintf($this->pages, $where, "");

			$result = $this->db->select($sql, array(), 'fetchAll');
			$result['total_registers'] = $result['row_counts'];

		}

		else{

			$where = "{$this->statusPublicacao('publish')} {$this->autorPublicacao($autor)}";
			$sql = sprintf($this->pages, $where, $this->limitPaginacao($quantidade, $pagina));

			$result = $this->db->select($sql, array(), 'fetchAll');
			foreach ($result as $chave => $valor){
				$result[$chave]['total_registers'] = $this->db->rowCount(sprintf($this->posts, $where, ''));
			}

		}

		return $result;

	}


	public function postsCategory($categoria = null, $post_type = 'post', $quantidade = null, $pagina = null){

		/*
			LISTA TODAS OS POSTS ATIVOS DE UMA CATEGORIA

			Ex. 1: postsCategory('php', 10) 	=> retorna 10 posts ativos
			Ex. 2: postsCategory('php', 10, 1)	=> retorna 10 posts ativos na pagina 1

			AUTOR	=> string ('nome da categoria')
			LIMIT	=> inteiro
			PAGINA	=> inteiro
		*/

		if(empty($categoria)){

			return array("ERRO" => "Categoria nao informada");

		}

		else if (!empty($categoria) && empty($quantidade) && empty($pagina)){

			if($categoria == 'todos'){
				$where = "{$this->taxonomy('category')} and {$this->tipoPublicacao($post_type)} and {$this->statusPublicacao('publish')}";
			}else{
				$where = "{$this->taxonomy('category')} and {$this->tipoPublicacao($post_type)} and {$this->statusPublicacao('publish')} and {$this->categoriaPublicacao($categoria)}";
			}

			$sql = sprintf($this->posts, $where, "");
			$result = $this->db->select($sql, array(), 'fetchAll');

			foreach ($result as $chave => $valor){
				$result[$chave]['total_registers'] = $result[0]['row_counts'];
			}

		}

		else{

			if($categoria == 'todos'){
				$where = "{$this->taxonomy('category')} and {$this->tipoPublicacao($post_type)} and {$this->statusPublicacao('publish')}";
			}else{
				$where = "{$this->taxonomy('category')} and {$this->tipoPublicacao($post_type)} and {$this->statusPublicacao('publish')} and {$this->categoriaPublicacao($categoria)}";
			}

			$sql = sprintf($this->posts, $where, $this->limitPaginacao($quantidade, $pagina));

			$result = $this->db->select($sql, array(), 'fetchAll');
			foreach ($result as $chave => $valor){
				$result[$chave]['total_registers'] = $this->db->rowCount(sprintf($this->posts, $where, ''));
			}

		}


		$result = $this->imagemDestacada($result);
		return $result;

	}


	public function categorys($status = null, $post_type = 'post', $exception = null){

		/*
			LISTA TODAS OS CATEGORIAS E QUANTIDADE DE POSTS => INFORMAR STATUS DO POST

			Ex. 1: categorys('publish') 	=> retorna todas as categorias com posts ativos
			STATUS	=> string ('publish', 'trash' ou '')
		*/

		if(empty($status) && empty($exception)){

			$where = "{$this->taxonomy('category')} and {$this->tipoPublicacao($post_type)}";
			$sql = sprintf($this->categorys, $where);

		}else if(!empty($status) && empty($exception)){
			$where = "{$this->taxonomy('category')} and {$this->tipoPublicacao($post_type)} and {$this->statusPublicacao($status)}";
			$sql = sprintf($this->categorys, $where);
		}

		else if(!empty($status) && !empty($exception)){
			$where = "{$this->taxonomy('category')} and {$this->tipoPublicacao($post_type)} and {$this->statusPublicacao($status)} and wt.name not in (" . implode(',', $exception) . ")";
			$sql = sprintf($this->categorys, $where);
		}

		$result = $this->db->select($sql, array(), 'fetchAll');
		array_unshift($result, array('categoria' => 'Todos', 'slug' => 'todos', 'total_posts' => $this->totalPosts()));
		return $result;

	}


	public function authors($status = null){

		/*
			LISTA TODAS OS AUTORES => INFORMAR STATUS DO POST

			Ex. 1: authors('publish') 	=> retorna todas os autores com posts ativos
			STATUS	=> string ('publish', 'trash' ou '')
		*/

		if(empty($status)){

			$where = "{$this->tipoPublicacao('post')}";
			$sql = sprintf($this->authors, $where);

		}else{

			$where = "{$this->tipoPublicacao('post')} and {$this->statusPublicacao($status)}";
			$sql = sprintf($this->authors, $where);
		}

		return $this->dataUsers($this->db->select($sql, array(), 'fetchAll'));

	}

	public function users($tipo = null){

		/*
			LISTA TODAS OS USUARIOS => INFORMAR TIPO DE USUARIO

			Ex. 1: users('administrator') => retorna todas os autores com posts ativos
			STATUS	=> string ('administrator', 'contributor', etc)
		*/

		//wum.meta_key = 'wp_capabilities' AND wum.meta_value like '%administrator%'

		if(empty($tipo)){

			$where = "{$this->metaKey('wp_capabilities')}";
			$sql = sprintf($this->users, $where);

		}else{

			$where = "{$this->metaKey('wp_capabilities')} and {$this->metaValue($tipo)}";
			$sql = sprintf($this->users, $where);
		}

		return $this->dataUsers($this->db->select($sql, array(), 'fetchAll'));

	}


	private function dataUsers($result){
		$dados = array();
		foreach ($result as $chave => $valor){

			$dados_users = $this->db->select("SELECT * FROM wp_usermeta WHERE user_id = {$this->filter($valor['id'], 'int')}", array(), 'fetchAll');

			foreach ( (array) $dados_users as $dados_user ) {
				$dados[$dados_user['meta_key']] = $dados_user['meta_value'];
			}

			$result[$chave]['sobre'] 		= $dados['description'];
			$result[$chave]['nivel'] 		= $dados['wp_user_level'];
			$result[$chave]['tipo']			= unserialize($dados['wp_capabilities']);
			$result[$chave]['img_perfil'] 	= (isset($dados['cupp_upload_meta']) ? $dados['cupp_upload_meta'] : false);

		}

		return $result;
	}

	private function statusPublicacao($status){

		//DEFINE STATUS SQL => STATUS PUBLICACAO (wp_post) MEDIANTE VALOR INFORMADO
		//Ex. status: publish, trash, auto-draft, inherit, draft, private, pending

		if(isset($status) && !empty($status)){

			return "wp.post_status = '{$this->filter($status, 'string')}'";

		} else{

			return "";

		}

	}


	private function slugPublicacao($slug){

		//MONTA SQL SLUG PUBLICACAO (wp_post)

		if(isset($slug) && !empty($slug)){

			return "wp.post_name = '{$this->filter($slug, 'string')}'";
		}

		else{
			return "";
		}

	}

	private function idPublicacao($id){

		//MONTA SQL SLUG PUBLICACAO (wp_post)

		if(isset($id) && !empty($id)){

			return "wp.ID = '{$this->filter($id, 'int')}'";
		}

		else{
			return "";
		}

	}

	private function autorPublicacao($autor){

		//MONTA SQL AUTOR PUBLICACAO (wp_users)

		if(isset($autor) && !empty($autor)){

			return "wu.user_nicename = '{$this->filter($autor, 'string')}'";
		}

		else{
			return "";
		}

	}


	private function categoriaPublicacao($categoria){

		//MONTA SQL CATEGORIA (wp_terms) PUBLICACAO

		if(isset($categoria) && !empty($categoria)){

			return "wt.slug = '{$this->filter($categoria, 'string')}'";
		}

		else{
			return "";
		}

	}

	private function tipoAnexo($tipo){

		//MONTA SQL TIPO ANEXO (wp_posts) PUBLICACAO
		//Ex. tipos: pdf, mp4, jpeg, image, video, application, etc

		if(isset($tipo) && !empty($tipo)){

			return "attach.post_mime_type like '%{$this->filter($tipo, 'string')}%'";
		}

		else{
			return "";
		}

	}

	private function metaKey($tipo){


		//DEFINE TIPO SQL => TIPO META KEY (wp_usermeta) MEDIANTE VALOR INFORMADO
		//Ex. tipos: description, nickname, first_name, last_name

		if(isset($tipo) && !empty($tipo)){
			return "wum.meta_key = '{$this->filter($tipo, 'string')}'";
		}

		else{
			return "";
		}
	}

	private function metaValue($tipo){


		//DEFINE TIPO SQL (like) => TIPO META VALUE (wp_usermeta) MEDIANTE VALOR INFORMADO
		//Ex. tipos: administrator, contributor, etc

		if(isset($tipo) && !empty($tipo)){
			return "wum.meta_value like '%{$this->filter($tipo, 'string')}%'";
		}

		else{
			return "";
		}
	}

	private function tipoPublicacao($tipo){

		//DEFINE TIPO SQL => TIPO PUBLICACAO (wp_post) MEDIANTE VALOR INFORMADO
		//Ex. tipos: post, page, attachment, revision

		if(isset($tipo) && !empty($tipo)){
			return "wp.post_type = '{$this->filter($tipo, 'string')}'";
		}

		else{
			return "";
		}
	}

	private function taxonomy($taxonomy){

		//DEFINE TIPO SQL => TIPO PUBLICACAO (wp_term_taxonomy) MEDIANTE VALOR INFORMADO
		//Ex. tipos: category, post_tag, etc

		if(isset($taxonomy) && !empty($taxonomy)){
			return "wtt.taxonomy = '{$this->filter($taxonomy, 'string')}'";
		}

		else{
			return "";
		}
	}


	private function limitPaginacao($quantidade = null, $pagina = null){

		//DEFININE LIMIT SQL => PAGINACAO DE ACORDO COM A QUANTIDADE DE REGISTROS REQUERIDOS E NÚMERO DA PAGINA INFORMADO

		if(!empty($quantidade) && empty($pagina)){

			return "LIMIT {$quantidade}";
		}

		else if(!empty($quantidade) && !empty($pagina)){

			$inicio = ($quantidade * $pagina) - $quantidade;
			return "LIMIT {$this->filter($inicio, 'int')}, {$this->filter($quantidade, 'int')}";

		}

		else{
			return "";
		}
	}


	public function paginacao($total, $quantidade = null, $url = null){

		//CRIA LISTA DE PAGINACAO => INFORMAR TOTAL DE REGISTROS DA CONSULTA,
		//QUANTIDADE REQUERIDA POR PAGINA E URL PERSONALIZADA PARA NEVAGACAO

		//calcula o número de páginas arredondando o resultado para cima
		$quantidade = (!isset($quantidade) || empty($quantidade)) ? 1 : $quantidade;

		$num_paginas = ceil($total/$quantidade);

		$voltar = (Url::segments('C') <= 1) ? 1 : Url::segments('C') - 1;
		$avancar = (Url::segments('C') == $num_paginas) ? $num_paginas : Url::segments('C') + 1;

		$list = "<ul class='pagination'>";
		$list .= "<li><a href='{$url}/{$voltar}'>&laquo; Voltar</a></li>";

		for($i = 1; $i < $num_paginas + 1; $i++) {
			$active = ($i == Url::segments('C')) ? 'active' : '';
			$list .= "<li class='{$active}'><a href='{$url}/$i'>".$i."</a></li>";
		}

		$list .= "<li><a href='{$url}/{$avancar}'>Avançar &raquo;</a></li>";
		return $list .= "</ul>";

	}

	function data_hora_br($data_hora){
		//CONVERTE DATA HORA US PARA DATA HORA PADRÃO PT-BR

		$timestamp      = strtotime($data_hora);
		$dia            = date('d', $timestamp);
		$mes            = date('m', $timestamp);
		$ano            = date('Y', $timestamp);

		$horas          = date('H', $timestamp);
		$minutos        = date('i', $timestamp);
		$segundos       = date('s', $timestamp);

		return $dia."/".$mes."/".$ano ." ". $horas.":".$minutos.":".$segundos;

	}

	function data_br($data_hora){
		//CONVERTE DATA HORA US PARA DATA PADRÃO PT-BR

		$timestamp      = strtotime($data_hora);
		$dia            = date('d', $timestamp);
		$mes            = date('m', $timestamp);
		$ano            = date('Y', $timestamp);

		return $dia."/".$mes."/".$ano;

	}

	function hora_br($data_hora){
		//CONVERTE DATA HORA US PARA HORA PADRÃO PT-BR

		$timestamp      = strtotime($data_hora);
		$horas          = date('H', $timestamp);
		$minutos        = date('i', $timestamp);
		$segundos       = date('s', $timestamp);

		return $horas.":".$minutos.":".$segundos;

	}

	public function dataExtenso($data_hora = null){

		//CONVERTE DATA HORA US PARA DATA PADRÃO PT-BR POR EXTENSO

		if(isset($data_hora) && !empty($data_hora)){
			$timestamp = strtotime($data_hora);
		}else{
			$timestamp = time();
		}

		$data = getdate($timestamp);

		$dia_semana = array("Domingo, ", "Segunda-feira, ", "Terça-feira, ", "Quarta-feira, ", "Quinta-feira, ", "Sexta-feira, ", "Sábado, ");
		$meses 		= array("", "Janeiro", "Fevereiro ", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");


		return $dia_semana[$data["wday"]] ." ". $data["mday"]." de ".$meses[$data["mon"]]." de ".$data["year"];
	}

	private function html2txt($texto){

		// REMOVE TODAS AS TAGS HTML DO POST TRANFORMANDO EM APENAS TEXT SIMPLES

		$search = array('@<script[^>]*?>.*?</script>@si', // Strip out javascript
				'@<[\/\!]*?[^<>]*?>@si',            	  // Strip out HTML tags
				'@<style[^>]*?>.*?</style>@siU',    	  // Strip style tags properly
				'@<![\s\S]*?--[ \t\n\r]*>@'         	  // Strip multi-line comments including CDATA
		);
		$result = preg_replace($search, '', $texto);
		return $result;
	}

	private function maybe_unserialize( $original ) {

		// don't attempt to unserialize data that wasn't serialized going in

		if ( $this->is_serialized( $original ) )
			return @unserialize( $original );
		return $original;
	}

	private function is_serialized( $data, $strict = true ) {

		// if it isn't a string, it isn't serialized.

		if ( ! is_string( $data ) ) {
			return false;
		}
		$data = trim( $data );
		if ( 'N;' == $data ) {
			return true;
		}
		if ( strlen( $data ) < 4 ) {
			return false;
		}
		if ( ':' !== $data[1] ) {
			return false;
		}
		if ( $strict ) {
			$lastc = substr( $data, -1 );
			if ( ';' !== $lastc && '}' !== $lastc ) {
				return false;
			}
		} else {
			$semicolon = strpos( $data, ';' );
			$brace     = strpos( $data, '}' );
			// Either ; or } must exist.
			if ( false === $semicolon && false === $brace )
				return false;
			// But neither must be in the first X characters.
			if ( false !== $semicolon && $semicolon < 3 )
				return false;
			if ( false !== $brace && $brace < 4 )
				return false;
		}
		$token = $data[0];
		switch ( $token ) {
			case 's' :
				if ( $strict ) {
					if ( '"' !== substr( $data, -2, 1 ) ) {
						return false;
					}
				} elseif ( false === strpos( $data, '"' ) ) {
					return false;
				}
				// or else fall through
			case 'a' :
			case 'O' :
				return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
			case 'b' :
			case 'i' :
			case 'd' :
				$end = $strict ? '$' : '';
				return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
		}
		return false;
	}

	public function resumePost($texto, $maximoCaracteres){

		// REMOVE TODAS AS TAGS HTML E GERA UM RESUMO DE ACORDO COM A QUANTIDADE DE CARACTERES INFORMADO

			$post = $this->html2txt($texto);

			$ex = explode(" ", strip_tags( $post ) );

			$novoTexto = null;

			foreach( $ex as $palavra ) {
				if ( strlen($novoTexto) < $maximoCaracteres ) {
					$tamanho = strlen($novoTexto) + strlen($palavra);

					if ( $tamanho < $maximoCaracteres ) {
						$novoTexto .= " {$palavra}";
					} else {
						break;
					}
				} else {
					break;
				}
			}

			return $novoTexto;
	}

	/**
	 *
	 * @param unknown_type $posts
	 */
	public function output($posts, $character_limit){

		foreach ($posts as $key => $value){

			$posts[$key]['data_extenso'] 		= $this->dataExtenso($posts[$key]['data_hora_publicacao_us']);
			$posts[$key]['data_publicacao'] 	= $this->data_br($posts[$key]['data_hora_publicacao_us']);
			$posts[$key]['hora_publicacao'] 	= $this->hora_br($posts[$key]['data_hora_publicacao_us']);
		    $posts[$key]['conteudo'] 			= $this->resumePost($posts[$key]['conteudo'], $character_limit);

		}

		return $posts;

	}

	public function filter($variavel, $tipo) {
		// Sanitize filters
		// Tipos: email, encoded, quotes, float, int, special_chars, full_special_chars, string, stripped, url, unsafe_raw

		switch ($tipo) {

			// EMAIL => Remove todos caracteres exceto letras, dígitos e #$%&'*+-/=^_`!{|}~@.[].
			case 'email':
				$filter = FILTER_SANITIZE_EMAIL;
				break;

				// ENCODED => Seqüência de URL-codificada, opcionalmente remove ou codifica caracteres especiais.
			case 'encoded':
				$filter = FILTER_SANITIZE_ENCODED;
				break;

			case 'quotes':
				$filter = FILTER_SANITIZE_MAGIC_QUOTES;
				break;

				// NUMBER FLOAT => Remove todos caracteres exceto dígitos, + - e, opcionalmente, eE..
			case 'float':
				$filter = FILTER_SANITIZE_NUMBER_FLOAT;
				break;

				// NUMBER INT => Remove todos caracteres exceto dígitos, mais e sinal de menos.
			case 'int':
				$filter = FILTER_SANITIZE_NUMBER_INT;
				break;

				// ESPECIAL CHARS => HTML-escape '"<> & e caracteres com valor ASCII menor que 32, opcionalmente remove ou codifica outros caracteres especiais.
			case 'special_chars':
				$filter = FILTER_SANITIZE_SPECIAL_CHARS;
				break;

				/*
				 FULL ESPECIAL CHARTS => Equivalente a chamar htmlspecialchars () com ENT_QUOTES set. Citações de codificação pode ser desabilitado configurando
				FILTER_FLAG_NO_ENCODE_QUOTES. Como htmlspecialchars (), este filtro está ciente da default_charset e se for detectada uma sequência de bytes que
				compõe um caractere inválido no conjunto atual de caracteres, em seguida, toda a cadeia é rejeitada resultando em uma seqüência de comprimento 0.
				Ao usar esse filtro como um filtro padrão, consulte o seguinte aviso sobre a configuração das bandeiras padrão para 0.
				*/

			case 'full_special_chars':
				$filter = FILTER_SANITIZE_FULL_SPECIAL_CHARS;
				break;

				// STRING => Remove tags, opcionalmente remove ou codifica caracteres especiais.
			case 'string':
				$filter = FILTER_SANITIZE_STRING;
				break;

				// STRIPPED => Alias of "string" filter.
			case 'stripped':
				$filter = FILTER_SANITIZE_STRIPPED;
				break;

				// URL => Remove all characters except letters, digits and $-_.+!*'(),{}|\\^~[]`<>#%";/?:@&=.
			case 'url':
				$filter = FILTER_SANITIZE_URL;
				break;

				// UNSAFE RAW => Do nothing, optionally strip or encode special characters. This filter is also aliased to FILTER_DEFAULT.
			case 'unsafe_raw':
				$filter = FILTER_UNSAFE_RAW;
				break;
		}

		return filter_var($variavel, $filter);
	}
}
