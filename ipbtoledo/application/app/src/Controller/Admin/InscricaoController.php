<?php
declare(strict_types=1);

namespace Farol360\Vestibular2017\Controller\Admin;

use Farol360\Vestibular2017\Controller;
use Farol360\Vestibular2017\Model;
use Farol360\Vestibular2017\Model\EntityFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

use Fusonic\SpreadsheetExport\Spreadsheet;
use Fusonic\SpreadsheetExport\ColumnTypes\DateColumn;
use Fusonic\SpreadsheetExport\ColumnTypes\NumericColumn;
use Fusonic\SpreadsheetExport\ColumnTypes\TextColumn;
use Fusonic\SpreadsheetExport\Writers\CsvWriter;

class InscricaoController extends Controller
{
    protected $entityFactory;
    protected $postFileModel;
    protected $inscricaoModel;
    protected $cursoModel;
    protected $statusModel;
    protected $localProvaModel;
    protected $origemModel;

    public function __construct(
        View $view,
        FlashMessages $flash,
        EntityFactory $entityFactory,
        Model $inscricao,
        Model $curso,
        Model $status,
        Model $localProva,
        Model $origem

    ) {
        parent::__construct($view, $flash);
        $this->entityFactory = $entityFactory;
        $this->inscricaoModel = $inscricao;
        $this->cursoModel = $curso;
        $this->statusModel = $status;
        $this->localProvaModel = $localProva;
        $this->origemModel = $origem;

    }


    //lista de inscricoes
    public function index(Request $request, Response $response): Response
    {

        //carregar infos
        $offset = 0;
        $limit = 50;
        $inscricao = $this->inscricaoModel->getAll($offset, $limit);
        $cursos = $this->cursoModel->getAll();
        $status = $this->statusModel->getAll();


        return $this->view->render($response, 'admin/inscricao/index.twig', [
            'inscricoes' => $inscricao,
            'cursos' => $cursos,
            'statuses' => $status
        ]);
    }

    public function export($request, $response, $args)
    {
        $export = new Spreadsheet();

        $export->addColumn(new DateColumn("Data do cadastro"));
        $export->addColumn(new TextColumn("ID"));
        $export->addColumn(new TextColumn("Nome"));
        $export->addColumn(new TextColumn("CPF"));
        $export->addColumn(new TextColumn("RG"));
        $export->addColumn(new TextColumn("Email"));
        $export->addColumn(new TextColumn("Telefone 1"));
        $export->addColumn(new TextColumn("Telefone 2"));
        $export->addColumn(new TextColumn("CEP"));
        $export->addColumn(new TextColumn("Estado"));
        $export->addColumn(new TextColumn("Cidade"));
        $export->addColumn(new TextColumn("Bairro"));
        $export->addColumn(new TextColumn("Rua"));
        $export->addColumn(new TextColumn("Número"));
        $export->addColumn(new TextColumn("Complemento"));
        $export->addColumn(new TextColumn("Opção de Curso"));
        $export->addColumn(new TextColumn("Local de Prova"));
        $export->addColumn(new TextColumn("Portador de Necessidades?"));
        $export->addColumn(new TextColumn("Qual?"));
        $export->addColumn(new TextColumn("Nota Enem?"));
        $export->addColumn(new TextColumn("Usuário Enem"));
        $export->addColumn(new TextColumn("Senha Enem"));
        $export->addColumn(new TextColumn("Como Ficou Sabendo?"));


        $inscricoes = $this->inscricaoModel->getAll();

        foreach ($inscricoes as $inscricao) {

            if ($inscricao->especial == '0' ) {
                $inscricao->especial = 'não';
            } elseif ($inscricao->especial == '1') {
                $inscricao->especial = 'sim';
            }
            $inscricao->id = str_pad($inscricao->id, 10, "0", STR_PAD_LEFT);

            $export->addRow([
                $inscricao->data_cadastro,
                $inscricao->id,
                $inscricao->nome,
                $inscricao->cpf,
                $inscricao->rg,
                $inscricao->email,
                $inscricao->telefone1,
                $inscricao->telefone2,
                $inscricao->cep,
                $inscricao->estado,
                $inscricao->cidade,
                $inscricao->bairro,
                $inscricao->rua,
                $inscricao->numero,
                $inscricao->complemento,
                $inscricao->curso_nome = $this->cursoModel->get(intval($inscricao->curso))->nome,
                $inscricao->local_prova_nome = $this->localProvaModel->get(intval($inscricao->local_prova))->nome,
                $inscricao->especial,
                $inscricao->especial_txt,
                $inscricao->enem,
                $inscricao->enem_login,
                $inscricao->enem_senha,
                $inscricao->origem_txt = $this->origemModel->get(intval($inscricao->origem))->nome
            ]);
        }

        $writer = new CsvWriter();
        $writer->includeColumnHeaders = true;

        $export->download($writer, "Inscricoes-" . time());
    }
}
