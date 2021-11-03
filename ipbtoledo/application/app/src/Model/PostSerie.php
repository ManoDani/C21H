<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

/**
*
*/
class PostSerie
{

    public $id;
    public $nome_serie;
    public $img_destaque;
    public $usr_date;
    public $slug;
    public $status;

    function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->nome_serie = $data['nome_serie'] ?? null;
        $this->img_destaque = $data['img_destaque'] ?? null;
        $this->usr_date = $data['usr_date'] ?? null;
        $this->slug = $data['slug'] ?? null;
        $this->status = $data['status'] ?? null;
    }
}
