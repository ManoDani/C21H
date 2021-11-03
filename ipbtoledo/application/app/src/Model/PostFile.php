<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

/**
*
*/
class PostFile
{

    public $id;
    public $id_post;
    public $nome;
    public $file;


    function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->id_post = $data['id_post'] ?? null;
        $this->nome = $data['nome'] ?? null;
        $this->file = $data['file'] ?? null;

    }
}
