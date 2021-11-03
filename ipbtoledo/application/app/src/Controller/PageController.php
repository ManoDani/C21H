<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Controller;

use Farol360\AncoraEad\Controller;
use Farol360\AncoraEad\Mailer;
use Farol360\AncoraEad\Model;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;
use Embera\Embera as Oembed;
use Embera\Formatter as Formatter;


class PageController extends Controller
{
    protected $mailer;
    protected $bannerModel;
    protected $postModel;
    protected $postSerieModel;
    protected $oembed;

    public function __construct(View $view, FlashMessages $flash, Mailer $mailer, Model $bannerModel,Model $postModel, Model $postSerieModel, Oembed $oembed)
    {
        parent::__construct($view, $flash);
        $this->mailer = $mailer;
        $this->bannerModel = $bannerModel;
        $this->postModel = $postModel;
        $this->postSerieModel = $postSerieModel;
        $this->oembed = $oembed;
    }

    public function contato(Request $request, Response $response): Response
    {
        if (empty($request)) {
            return $this->view->render($response, 'page/contato.twig');
        } else {
            if (empty($request->getParsedBody())) {
                return $this->view->render($response, 'page/contato.twig');
            } else {
                $texto =
                    "Olá!\n
                    Alguém entrou em contato \n\n
                    -----DADOS----- \n
                    NOME:" . $request->getParsedBody()['nome'] . "\n
                    EMAIL: " . $request->getParsedBody()['email'] . "\n
                    TELEFONE: " . $request->getParsedBody()['telefone'] . "
                    \n\n
                    CORPO DA MENSAGEM: " . $request->getParsedBody()['corpo-email'];

                $this->mailer->send(
                    $request->getParsedBody()['nome'],
                    $request->getParsedBody()['email'],
                    "Contato via Website",
                    $texto
                );

                return $this->httpRedirect($request, $response, '/obrigado');
            }
        }
    }

