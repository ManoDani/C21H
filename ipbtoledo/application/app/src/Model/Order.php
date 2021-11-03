<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

class Order
{
    public $id;
    public $reference;
    public $amount;
    public $transaction;
    public $status;
    public $raw;
    public $course_id;
    public $user_id;
    public $created_at;
    public $updated_at;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->reference = $data['reference'] ?? null;
        $this->amount = $data['amount'] ?? null;
        $this->transaction = $data['transaction'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->raw = $data['raw'] ?? null;
        $this->course_id = $data['course_id'] ?? null;
        $this->user_id = $data['user_id'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }
}
