<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Controller;

use Embera\Embera as Oembed;
use Embera\Formatter as Formatter;
use Farol360\AncoraEad\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;
use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\Model\EntityFactory;

class PostController extends Controller
{

    protected $postModel;
    protected $postTypeModel;
    protected $postFileModel;
    protected $postSerieModel;
    protected $entityFactory;
    protected $oembed;

    public function __construct(View $view, FlashMessages $flash, Model $post, Model $postType, Model $postFile, Model $postSerie, Oembed $oembed, EntityFactory $entityFactory )
    {
        parent::__construct($view, $flash);
        $this->postModel = $post;
        $this->postTypeModel = $postType;
        $this->postFileModel = $postFile;
        $this->postSerieModel = $postSerie;
        $this->entityFactory = $entityFactory;
        $this->oembed = $oembed;
    }

    //post list
    public function index(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        if (!empty($params['page'])) {
            $page = intval($params['page']);
        } else {
            $page = 1;
        }
        $limit = 10;
        $offset = ($page - 1) * $limit;

        if (!empty($params['ordem'])) {
            $ordem = intval($params['ordem']);
        } else {
            $ordem = 1;
        }

        if ($ordem == 1) {
            $posts = $this->postModel->getAll($offset, $limit);
        } else {
            $posts = $this->postModel->getAllAsc($offset, $limit);
        }

        $postTypes = $this->postTypeModel->getAll();

        foreach ($posts as $post) {
            if ($post->id_tipo_post == 2) {
                $embera =  new Oembed(
                        ['params' => [
                            'responsive' => true

                            ],
                        ]);
                $post->embed_desktop = new Formatter(
                    new Oembed(
                        ['params' => [
                            'responsive' => true

                            ],
                        ]));
                
                $post->embed_mobile = new Formatter(
                    new Oembed(
                        ['params' => [
                            'width' => 226,

                            ],
                        ]));

                //$post->embed_desktop = $post->embed_desktop->setTemplate('<div style="width:300px;" class="admin-video-list">{html}</div>', $post->destaque);
                //$post->embed_mobile = $post->embed_mobile->setTemplate('<div style="width:226px; position: relative; margin: 0 auto;" class="admin-video-list">{html}</div>', $post->destaque);
             $post->embed_desktop = $embera->autoEmbed($post->destaque);
               
            }

        }

        $amountPosts = $this->postModel->getAmount();
        $amountPages = ceil($amountPosts->amount /$limit);
         return $this->view->render($response, 'page/midia.twig', [
            'posts' => $posts,
            'postTypes' => $postTypes,
            'page' => $page,
            'amountPages' => $amountPages,
            'ordem' => $ordem
        ]);
    }

    //post list
    public function midia_fotos(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        if (!empty($params['page'])) {
            $page = intval($params['page']);
        } else {
            $page = 1;
        }
        $limit = 10;
        $offset = ($page - 1) * $limit;

        if (!empty($params['ordem'])) {
            $ordem = intval($params['ordem']);
        } else {
            $ordem = 1;
        }

        $posts = $this->postModel->getAll($offset, $limit);
        $postTypes = $this->postTypeModel->getAll();

        foreach ($posts as $post) {
            if ($post->id_tipo_post == 2) {
                $post->embed = new Formatter(
                    new Oembed(
                        ['params' => [
                            'width' => 300,
                            'heigth' => 160,
                            ],
        ]));
                $post->embed = $post->embed->setTemplate('<div style="width:100%;" class="admin-video-list">{html}</div>', $post->destaque);
            }

        }

        $amountPosts = $this->postModel->getAmount();
        $amountPages = ceil($amountPosts->amount /$limit);
        return $this->view->render($response, 'page/midia_foto.twig', [
            'posts' => $posts,
            'postTypes' => $postTypes,
            'page' => $page,
            'amountPages' => $amountPages,
            'ordem' => $ordem
        ]);
    }

