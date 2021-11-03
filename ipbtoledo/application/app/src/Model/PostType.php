<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

/**
*
*/
class PostType
{

    public $id;
    public $nome;

    function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->nome = $data['nome'] ?? null;
    }
}
