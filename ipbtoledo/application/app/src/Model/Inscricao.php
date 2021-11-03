<?php
declare(strict_types=1);

namespace Farol360\Vestibular2017\Model;

class Inscricao
{
    public $id;
    public $nome;
    public $cpf;
    public $rg;
    public $cep;
    public $estado;
    public $cidade;
    public $bairro;
    public $rua;
    public $numero;
    public $complemento;
    public $curso;
    public $telefone1;
    public $telefone2;
    public $email;
    public $origem;
    public $especial;
    public $especial_txt;
    public $enem;
    public $enem_login;
    public $enem_senha;
    public $local_prova;
    public $data_cadastro;
    public $status;


    public function __construct(array $data = [])
    {
        $this->id             = $data['id']           ?? null;
        $this->nome           = !empty($data['nome']) ? strtolower($data['nome']) : null;
        $this->cpf           = !empty($data['cpf']) ? strtolower($data['nome']) : null;
        $this->rg           = !empty($data['rg']) ? strtolower($data['nome']) : null;
        $this->cep          = $data['cep']      ?? null;
        $this->estado        = $data['estado']      ?? null;
        $this->cidade        = $data['cidade']      ?? null;
        $this->bairro        = $data['bairro']      ?? null;
        $this->rua           = $data['rua']      ?? null;
        $this->numero        = $data['numero']      ?? null;
        $this->complemento    = $data['complemento']      ?? null;
        $this->curso          = $data['curso']        ?? null;
        $this->telefone1      = $data['telefone1']    ?? null;
        $this->telefone2      = $data['telefone2']    ?? null;
        $this->email          = $data['email']        ?? null;
        $this->origem         = $data['origem']       ?? null;
        $this->especial       = $data['especial']       ?? null;
        $this->especial_txt   = $data['especial_txt']       ?? null;
        $this->enem           = $data['enem'] ?? null;
        $this->enem_login     = $data['enem_login']       ?? null;
        $this->enem_senha     = $data['enem_senha']       ?? null;
        $this->local_prova    = $data['local_prova']       ?? null;
        $this->data_cadastro  = $data['data_cadastro']    ?? null;
        $this->status         = $data['status']       ?? null;
    }
}
