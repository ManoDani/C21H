<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Controller\Admin;

use Embera\Embera as Oembed;
use Embera\Formatter as Formatter;
use Farol360\AncoraEad\Controller;
use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\Model\EntityFactory;
use Farol360\AncoraEad\Model\PostFile as PostFile;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

class PostController extends Controller
{
    protected $postModel;
    protected $postTypeModel;
    protected $postSerieModel;
    protected $postTagModel;
    protected $entityFactory;
    protected $oembed;
    protected $postFileModel;

    public function __construct(
        View $view,
        FlashMessages $flash,
        Model $post,
        Model $postType,
        Model $postSerie,
        Model $postFile,
        Model $postTag,
        Oembed $oembed,
        EntityFactory $entityFactory
    ) {
        parent::__construct($view, $flash);
        $this->postModel = $post;
        $this->postTypeModel = $postType;
        $this->postSerieModel = $postSerie;
        $this->postFileModel = $postFile;
        $this->postTagModel = $postTag;
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

        //remove temporaries if has any
        $this->postModel->deleteTipo(3);

        $posts = $this->postModel->getAll($offset, $limit);
        $postTypes = $this->postTypeModel->getAll();

        foreach ($posts as $post) {

            $post->categoria = $this->postSerieModel->get($post->id_categoria)->nome_serie;

            if ($post->id_tipo_post == 2) {
                $post->embed_mobile_iphone = new Formatter(
                    new Oembed(
                        ['params' => [
                            'width' => 226,
                            'heigth' => 130,
                            ],
                        ]));
                $post->embed_mobile_samsung = new Formatter(
                    new Oembed(
                        ['params' => [
                            'width' => 240,
                            'heigth' => 150,
                            ],
                        ]));
                $post->embed_desktop = new Formatter(
                    new Oembed(
                        ['params' => [
                            'width' => 300,
                            'heigth' => 160,
                            ],
                        ]));

                $post->embed_mobile_iphone = $post->embed_mobile_iphone->setTemplate('<div style="width:226px; position:relative; margin:0 auto;" class="admin-video-list">{html}</div>', $post->destaque);

                $post->embed_mobile_samsung = $post->embed_mobile_samsung->setTemplate('<div style="width:240px; position:relative; margin:0 auto;" class="admin-video-list">{html}</div>', $post->destaque);

                $post->embed_desktop = $post->embed_desktop->setTemplate('<div style="width:300px; position:relative; margin:0 auto;" class="admin-video-list">{html}</div>', $post->destaque);
            }

        }

        $amountPosts = $this->postModel->getAmount();
        $amountPages = ceil($amountPosts->amount / $limit);


        return $this->view->render($response, 'admin/post/index.twig', [
            'posts' => $posts,
            'postTypes' => $postTypes,
            'page' => $page,
            'amountPages' => $amountPages,
        ]);
    }

    public function add(Request $request, Response $response): Response
    {
        if (empty($request->getParsedBody())) {

            //clean temp
            $this->postModel->deleteTipo(3);

            $postTypes = $this->postTypeModel->getAll();
            $postSeries = $this->postSerieModel->getAll();

            //create temp post
            $data['id_tipo_post'] = 3; //temp post id
            $data['id_categoria'] = 0; //Sem categoria, padrão
            $data['data_cadastro'] = date('Y-m-d H:i');
            $data['data_alteracao'] = date('Y-m-d H:i');
            $data['titulo'] = "Nova Mídia";
            $data['descricao'] = "";
            $data['destaque'] = "images/default-img.png";
            $data['status'] = 0;

            $post = $this->entityFactory->createPost($data);
            $post->id = $this->postModel->add($post);


            return $this->view->render($response, 'admin/post/add.twig', [
            'postTypes' => $postTypes,
            'post' => $post,
            'postSeries' => $postSeries
        ]);
        }
        $post = $this->entityFactory->createPost($request->getParsedBody());

        $post->id = (int)$this->postModel->add($post);

        if ($post->id !== null) {
            $files = $request->getUploadedFiles();
            if (!empty($files['destaque-img'])) {
                $image = $files['destaque-img'];
                if ($image->getError() === UPLOAD_ERR_OK) {
                    //verify allowed extensions
                    $filename = $image->getClientFilename();

                    $allowedExtensions = [
                        'jpg',
                        'jpeg',
                        'gif',
                        'png'
                    ];

                    if (!in_array(pathinfo($filename,PATHINFO_EXTENSION), $allowedExtensions)) {
                        //remove post atual
                        $this->postModel->delete($post->id);
                        $this->flash->addMessage('danger', "Imagem em formato inválido.");
                        return $this->httpRedirect($request, $response, '/admin/posts/add');
                    }

                    //verify size
                    if ($image->getSize() > 400000) {
                        $this->postModel->delete($post->id);
                        $this->flash->addMessage('danger', "Imagem muito grande (max 300kb).");
                        return $this->httpRedirect($request, $response, '/admin/posts/add');
                    }

                    $filename = sprintf(
                        '%s.%s',
                        uniqid(),
                        pathinfo($image->getClientFilename(), PATHINFO_EXTENSION)
                    );
                    $path = 'upload/img/';
                    $image->moveTo($path . $filename);
                    $post->destaque = $path . $filename;
                    $this->postModel->update($post);
                }
            }
            if (!empty($files['imagens'])) {
                foreach ($files['imagens'] as $img) {
                    $image = $img;
                    if ($image->getError() === UPLOAD_ERR_OK) {
                        $filename = sprintf(
                            '%s.%s',
                            uniqid(),
                            pathinfo($image->getClientFilename(), PATHINFO_EXTENSION)
                        );
                        $path = 'upload/img/';
                        $image->moveTo($path . $filename);
                        $data['id_post'] = $post->id;
                        $data['file'] = $path . $filename;
                        $postFile = new PostFile( $data);
                        $this->postFileModel->add($postFile);
                    }
                }

            }
            $this->flash->addMessage('success', "Mídia adicionada com sucesso.");
            return $this->httpRedirect($request, $response, '/admin/posts');
        }
        $this->flash->addMessage('danger', "Erro ao adicionar Mídia.");
        return $this->httpRedirect($request, $response, '/admin/posts');
    }

