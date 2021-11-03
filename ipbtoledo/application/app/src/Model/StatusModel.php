<?php
declare(strict_types=1);

namespace Farol360\Vestibular2017\Model;

use Farol360\Vestibular2017\Model;
use Farol360\Vestibular2017\Model\Status;

class StatusModel extends Model
{
    public function add(Status $status)
    {
        $sql = "
            INSERT INTO status (descricao)
            VALUES (:descricao)
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':descricao' => $inscricao->descricao
        ];
        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $descricao): bool
    {
        $sql = "DELETE FROM status WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        return $stmt->execute($parameters);
    }

    public function get(int $id)
    {
        $sql = "
            SELECT
                *
            FROM
                status
            WHERE
                id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Status::class);
        return $stmt->fetch();
    }

    public function getAll(): array
    {
        $sql = "
            SELECT
                *
            FROM
                status
           ";
        $stmt = $this->db->prepare($sql);

        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Curso::class);
        return $stmt->fetchAll();
    }

    public function update(Status $status): bool
    {
        $sql = "
            UPDATE
                status
            SET
                descricao = :descricao
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':descricao' => $status->descricao

        ];
        return $stmt->execute($parameters);
    }
}
