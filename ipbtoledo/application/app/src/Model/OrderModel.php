<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\Model\Order;

class OrderModel extends Model
{
    public function add(Order $order)
    {
        $sql = "
            INSERT INTO orders (reference, course_id, user_id, amount, transaction, status, raw)
            VALUES (:reference, :course_id, :user_id, :amount, :transaction, :status, :raw)
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':reference' => $order->reference,
            ':course_id' => $order->course_id,
            ':user_id' => $order->user_id,
            ':amount' => $order->amount,
            ':transaction' => $order->transaction,
            ':status' => $order->status,
            ':raw' => $order->raw,
        ];
        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM orders WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        return $stmt->execute($parameters);
    }

    public function get(int $id)
    {
        $sql = "
            SELECT
                id,
                reference,
                course_id,
                user_id,
                amount,
                transaction,
                status,
                raw,
                created_at,
                updated_at
            FROM
                orders
            WHERE
                id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Order::class);
        return $stmt->fetch();
    }

    public function getAll(int $offset = 0, int $limit = PHP_INT_MAX): array
    {
        $sql = "
            SELECT
                id,
                reference,
                course_id,
                user_id,
                amount,
                transaction,
                status,
                raw,
                created_at,
                updated_at
            FROM
                orders
            ORDER BY
                id DESC
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Order::class);
        return $stmt->fetchAll();
    }

    public function getOrders(int $offset = 0, int $limit = PHP_INT_MAX): array
    {
        $sql = "
            SELECT
                orders.id,
                reference,
                course_id,
                courses.title AS course_name,
                user_id,
                users.name AS user_name,
                amount,
                transaction,
                orders.status,
                raw,
                orders.created_at,
                orders.updated_at
            FROM
                orders
                LEFT JOIN courses ON courses.id = orders.course_id
                LEFT JOIN users ON users.id = orders.user_id
            WHERE
                transaction IS NOT NULL
            ORDER BY
                id DESC
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Order::class);
        return $stmt->fetchAll();
    }

    public function getByReference(string $reference)
    {
        $sql = "
            SELECT
                id,
                reference,
                course_id,
                user_id,
                amount,
                transaction,
                status,
                raw,
                created_at,
                updated_at
            FROM
                orders
            WHERE
                reference = :reference
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':reference' => $reference];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Order::class);
        return $stmt->fetch();
    }

    public function update(Order $order): bool
    {
        $sql = "
            UPDATE
                orders
            SET
                reference = :reference,
                course_id = :course_id,
                user_id = :user_id,
                amount = :amount,
                transaction = :transaction,
                status = :status,
                raw = :raw
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $order->id,
            ':reference' => $order->reference,
            ':course_id' => $order->course_id,
            ':user_id' => $order->user_id,
            ':amount' => $order->amount,
            ':transaction' => $order->transaction,
            ':status' => $order->status,
            ':raw' => $order->raw,
        ];
        return $stmt->execute($parameters);
    }
}