    public function addimagem(Request $request, Response $response, array $args) {
        $postFile = $this->entityFactory->createPostFile($request->getParsedBody());
        $id_post = $postFile->id_post;
        $files = $request->getUploadedFiles();

        if ($files['imagens'][0]->getSize() == 0) {

            $this->flash->addMessage('danger', "Não foram carregadas imagens.");
            return $this->httpRedirect($request, $response, '/admin/posts/galeria/'. $id_post);

        }

        else {

            foreach ($files['imagens'] as $img) {
                $image = $img;
                if ($image->getError() === UPLOAD_ERR_OK) {
                    //verify allowed extensions
                    $filename = $image->getClientFilename();

                    $allowedExtensions = [
                        'jpg',
                        'jpeg',
                        'gif',
                        'png'
                    ];

                    if (!in_array(pathinfo($filename,PATHINFO_EXTENSION), $allowedExtensions)) {
                        $this->flash->addMessage('danger', "Imagem em formato inválido.");
                        return $this->httpRedirect($request, $response, '/admin/posts/galeria/'. $id_post);
                    }

                    //verify size
                    if ($image->getSize() > 400000) {
                        $this->flash->addMessage('danger', "Imagem muito grande (max 300kb).");
                        return $this->httpRedirect($request, $response, '/admin/posts/galeria/'. $id_post);
                    }
                    $filename = sprintf(
                        '%s.%s',
                        uniqid(),
                        pathinfo($image->getClientFilename(), PATHINFO_EXTENSION)
                    );
                    $path = 'upload/img/';
                    $image->moveTo($path . $filename);
                    $data['id_post'] = $id_post;
                    $data['file'] = $path . $filename;
                    $postFile = $this->entityFactory->createPostFile($data);
                    $this->postFileModel->add($postFile);
                }

                else {
                    $postFiles = $this->postFileModel->getFiles((int)$id_post);
                    $this->flash->addMessage('success', "Erro ao carregar mídia.");
                    return $this->httpRedirect($request, $response, '/admin/posts/galeria/'. $id_post);
                }
            }
            $postFiles = $this->postFileModel->getFiles((int)$id_post);
            $this->flash->addMessage('success', "Mídia adicionada com sucesso.");
            return $this->httpRedirect($request, $response, '/admin/posts/galeria/'. $id_post);
        }

    }

    /*
    Chamada assíncrona
    */
    public function addTag(Request $request, Response $response, array $args)
    {
        $postTag = $this->entityFactory->createPostTag($request->getParsedBody());
        $postTag->id = $this->postTagModel->add($postTag);
        return json_encode($postTag);
    }

