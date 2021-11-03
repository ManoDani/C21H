<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

/**
*
*/
class Banner
{

    public $id;
    public $image;
    public $is_mobile;
    public $priority;
    public $upload_date;
    public $link;


    function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->image = $data['image'] ?? null;
        $this->is_mobile = $data['is_mobile'] ?? null;
        $this->priority = $data['priority'] ?? null;
        $this->upload_date = $data['upload_date'] ?? null;
        $this->link = $data['link'] ?? null;

    }
}