    //post list
    public function midia_videos(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        if (!empty($params['page'])) {
            $page = intval($params['page']);
        } else {
            $page = 1;
        }
        $limit = 10;
        $offset = ($page - 1) * $limit;

        if (!empty($params['ordem'])) {
            $ordem = intval($params['ordem']);
        } else {
            $ordem = 1;
        }

        $posts = $this->postModel->getAll($offset, $limit);
        $postTypes = $this->postTypeModel->getAll();

        foreach ($posts as $post) {
            if ($post->id_tipo_post == 2) {
                $post->embed = new Formatter(
                    new Oembed(
                        ['params' => [
                            'width' => 300,
                            'heigth' => 160,
                            ],
                ]));
                $embera =  new Oembed(
                        ['params' => [
                            'responsive' => true

                            ],
                        ]);
                $post->embed_desktop = $embera->autoEmbed($post->destaque);
            }

        }
        
        $amountPosts = $this->postModel->getAmount();
        $amountPages = ceil($amountPosts->amount /$limit);
        return $this->view->render($response, 'page/midia_video.twig', [
            'posts' => $posts,
            'postTypes' => $postTypes,
            'page' => $page,
            'amountPages' => $amountPages,
            'ordem' => $ordem
        ]);
    }

    public function testePost(Request $request, Response $response): Response
    {

        $post = $this->entityFactory->createPost([
            'id' => 2,
            'id_tipo_post' => 1,
            'data_cadastro' => date("Y-m-d H:i:s"),
            'data_alteracao' => date("Y-m-d H:i:s"),
            'id_autor' => 1,
            'titulo' => "teste 3",
            'descricao' => "teste 3",
            'destaque' => "teste 3",
            'status' => 1,
            'id_categoria' => 1
        ]);

        $this->postModel->update($post);

        return $this->view->render($response, 'page/teste.twig', [
        //    'post' => $post
         ]);
    }

    public function postid(Request $request, Response $response, array $args): Response
    {

        $params = $request->getQueryParams();

        if (!empty($params['page'])) {
            $page = intval($params['page']);
        } else {
            $page = 1;
        }

        if (!empty($params['ordem'])) {
            $ordem = intval($params['ordem']);
        } else {
            $ordem = 1;
        }

        if (!empty($params['tipo_post'])) {
            $tipo_post = $params['tipo_post'];
        } else {
            $tipo_post = 'midia';
        }

        // se tipo post for diferente de /midia
        if (strcmp($tipo_post,'midia') != 0 ) {
            $rota_origem = '/midia/' . $tipo_post;
        } else {
            $rota_origem = '/' . $tipo_post;
        }

        $id = intval($args['id']);
        $post = $this->postModel->get($id);
        if ($post->id_tipo_post == 2) {
                $post->embed_desktop = new Oembed();
                $post->embed_desktop = $post->embed_desktop->autoEmbed($post->destaque);

                $post->embed_mobile = new Formatter(
                    new Oembed(
                        ['params' => [
                            'width' => 300,
                            'heigth' => 160,
                            ],
                ]));
                $post->embed_mobile = $post->embed_mobile->setTemplate('<div style="width:300px;" class="admin-video-list">{html}</div>', $post->destaque);

            }

        $postFiles = $this->postFileModel->getFiles($id);

        return $this->view->render($response, 'page/postid.twig', [
           'post' => $post,
           'ordem' => $ordem,
           'page' => $page,
           'rota_origem' => $rota_origem,
           'postFiles' => $postFiles

         ]);
    }

    public function serieId(Request $request, Response $response, array $args): Response
    {
        if (isset($args['id'])) {
            $id = intval($args['id']);
        } else {
            $id = 0;
        }
        
        $slug = $args['slug'];
        $serie = $this->postSerieModel->getBySlug($slug);

        // get serie atual posts
        $serie_posts = $this->postModel->getByPostSerie((int) $serie->id);

        // prepare embeded for serie_atual_posts
        foreach ($serie_posts as $post)
        {
            if ($post->id_tipo_post == 2)
            {
                $post->embed_desktop = new Formatter(
                    new Oembed(
                        ['params' => [
                            'width' => 557,

                            ],
                        ]));
                $post->embed_mobile = new Formatter(
                    new Oembed(
                        ['params' => [
                            'width' => 258,

                            ],
                        ]));

                $post->embed_desktop = $post->embed_desktop->setTemplate('<div  class="admin-video-list">{html}</div>', $post->destaque);

                $post->embed_mobile = $post->embed_mobile->setTemplate('<div style="width:258px; position: relative; margin: 0 auto;" class="admin-video-list">{html}</div>', $post->destaque);
            }
        }

        return $this->view->render($response, 'page/serieId.twig', [
           'serie' => $serie,
           'serie_posts' => $serie_posts
         ]);
    }

}
