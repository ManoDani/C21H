<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

/**
*
*/
class PostTag
{

    public $id;
    public $id_post;
    public $nome;

    function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->id_post = $data['id_post'] ?? null;
        $this->nome = $data['nome'] ?? null;

    }
}
