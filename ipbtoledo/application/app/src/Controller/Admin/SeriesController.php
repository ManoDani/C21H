<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Controller\Admin;

use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Farol360\AncoraEad\Model\EntityFactory;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

class SeriesController extends Controller
{

    protected $postModel;
    protected $postSerieModel;
    protected $entityFactory;

    public function __construct(
        View $view,
        FlashMessages $flash,
        Model $postModel,
        Model $postSerieModel,
        EntityFactory $entityFactory
    ) {
        parent::__construct($view, $flash);
        $this->postModel = $postModel;
        $this->postSerieModel = $postSerieModel;
        $this->entityFactory = $entityFactory;

    }


    public function add(Request $request, Response $response): Response
    {
        if (empty($request->getParsedBody())) {


            return $this->view->render($response, 'admin/series/add.twig');
        }

        else {
            $series = $this->entityFactory->createPostSerie($request->getParsedBody());


            $series->id = (int)$this->postSerieModel->add($series);

            //add img
            if ($series->id !== null) {
                $files = $request->getUploadedFiles();
                if (!empty($files['img_destaque'])) {
                $image = $files['img_destaque'];
                if ($image->getError() === UPLOAD_ERR_OK) {
                    $filename = sprintf(
                        '%s.%s',
                        uniqid(),
                        pathinfo($image->getClientFilename(), PATHINFO_EXTENSION)
                    );
                    $path = 'upload/img/';
                    $image->moveTo($path . $filename);
                    $series->img_destaque = $path . $filename;
                    $this->postSerieModel->update($series);
                }
            }
            }
        }
        $this->flash->addMessage('success', "Série adicionada com Sucesso.");
        return $this->httpRedirect($request, $response, '/admin/series');
    }

    public function alteraSerieAtual(Request $request, Response $response): Response
    {

        if(!empty($request->getParsedBody())) {
            $serieAtual->id = $request->getParsedBody();
            $this->postSerieModel->setSerieAtual($serieAtual->id['serieAtual']);
            $this->flash->addMessage('success', "Serie Atual alterada com sucesso.");
            return $this->httpRedirect($request, $response, '/admin/series');
        }

        $this->flash->addMessage('error', "Erro ao alterar Serie Atual.");
        return $this->httpRedirect($request, $response, '/admin/series');
    }

    public function alteraProgramaDevocional(Request $request, Response $response): Response
    {

        if(!empty($request->getParsedBody())) {
            $programaDevocional->id = $request->getParsedBody();
            $this->postSerieModel->setProgramaDevocional($programaDevocional->id['programaDevocional']);
            $this->flash->addMessage('success', "Programa Devocional Alterado com Sucesso.");
            return $this->httpRedirect($request, $response, '/admin/series');
        }

        $this->flash->addMessage('error', "Erro ao alterar Programa Devocional.");
        return $this->httpRedirect($request, $response, '/admin/series');
    }