    public function removeTag(Request $request, Response $response, array $args)
    {
        $post = intval($args['id']);
        $teste = $request->getParsedBody();
        $postTag = $this->postTagModel->get(intval($teste['id']));
        $error = 1;
        if($this->postTagModel->delete($postTag->id)) {
            return json_encode($postTag);
        }
        return json_encode($error);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $postId = intval($args['id']);
        $post = $this->postModel->get($postId);

        //remove old img from disk
        if (file_exists($post->destaque)) {
            unlink($post->destaque);
        }

        //remove post
        $this->postModel->delete($postId);

        //remove galery files
        $this->postFileModel->delete($postId);

        //redirect
        $this->flash->addMessage('success', "Post removido com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/posts');
    }

    public function disable(Request $request, Response $response, array $args): Response
    {
        $postId = intval($args['id']);
        $this->postModel->disable($postId);
        $this->flash->addMessage('success', "Post desabilitado com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/posts');
    }

    public function enable(Request $request, Response $response, array $args): Response
    {
        $postId = intval($args['id']);
        $this->postModel->enable($postId);
        $this->flash->addMessage('success', "Post habilitado com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/posts');
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $postId = intval($args['id']);
        $post = $this->postModel->get($postId);

        if (!$post) {
            $this->flash->addMessage('danger', "Post não encontrado.");
            return $this->httpRedirect($request, $response, '/admin/posts');
        }

        $postTypes = $this->postTypeModel->getAll();
        $postSeries = $this->postSerieModel->getAll();
        $postFiles = $this->postFileModel->getFiles($postId);
        $postTags = $this->postTagModel->getTags($postId);

        return $this->view->render($response, 'admin/post/edit.twig', [
            'post' => $post,
            'postTypes' => $postTypes,
            'postSeries' => $postSeries,
            'postFiles' => $postFiles,
            'postTags' => $postTags,
        ]);
    }

    public function galeria(Request $request, Response $response, array $args): Response
    {
        $postId = intval($args['id']);
        $post = $this->postModel->get($postId);
        $postFiles = $this->postFileModel->getFiles($postId);
        return $this->view->render($response, 'admin/post/galeria.twig', [
            'postId' => $postId,
            'post' => $post,
            'postFiles' => $postFiles
        ]);

    }
    public function removeImage(Request $request, Response $response, array $args): Response
    {
        $postId = intval($args['id']);
        $post = $this->postModel->get($postId);


        if (!$post) {
            $this->flash->addMessage('danger', "Mídia não encontrada.");
            return $this->httpRedirect($request, $response, '/admin/posts');
        }

        //se for do tipo imagem apaga a imagem
        if ($post->id_tipo_post == 1) {
            if (!unlink($post->destaque)) {
                $this->flash->addMessage('danger', "Não foi possível remover a imagem.");
                return $this->httpRedirect($request, $response, '/admin/posts/edit/' . $post->id);
            }
        }

        $post->destaque = null;
        $this->postModel->update($post);

        $this->flash->addMessage('success', "Imagem removida com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/posts/edit/' . $post->id);
    }

    public function removeImagemGaleria(Request $request, Response $response, array $args) {

        $postFileId = intval($args['id']);
        $postFile = $this->postFileModel->get($postFileId);

        if(!$postFileId) {
            $this->flash->addMessage('danger', "Imagem não encontrada.");
            return $this->httpRedirect($request, $response, '/admin/posts/');

        } else {
            $this->postFileModel->delete($postFileId);

            $this->flash->addMessage('success', "Imagem removida com sucesso.");
            return $this->httpRedirect($request, $response, '/admin/posts/galeria/' . $postFile->id_post);
        }
    }

    public function update(Request $request, Response $response): Response
    {
        $req =  $request->getParsedBody();

        $post = $this->entityFactory->createPost($request->getParsedBody());

        $files = $request->getUploadedFiles();

        // if destaque img is empty = no img uploaded
        if (!empty($files['destaque-img'])) {
            $image = $files['destaque-img'];
            if ($image->getError() === UPLOAD_ERR_OK) {

                //verify allowed extensions
                $filename = $image->getClientFilename();

                $allowedExtensions = [
                    'jpg',
                    'jpeg',
                    'gif',
                    'png'
                ];

                if (!in_array(pathinfo($filename,PATHINFO_EXTENSION), $allowedExtensions)) {
                    $this->flash->addMessage('danger', "Imagem em formato inválido.");
                    return $this->httpRedirect($request, $response, '/admin/posts/' . $post->id);
                }

                //verify size
                if ($image->getSize() > 400000) {
                    $this->flash->addMessage('danger', "Imagem muito grande (max 400kb).");
                    return $this->httpRedirect($request, $response, '/admin/posts/' . $post->id);
                }

                $filename = sprintf(
                    '%s.%s',
                    uniqid(),
                    pathinfo($image->getClientFilename(), PATHINFO_EXTENSION)
                );
                $path = 'upload/img/';
                $image->moveTo($path . $filename);
                $post->destaque = $path . $filename;

                //remove old img from disk
                if (file_exists($request->getParsedBody()['destaque_old'])) {
                    unlink($request->getParsedBody()['destaque_old']);
                }
            }


        } else {
            $post->destaque = $req['destaque_old'];
        }

        $postOld = $this->postModel->get((int)$post->id);
        $post->status = $postOld->status;

        $this->postModel->update($post);

        $this->flash->addMessage('success', "Mídia atualizadá com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/posts/' . $post->id);
    }

    public function view(Request $request, Response $response, array $args): Response
    {
        $postId = intval($args['id']);
        $post = $this->postModel->get($postId);
        if (!$post) {
            $this->flash->addMessage('danger', "Post não encontrado.");
            return $this->httpRedirect($request, $response, '/admin/courses');
        }

        $modules = $this->moduleModel->getModules((int)$post->id);
        if ($modules) {
            foreach ($modules as $module) {
                $module->levels = $this->levelModel->getLevels((int)$module->id);
            }
        }

        return $this->view->render($response, 'admin/posts/view.twig', [
            'post' => $post,
            'modules' => $modules
        ]);
    }






}
