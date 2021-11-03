<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

class Module
{
    public $id;
    public $number;
    public $title;
    public $description;
    public $course_id;
    public $created_at;
    public $updated_at;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->number = $data['number'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->course_id = $data['course_id'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }
}
