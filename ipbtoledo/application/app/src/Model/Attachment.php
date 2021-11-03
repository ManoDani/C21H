<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

class Attachment
{
    public $id;
    public $file_name;
    public $path;
    public $level_id;
    public $created_at;
    public $updated_at;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->file_name = $data['file_name'] ?? null;
        $this->path = $data['path'] ?? null;
        $this->level_id = $data['level_id'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }
}
