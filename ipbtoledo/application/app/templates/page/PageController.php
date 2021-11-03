<?php
declare(strict_types=1);

namespace Farol360\Vestibular2017\Controller;

use Farol360\Vestibular2017\Controller;
use Farol360\Vestibular2017\Mailer;
use Farol360\Vestibular2017\Model;
use Farol360\Vestibular2017\Model\Inscricao;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

class PageController extends Controller
{
    protected $mailer;
    protected $pageStatisticModel;
    protected $inscricaoModel;
    protected $cursoModel;
    protected $origemModel;
    protected $localProvaModel;

    public function __construct(View $view, FlashMessages $flash, Mailer $mailer, Model $pageStatistic, Model $inscricao, Model $curso, Model $origem, Model $localProva)
    {
        parent::__construct($view, $flash);
        $this->mailer = $mailer;
        $this->pageStatisticModel = $pageStatistic;
        $this->inscricaoModel = $inscricao;
        $this->cursoModel = $curso;
        $this->origemModel = $origem;
        $this->localProvaModel = $localProva;
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


    public function index(Request $request, Response $response): Response
    {

        //incrementa estatísticas da página
        $pageStatistic = $this->pageStatisticModel->get(1);
        $pageStatistic->page_statistic_value = intval($pageStatistic->page_statistic_value) + 1;
        $pageStatistic->page_statistic_value = strval($pageStatistic->page_statistic_value);
        $this->pageStatisticModel->update($pageStatistic);

        //carrega as origens
        $origens = $this->origemModel->getAll();

        //carrega os locais de prova
        $locaisProva = $this->localProvaModel->getAll();

        // se não vier do form
        if (empty($request->getParsedBody())) {
            return $this->view->render($response, 'page/index.twig', [
            'page_statistic' => $pageStatistic,
            'origens' => $origens,
            'locaisProva' => $locaisProva]);
        } else {

            $error = false;

            //Receber dados do form
            if (!empty($request->getParsedBody()['termos'])) {
                $inscricao = new Inscricao();

                if (!empty($request->getParsedBody()['nome'])) {
                    $inscricao->nome       = $request->getParsedBody()['nome'];
                } else {
                    $error = true;
                }

                if (!empty($request->getParsedBody()['cpf'])) {
                    $inscricao->cpf       = $request->getParsedBody()['cpf'];
                } else {
                    $error = true;
                }

                if (!empty($request->getParsedBody()['rg'])) {
                    $inscricao->rg       = $request->getParsedBody()['rg'];
                } else {
                    $error = true;
                }

                if (!empty($request->getParsedBody()['cursos'])) {
                    $inscricao->curso       = intval($request->getParsedBody()['cursos']);
                    $inscricao->curso_descricao = $this->cursoModel->get(intval($request->getParsedBody()['cursos']))->nome;
                } else {
                    $error = true;
                }

                if (!empty($request->getParsedBody()['cep'])) {
                    $inscricao->cep = $request->getParsedBody()['cep'];
                } else {
                    $error = true;
                }

                if (!empty($request->getParsedBody()['estado'])) {
                    $inscricao->estado = $request->getParsedBody()['estado'];
                } else {
                    $error = true;
                }

                if (!empty($request->getParsedBody()['cidade'])) {
                    $inscricao->cidade = $request->getParsedBody()['cidade'];
                } else {
                    $error = true;
                }

                if (!empty($request->getParsedBody()['bairro'])) {
                   $inscricao->bairro = $request->getParsedBody()['bairro'];
                } else {
                    $error = true;
                }

                if (!empty($request->getParsedBody()['rua'])) {
                   $inscricao->rua = $request->getParsedBody()['rua'];
                } else {
                    $error = true;
                }

                if (!empty($request->getParsedBody()['numero'])) {
                   $inscricao->numero = $request->getParsedBody()['numero'];
                } else {
                    $error = true;
                }
                $inscricao->complemento = $request->getParsedBody()['complemento'];
                if (!empty($request->getParsedBody()['telefone1'])) {
                   $inscricao->telefone1 = $request->getParsedBody()['telefone1'];
                } else {
                    $error = true;
                }
                $inscricao->telefone2 = $request->getParsedBody()['telefone2'];
                if (!empty($request->getParsedBody()['email'])) {
                   $inscricao->email = $request->getParsedBody()['email'];
                } else {
                    $error = true;
                }
                if (!empty($request->getParsedBody()['origem'])) {
                   $inscricao->origem = $request->getParsedBody()['origem'];
                } else {
                    $error = true;
                }

                if (!empty($request->getParsedBody()['especial'])) {
                    if ($request->getParsedBody()['especial'] == 'sim') {
                        $inscricao->especial = 1;
                        $inscricao->especial_nome = 'sim';
                        if (!empty($request->getParsedBody()['especial_txt'])) {
                            $inscricao->especial_txt = $request->getParsedBody()['especial_txt'];
                        } else {
                            $error = true;
                        }
                    }
                    if ($request->getParsedBody()['especial'] == 'nao') {
                        $inscricao->especial = 0;
                        $inscricao->especial_nome = 'não';
                        $inscricao->especial_txt = 'não informado.';
                    }
                } else {
                    $error = true;
                }
                if (!empty($request->getParsedBody()['localProva'])) {
                   $inscricao->localProva = $request->getParsedBody()['localProva'];
                    $inscricao->localProva_descricao = $this->localProvaModel->get(intval($request->getParsedBody()['localProva']))->nome;
                } else {
                    $error = true;
                }

                if ($error == true) {
                    $this->flash->addMessage('danger', "Por favor preencha todos os campos obrigatórios.");
                    return $this->httpRedirect($request, $response, '/');
                }

                //salva a inscrição
                //adiciona a mascara para apresentar com zeros à esquerda
                $inscricao->id = str_pad($this->inscricaoModel->add($inscricao), 10, "0", STR_PAD_LEFT);

                //enviar email ao administrador informando novo cadastro de participante
                $inscricao_email = $this->inscricaoModel->get(intval($inscricao->id));
                $inscricao_email->curso_descricao = $this->cursoModel->get(intval($inscricao_email->curso))->nome;
                $inscricao_email->origem_descricao = $this->origemModel->get(intval($inscricao_email->origem))->nome;
                $inscricao_email->localProva_descricao = $this->localProvaModel->get(intval($inscricao_email->local_prova))->nome;

                $texto = "  <meta charset=\"UTF-8\">
                            <div style=\" font-size:large; \">
                            <p>Olá!</p>
                            <p>Uma nova inscrição foi recebida no Hotsite do Vestibular:</p>
                           <p><strong>Data e hora:</strong> " . $inscricao_email->data_cadastro . "</p>
                            <p><strong>Número de Inscrição:</strong> " . str_pad($inscricao_email->id, 10, "0", STR_PAD_LEFT) . "</p>
                            <p><strong>Nome:</strong> " .$inscricao_email->nome . "</p>
                            <p><strong>CPF:</strong> " .$inscricao_email->cpf . "</p>
                            <p><strong>RG:</strong> " .$inscricao_email->rg . "</p>
                            <p><strong>Email:</strong> " . $inscricao_email->email . "</p>
                            <p><strong>Telefone 1:</strong> " . $inscricao_email->telefone1 . "</p>
                            <p><strong>Telefone 2:</strong> " . $inscricao_email->telefone2 . "</p>
                            <p><strong>Curso:</strong> " . $inscricao_email->curso_descricao . "</p>
                            <p><strong>Local de Prova:</strong> " . $inscricao_email->localProva_descricao . "</p>
                            <p><strong>Portador de Necessidades Especiais?</strong> " . $inscricao_email->especial_nome . "</p>
                            <p><strong>Qual?</strong> " . $inscricao_email->especial_txt . "</p>
                            <p><strong>Como soube do vestibular:</strong> " . $inscricao_email->origem_descricao . "</p>
                            <p>--------------- ENDEREÇO --------</p>
                            <p><strong>CEP:</strong> " . $inscricao_email->cep . "</p>
                            <p><strong>Estado:</strong> " . $inscricao_email->estado . "</p>
                            <p><strong>Cidade:</strong> " . $inscricao_email->cidade . "</p>
                            <p><strong>Bairro:</strong> " . $inscricao_email->bairro . "</p>
                            <p><strong>Rua:</strong> " . $inscricao_email->rua . "</p>
                            <p><strong>Número:</strong> " . $inscricao_email->numero . "</p>
                            <p><strong>Complemento:</strong> " . $inscricao_email->complemento . "</p>
                            <p>----------------------------------
                            <p>O participante já recebeu um email de confirmação e está aguardando o boleto por email.</p>
                            <p>Não esqueça de acessar o site <a href=\"http://vestibular.iseperondon.com.br/admin\">vestibular.iseperondon.com.br/admin</a> para alterar o status do pedido.</p>
                            </div>
                            ";


                $this->mailer->send(
                    'Vestibular Isepe',
                    'vestibular@iseperondon.com.br',
                    "Nova Inscrição via Hotsite",
                    $texto
                );

                //envia email ao participante agradecendo cadastro e aguardando instruções de pagamento
                $texto2 = " <meta charset=\"UTF-8\">
                            <div style=\" font-size:large; \">
                            <p>Olá! </p>
                            <p>Sua inscricao no vestibular Isepe Rondon foi realizada com sucesso.</p>
                            <p><strong>Curso: </strong>". $inscricao_email->curso_descricao . ".</p>
                            <p><strong>Local de Prova: </strong>". $inscricao_email->localProva_descricao . ".</p>
                            <p><strong>Portador de Necessidades Especiais?</strong> " . $inscricao_email->especial_nome . "</p>
                            <p><strong>Qual?</strong> " . $inscricao_email->especial_txt . "</p>
                            <p><strong>Status: </strong>Em Aberto.</p>
                            <p><strong>Número de inscrição: </strong>". str_pad($inscricao_email->id, 10, "0", STR_PAD_LEFT) .". </p>
                            <p><strong>Nome: </strong>". $inscricao_email->nome . ".</p>
                            <p><strong>CPF: </strong>". $inscricao_email->cpf . ".</p>
                            <p><strong>RG: </strong>". $inscricao_email->rg . ".</p>
                            <p><strong>Email: </strong>". $inscricao_email->email . ".</p>
                            <p><strong>Telefone 1: </strong>". $inscricao_email->telefone1 . ".</p>
                            <p><strong>Telefone 2: </strong>". $inscricao_email->telefone2 . ".</p>
                            <p>Em breve enviaremos um boleto neste email. Fique atento. Em caso de dúvidas entre em contato:</p>
                            <p>Telefone: (45) 3254-6476</p>
                            </div>";
                $this->mailer->send(
                    'Vestibular Isepe',
                    $inscricao_email->email,
                    "Sua Inscrição no Vestibular Isepe Rondon",
                    $texto2
                );


                return $this->view->render($response, 'page/contato_obrigado.twig', [
                'page_statistic' => $pageStatistic,
                'inscricao' => $inscricao]);
            }


            return $this->view->render($response, 'page/index.twig', [
                'page_statistic' => $pageStatistic]);

        }




    }

    public function manutencao(Request $request, Response $response): Response
    {


        return $this->view->render($response, 'page/manutencao.twig');
    }



}
