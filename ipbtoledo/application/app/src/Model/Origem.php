<?php
declare(strict_types=1);

namespace Farol360\Vestibular2017\Model;

class Origem
{
    public $id;
    public $nome;

    public function __construct(array $data = [])
    {
        $this->id             = $data['id']           ?? null;
        $this->nome           = !empty($data['nome']) ? strtolower($data['nome']) : null;

    }
}
