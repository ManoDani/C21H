<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

/**
*
*/
class Post
{

    public $id;
    public $id_tipo_post;
    public $data_cadastro;
    public $data_alteracao;
    public $id_autor;
    public $titulo;
    public $descricao;
    public $destaque;
    public $status;
    public $id_categoria;


    function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->id_tipo_post = $data['id_tipo_post'] ?? null;
        $this->data_cadastro = $data['data_cadastro'] ?? null;
        $this->data_alteracao = $data['data_alteracao'] ?? null;
        $this->id_autor = $data['id_autor'] ?? null;
        $this->titulo = $data['titulo'] ?? null;
        $this->descricao = $data['descricao'] ?? null;
        $this->destaque = $data['destaque'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->id_categoria = $data['id_categoria'] ?? null;

    }
}
