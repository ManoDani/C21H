<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Controller\Admin;

use Embera\Embera as Oembed;
use Farol360\AncoraEad\Controller;
use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\Model\EntityFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

class LevelController extends Controller
{
    protected $attachmentModel;
    protected $entityFactory;
    protected $levelModel;
    protected $moduleModel;
    protected $oembed;

    public function __construct(
        View $view,
        FlashMessages $flash,
        Model $level,
        Model $module,
        Model $attachment,
        Oembed $oembed,
        EntityFactory $entityFactory
    ) {
        parent::__construct($view, $flash);
        $this->levelModel = $level;
        $this->moduleModel = $module;
        $this->attachmentModel = $attachment;
        $this->oembed = $oembed;
        $this->entityFactory = $entityFactory;
    }

    public function add(Request $request, Response $response): Response
    {
        if (empty($request->getParsedBody())) {
            $moduleId = 0;
            if (!empty($request->getQueryParams())) {
                $moduleId = (int) $request->getQueryParams()['module'];
            }
            $modules = $this->moduleModel->getAll();
            return $this->view->render($response, 'admin/level/add.twig', [
                'modules' => $modules,
                'module_id' => $moduleId,
            ]);
        }

        $level = $this->entityFactory->createLevel($request->getParsedBody());
        $level->number = intval($this->levelModel->getGreatestNumber($level->module_id)['max_number']);
        $level->number++;
        $level->id = $this->levelModel->add($level);

        if ($level->id !== null) {
            $files = $request->getUploadedFiles();
            if (!empty($files['attachments'])) {
                foreach ($files['attachments'] as $attachmentFile) {
                    if ($attachmentFile->getError() === UPLOAD_ERR_OK) {
                        $fileName = $attachmentFile->getClientFilename();
                        $path = 'upload/' . uniqid();
                        $attachmentFile->moveTo($path);
                        $attachment = $this->entityFactory->createAttachment([
                            'file_name' => $fileName,
                            'path' => $path,
                            'level_id' => $level->id,
                        ]);
                        $this->attachmentModel->add($attachment);
                    }
                }
            }
            $this->flash->addMessage('success', "Nível adicionado com sucesso.");
            return $this->httpRedirect($request, $response, '/admin/modules/' . $level->module_id);
        }
        $this->flash->addMessage('danger', "Erro ao adicionar nível.");
        return $this->httpRedirect($request, $response, '/admin/courses');
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $id = intval($args['id']);
        $level = $this->levelModel->get($id);
        if (!empty($level)) {
            $this->levelModel->delete($level->id, (int)$level->module_id, (int)$level->number);
            $this->flash->addMessage('success', "Nível excluído com sucesso.");
            return $this->httpRedirect($request, $response, '/admin/modules/' . $level->module_id);
        } else {
            $this->flash->addMessage('danger', "Nível não existente.");
            return $this->httpRedirect($request, $response, '/admin/courses');
        }
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $id = intval($args['id']);
        $level = $this->levelModel->get($id);

        if (!$level) {
            $this->flash->addMessage('danger', "Nível não existente.");
            return $this->httpRedirect($request, $response, '/admin/courses');
        }

        $attachments = $this->attachmentModel->getAttachments((int)$level->id);

        if (!empty($args['action'])) {
            if ($args['action'] == 'up' && $level->number > 1) {
                $this->levelModel->swapUp((int)$level->id);
            } elseif ($args['action'] == 'down' &&
                $level->number < $this->levelModel->getGreatestNumber((int)$level->module_id)['max_number']
            ) {
                $this->levelModel->swapDown((int)$level->id);
            } elseif ($args['action'] == 'delete-attachment' && intval($args['attachment']) > 0) {
                $attachment = $this->attachmentModel->get(intval($args['attachment']));
                if (!unlink($attachment->path)) {
                    $this->flash->addMessage('danger', "Não foi possível remover anexo.");
                    return $this->httpRedirect($request, $response, '/admin/levels/edit/' . $level->id);
                }
                $this->attachmentModel->delete($attachment->id);
                $this->flash->addMessage('success', "Anexo removido com sucesso");
                return $this->httpRedirect($request, $response, '/admin/levels/edit/' . $level->id);
            } else {
                return $this->httpRedirect($request, $response, '/admin/modules/' . $level->module_id, 404);
            }
        }

        $modules = $this->moduleModel->getAll();

        return $this->view->render($response, 'admin/level/edit.twig', [
            'level' => $level,
            'modules' => $modules,
            'attachments' => $attachments,
        ]);
    }

    public function update(Request $request, Response $response): Response
    {
        $level = $this->entityFactory->createLevel($request->getParsedBody());

        $files = $request->getUploadedFiles();
        if (!empty($files['attachments'])) {
            foreach ($files['attachments'] as $attachmentFile) {
                if ($attachmentFile->getError() === UPLOAD_ERR_OK) {
                    $fileName = $attachmentFile->getClientFilename();
                    $path = 'upload/' . uniqid();
                    $attachmentFile->moveTo($path);
                    $attachment = $this->entityFactory->createAttachment([
                        'file_name' => $fileName,
                        'path' => $path,
                        'level_id' => $level->id,
                    ]);
                    $this->attachmentModel->add($attachment);
                }
            }
        }

        $this->levelModel->update($level);
        $this->flash->addMessage('success', "Nível editado com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/levels/' . $level->id);
    }

    public function view(Request $request, Response $response, array $args): Response
    {
        $id = intval($args['id']);
        $level = $this->levelModel->get($id);
        if (!$level) {
            $this->flash->addMessage('danger', "Nível não existente.");
            return $this->httpRedirect($request, $response, '/admin/courses');
        }

        $level->embed = $this->oembed->autoEmbed($level->video);

        $attachments = $this->attachmentModel->getAttachments((int)$level->id);

        return $this->view->render($response, 'admin/level/view.twig', [
            'level' => $level,
            'attachments' => $attachments,
        ]);
    }
}
