<?php
declare(strict_types=1);

namespace Farol360\Vestibular2017\Model;

class Status
{
    public $id;
    public $descricao;

    public function __construct(array $data = [])
    {
        $this->id             = $data['id']           ?? null;
        $this->descricao           = !empty($data['descricao']) ? strtolower($data['descricao']) : null;

    }
}