    public function alteraSerieEspecial(Request $request, Response $response): Response
    {

        if(!empty($request->getParsedBody())) {
            $serieEspecial->id = $request->getParsedBody();
            $this->postSerieModel->setSerieEspecial($serieEspecial->id['serieEspecial']);
            $this->flash->addMessage('success', "Serie Espeial alterada com sucesso.");
            return $this->httpRedirect($request, $response, '/admin/series');
        }

        $this->flash->addMessage('error', "Erro ao alterar Serie Especial.");
        return $this->httpRedirect($request, $response, '/admin/series');
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $postSerieId = intval($args['id']);
        $postSerie = $this->postSerieModel->get($postSerieId);
        var_dump($postSerie);


        //1 - delete serie
        $this->postSerieModel->delete($postSerieId);

        //2 - remove id_category from post
        $this->postModel->deleteSerie($postSerieId);

        //3 - Remove Imagem
        if (file_exists($postSerie->img_destaque)) {
             unlink($postSerie->img_destaque);
        }


        $this->flash->addMessage('success', "Serie removida com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/series');
    }

    public function disable(Request $request, Response $response, array $args): Response
    {
        $postId = intval($args['id']);
        $this->postSerieModel->disable($postId);
        $this->flash->addMessage('success', "Série desabilitada com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/series');
    }

    public function enable(Request $request, Response $response, array $args): Response
    {
        $postId = intval($args['id']);
        $this->postSerieModel->enable($postId);
        $this->flash->addMessage('success', "Série habilitada com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/series');
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $postId = intval($args['id']);
        $post = $this->postSerieModel->get($postId);

        //if ar in on route
        if (empty($request->getParsedBody())) {
            if (!$post) {
                $this->flash->addMessage('danger', "Serie não encontrada.");
                return $this->httpRedirect($request, $response, '/admin/series');
            }

            return $this->view->render($response, 'admin/series/edit.twig', [
                'serie' => $post,
            ]);

        //if edited file
        } else {

            //get series from interface
            $series = $this->entityFactory->createPostSerie($request->getParsedBody());
            $files = $request->getUploadedFiles();


            if (!empty($files['img_destaque'])) {

                //transfer image and save path
                $image = $files['img_destaque'];
                if ($image->getError() === UPLOAD_ERR_OK) {
                    $filename = sprintf(
                        '%s.%s',
                        uniqid(),
                        pathinfo($image->getClientFilename(), PATHINFO_EXTENSION)
                    );
                    $path = 'upload/img/';
                    $image->moveTo($path . $filename);
                    $series->img_destaque = $path . $filename;
                }

                // if has no new image
                if (strlen($files['img_destaque']->file) == 0) {
                    $series->img_destaque = $request->getParsedBody()['img_destaque_load'];
                } else {
                   //remove old img from disk
                    if (file_exists($request->getParsedBody()['img_destaque_load'])) {
                        unlink($request->getParsedBody()['img_destaque_load']);
                    }
                }

            }

            $postSerieOld = $this->postSerieModel->get($postId);
            $series->status = $postSerieOld->status;

            $this->postSerieModel->update($series);
            $this->flash->addMessage('success', "Serie alterada com sucesso.");
            return $this->httpRedirect($request, $response, '/admin/series');
        }
    }

    public function series(Request $request, Response $response)
    {

        $params = $request->getQueryParams();
        if (!empty($params['page'])) {
            $page = intval($params['page']);
        } else {
            $page = 1;
        }
        $limit = 5;
        $offset = ($page - 1) * $limit;

        //get series
        $postSerie = $this->postSerieModel->getAll($offset, $limit);
        //total to seria atual use
        $postSerieTotal = $this->postSerieModel->getAll();

        foreach ($postSerie as $serie) {
            if($serie->usr_date != null) {
                $serie->usr_date = date("d-m-Y", strtotime($serie->usr_date));
            }

        }

        // Serie Atual part
        $serieAtualid = (int)$this->postSerieModel->getSerieAtual()->value_config;
        $serieAtualnome = $this->postSerieModel->get((int)$this->postSerieModel->getSerieAtual()->value_config);

        // Programa Devocional
        $programaDevocional->id = (int)$this->postSerieModel->getProgramaDevocional()->value_config;
        $programaDevocional = $this->postSerieModel->get($programaDevocional->id);

        // Serie Especial
        $serieEspecial->id = (int)$this->postSerieModel->getSerieEspecial()->value_config;
        $serieEspecial = $this->postSerieModel->get($serieEspecial->id);

        if (($serieAtualid == 0 ) || ($serieAtualid == '')) {
            $serieAtualnome->nome_serie ="Sem Categoria";
        }
        // --- end Serie Atual Part

        $amountPosts = $this->postSerieModel->getAmount();
        $amountPages = ceil($amountPosts->amount /$limit);

        return $this->view->render($response, 'admin/series/series.twig', [
            'postSerie' => $postSerie,
            'postSerieTotal' => $postSerieTotal,
            'serieAtualid' => $serieAtualid,
            'serieAtualnome' => $serieAtualnome->nome_serie,
            'programaDevocional' => $programaDevocional,
            'serieEspecial' => $serieEspecial,
            'page' => $page,
            'amountPages' => $amountPages

            ]);
    }
}
