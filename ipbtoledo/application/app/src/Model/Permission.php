<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

class Permission
{
    public $id;
    public $resource;
    public $description;
    public $role_id;
    public $created_at;
    public $updated_at;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->resource = !empty($data['resource']) ? strtolower($data['resource']) : null;
        $this->description = $data['description'] ?? null;
        $this->role_id = $data['role_id'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }
}
