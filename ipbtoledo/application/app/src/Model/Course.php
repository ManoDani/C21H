<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

class Course
{
    public $id;
    public $title;
    public $description;
    public $long_description;
    public $image;
    public $status;
    public $price;
    public $created_at;
    public $updated_at;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->long_description = $data['long_description'] ?? null;
        $this->image = $data['image'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->price = $data['price'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }
}
