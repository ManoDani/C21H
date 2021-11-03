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

class BannerController extends Controller
{

    protected $bannerModel;
    protected $entityFactory;

    public function __construct(
        View $view,
        FlashMessages $flash,
        Model $bannerModel,
        EntityFactory $entityFactory
    ) {
        parent::__construct($view, $flash);
        $this->bannerModel = $bannerModel;
        $this->entityFactory = $entityFactory;

    }


    public function add(Request $request, Response $response): Response
    {
        if (empty($request->getParsedBody())) {

            $date = date(date("Y-m-d"));

            return $this->view->render($response, 'admin/banners/add.twig',
                [
                'date' => $date
                ]);
        }

        else {
            $banner = $this->entityFactory->createBanner($request->getParsedBody());

            $banner->id = (int)$this->bannerModel->add($banner);

            //add img
            if ($banner->id !== null) {
                $files = $request->getUploadedFiles();

                if (!empty($files['image'])) {
                $image = $files['image'];
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
                        //remove atual banner
                        $this->bannerModel->delete($banner->id);
                        $this->flash->addMessage('danger', "Imagem em formato inválido.");
                        return $this->httpRedirect($request, $response, '/admin/banners/add');
                    }

                    //verify size
                    if ($image->getSize() > 2200000) {
                        //remove atual banner
                        $this->bannerModel->delete($banner->id);
                        $this->flash->addMessage('danger', "Imagem muito grande (max 400kb).");
                        return $this->httpRedirect($request, $response, '/admin/banners/add');
                    }

                    $filename = sprintf(
                        '%s.%s',
                        uniqid(),
                        pathinfo($image->getClientFilename(), PATHINFO_EXTENSION)
                    );
                    $path = 'upload/img/';
                    $image->moveTo($path . $filename);
                    $banner->image = $path . $filename;
                    $this->bannerModel->update($banner);
                }
            }
            }
        }
        $this->flash->addMessage('success', "Banner adicionado com Sucesso.");
        return $this->httpRedirect($request, $response, '/admin');
    }


    public function delete(Request $request, Response $response, array $args): Response
    {
        $bannerId = intval($args['id']);
        $banner = $this->bannerModel->get($bannerId);


        //1 - delete banner
        $this->bannerModel->delete($bannerId);

        //3 - Remove Imagem
        if (file_exists($banner->image)) {
             unlink($banner->image);
        }


        $this->flash->addMessage('success', "Banner removido com sucesso.");
        return $this->httpRedirect($request, $response, '/admin');
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $bannerId = intval($args['id']);
        $banner = $this->bannerModel->get($bannerId);

        //if ar in on route
        if (empty($request->getParsedBody())) {
            if (!$banner) {
                $this->flash->addMessage('danger', "Banner não encontrado.");
                return $this->httpRedirect($request, $response, '/admin');
            }

            return $this->view->render($response, 'admin/banners/edit.twig', [
                'banner' => $banner,
            ]);

        //if edited file
        } else {
            //get series from interface
            $banner = $this->entityFactory->createBanner($request->getParsedBody());


            $files = $request->getUploadedFiles();

            if (!empty($files['image'])) {

                //transfer image and save path
                $image = $files['image'];
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
                        return $this->httpRedirect($request, $response, '/admin/banners/add');
                    }

                    //verify size
                    if ($image->getSize() > 2200000) {
                        $this->flash->addMessage('danger', "Imagem muito grande (max 400kb).");
                        return $this->httpRedirect($request, $response, '/admin/banners/add');
                    }

                    $filename = sprintf(
                        '%s.%s',
                        uniqid(),
                        pathinfo($image->getClientFilename(), PATHINFO_EXTENSION)
                    );
                    $path = 'upload/img/';
                    $image->moveTo($path . $filename);
                    $banner->image = $path . $filename;
                }

                // if has no new image
                if (strlen($files['image']->file) == 0) {
                    $banner->image = $request->getParsedBody()['image_load'];
                } else {
                   //remove old img from disk
                    if (file_exists($request->getParsedBody()['image_load'])) {
                        unlink($request->getParsedBody()['image_load']);
                    }
                }

            }

            $this->bannerModel->update($banner);
            $this->flash->addMessage('success', "Banner alterado com sucesso.");
            return $this->httpRedirect($request, $response, '/admin');
        }
    }
}