    public function contatoObrigado(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'page/contato_obrigado.twig');
    }

    public function cuidado_pastoral(Request $request, Response $response): Response
    {

        $tipo = 0;

        return $this->view->render($response, 'page/cuidado_pastoral.twig', [
            'tipo' => $tipo
        ]);
    }

    public function cuidado_pastoral_pedido(Request $request, Response $response): Response
    {
        $tipo = 1;
        $erro = false;
        if (empty($request)) {

            return $this->view->render($response, 'page/cuidado_pastoral_pedido.twig', [
            'tipo' => $tipo
            ]);
        } else {
            if (empty($request->getParsedBody())) {
                return $this->view->render($response, 'page/cuidado_pastoral_pedido.twig', [
                    'tipo' => $tipo
                ]);
            } else {

                $texto =
                    "Olá!\n
                    Foi feito contato no website através da aba 'Cuidado Pastoral - PEDIDO DE ORAÇÃO' \n
                    -----DADOS----- \n
                    NOME:";

                if ($request->getParsedBody()['nome']) {

                    $texto .= $request->getParsedBody()['nome'];
                    $texto .= "\n";
                }

                if ($request->getParsedBody()['nome'] == '') {
                    $erro = "nome";
                }

                $texto .= "EMAIL: ";

                if ($request->getParsedBody()['email']) {
                    $texto .= $request->getParsedBody()['email'];
                    $texto .= "\n";
                }

                $texto .= "TELEFONE: ";

                if ($request->getParsedBody()['telefone']) {
                    $texto .= $request->getParsedBody()['telefone'];
                    $texto .= "\n\n";
                }

                $texto .= "CORPO DA MENSAGEM: ";

                if ($request->getParsedBody()['corpo-email']) {
                    $texto .= $request->getParsedBody()['corpo-email'];

                }

                if (!$erro) {
                    $this->mailer->send(
                    'Contato Site - PEDIDO ORAÇÃO',
                    'contato@ipbtoledo.com.br',
                    'website IPB TOLEDO',
                    $texto
                    );

                    return $this->view->render($response, 'page/contato_obrigado.twig');
                } else {
                    return $this->view->render($response, 'page/cuidado_pastoral_pedido.twig', [
                        'erro' => $erro,
                        'tipo' => $tipo
                    ]);
                }
            }
        }
    }

    public function cuidado_pastoral_aconselhamento(Request $request, Response $response): Response
    {

        $tipo = 2;
        $erro = false;
        if (empty($request)) {
            return $this->view->render($response, 'page/cuidado_pastoral_aconselhamento.twig', [
            'tipo' => $tipo
            ]);
        } else {
            if (empty($request->getParsedBody())) {
                return $this->view->render($response, 'page/cuidado_pastoral_aconselhamento.twig', [
                    'tipo' => $tipo
                ]);
            } else {
                $texto =
                    "Olá!\n
                    Foi feito contato no website através da aba 'Cuidado Pastoral - ACONSELHAMENTO' \n
                    -----DADOS----- \n
                    NOME:";

                if ($request->getParsedBody()['nome']) {
                    $texto .= $request->getParsedBody()['nome'];
                    $texto .= "\n";
                }

                if ($request->getParsedBody()['nome'] == '') {
                    $erro = "nome";
                }

                $texto .= "EMAIL: ";

                if ($request->getParsedBody()['email']) {
                    $texto .= $request->getParsedBody()['email'];
                    $texto .= "\n";
                }

                if ($request->getParsedBody()['email'] == '') {
                    $erro = "email";
                }

                $texto .= "TELEFONE: ";

                if ($request->getParsedBody()['telefone']) {
                    $texto .= $request->getParsedBody()['telefone'];
                    $texto .= "\n\n";
                }

                if ($request->getParsedBody()['telefone'] == '') {
                    $erro = "telefone";
                }

                $texto .= "CORPO DA MENSAGEM: ";

                if ($request->getParsedBody()['corpo-email']) {
                    $texto .= $request->getParsedBody()['corpo-email'];
                }

                if (!$erro) {
                    $this->mailer->send(
                    'Contato Site - ACONSELHAMENTO',
                    'contato@ipbtoledo.com.br',
                    'website IPB TOLEDO',
                    $texto
                    );

                    return $this->view->render($response, 'page/contato_obrigado.twig');
                } else {
                    return $this->view->render($response, 'page/cuidado_pastoral_aconselhamento.twig', [
                        'erro' => $erro,
                        'tipo' => $tipo
                    ]);
                }
            }
        }
    }

    public function cuidado_pastoral_outros(Request $request, Response $response): Response
    {
        if (empty($request)) {
            return $this->view->render($response, 'page/cuidado_pastoral_outros.twig');
        } else {
            if (empty($request->getParsedBody())) {
                return $this->view->render($response, 'page/cuidado_pastoral_outros.twig');
            } else {
                $texto =
                    "Olá!\n
                    Foi feito contato no website através da aba 'Cuidado Pastoral - Outros Contatos' \n\n
                    -----DADOS----- \n
                    NOME:" . $request->getParsedBody()['nome'] . "\n
                    EMAIL: " . $request->getParsedBody()['email'] . "\n
                    TELEFONE: " . $request->getParsedBody()['telefone'] . "
                    \n\n
                    CORPO DA MENSAGEM: " . $request->getParsedBody()['corpo-email'];

                $this->mailer->send(
                    'Contato via Website',
                    'contato@ipbtoledo.com.br',
                    $request->getParsedBody()['nome'],
                    $texto
                );
                return $this->view->render($response, 'page/contato_obrigado.twig');
            }
        }
    }

    public function hash(Request $request, Response $response): Response {

        $pass = password_hash('IPB2966toledo', PASSWORD_DEFAULT) ;
        echo $pass;
        $token = bin2hex(random_bytes(16));
        echo "!!" . $token;
        return $this->view->render($response, 'page/hash.twig');
    }

    public function igreja(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'page/igreja.twig');
    }

    public function igrejaHistoria(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'page/igreja.twig');
    }

    public function igrejaLiderancas(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'page/lideranca.twig');
    }

    public function index(Request $request, Response $response): Response
    {
        $banners = $this->bannerModel->getAll();

        return $this->view->render($response, 'page/index.twig', ['banners' =>$banners]);
    }

    public function manutencao(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'page/manutencao.twig');
    }

    public function mensagem_atual(Request $request, Response $response): Response
    {
        $serie_atual = $this->postSerieModel->getSerieAtual();

        $id = (int)$serie_atual->value_config;

        $serie_atual = $this->postSerieModel->get($id);
        //var_dump($serie_atual);
        //die;

        return $this->httpRedirect($request, $response, '/series/'."$serie_atual->slug");
    }

    public function mensagens_series(Request $request, Response $response): Response
    {

        $params = $request->getQueryParams();

        if (!empty($params['page'])) {
            $page = intval($params['page']);
        } else {
            $page = 1;
        }
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // get serie atual
        $serie_atual = $this->postSerieModel->getSerieAtual();
        $serie_atual = $this->postSerieModel->get($serie_atual->slug);

        // get serie atual posts
        $serie_atual_posts = $this->postModel->getByPostSerie((int) $serie_atual->id);

        // prepare embeded for serie_atual_posts
        foreach ($serie_atual_posts as $post)
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

        // get programa devocional
        $programa_devocional = $this->postSerieModel->getProgramaDevocional();
        $programa_devocional = $this->postSerieModel->get($programa_devocional->value_config);

        // get programa devocional posts
        $programa_devocional_posts = $this->postModel->getByPostSerie((int) $programa_devocional->id);

        // prepare for serie_especial_posts
        foreach ($programa_devocional_posts as $post) {
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

        // get serie especial
        $serie_especial = $this->postSerieModel->getSerieEspecial();
        $serie_especial = $this->postSerieModel->get($serie_especial->value_config);

        // get serie especial posts
        $serie_especial_posts = $this->postModel->getByPostSerie((int) $serie_especial->id);

        // prepare for serie_especial_posts
        foreach ($serie_especial_posts as $post) {
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

        // get all mensagens series
        $mensagens_series = $this->postSerieModel->getAll($offset, $limit);

        $amountPosts = $this->postSerieModel->getAmount();
        $amountPages = ceil($amountPosts->amount /$limit);

        return $this->view->render($response, 'page/mensagens_series.twig',
            [
            'mensagens_series'
                => $mensagens_series,
            'page'
                => $page,
            'amountPages'
                => $amountPages,
            'serie_atual'
                => $serie_atual,
            'serie_atual_posts'
                => $serie_atual_posts,
            'programa_devocional'
                => $programa_devocional,
            'programa_devocional_posts'
                => $programa_devocional_posts,
            'serie_especial'
                => $serie_especial,
            'serie_especial_posts'
                => $serie_especial_posts
            ]);
    }

    public function midia(Request $request, Response $response): Response
    {


        return $this->view->render($response, 'page/midia.twig');
    }

    public function oracao(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'page/oracao.twig');
    }

    public function onde_estamos(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'page/onde_estamos.twig');
    }

    public function serie_especial(Request $request, Response $response): Response
    {
        $serie_atual = $this->postSerieModel->getSerieEspecial();

        $id = (int)$serie_atual->value_config;

        $serie_atual = $this->postSerieModel->get($id);

        return $this->httpRedirect($request, $response, '/series/'."$serie_atual->slug");
    }

}
