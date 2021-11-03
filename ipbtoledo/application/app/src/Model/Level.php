<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

class Level
{
    public $id;
    public $number;
    public $title;
    public $content;
    public $video;
    public $module_id;
    public $created_at;
    public $updated_at;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->number = $data['number'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->content = $data['content'] ?? null;
        $this->video = $data['video'] ?? null;
        $this->video = !empty($data['video']) ? $data['video'] : null;
        $this->module_id = $data['module_id'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }
}
