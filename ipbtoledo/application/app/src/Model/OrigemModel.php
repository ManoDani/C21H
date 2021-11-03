<?php
declare(strict_types=1);

namespace Farol360\Vestibular2017\Model;

use Farol360\Vestibular2017\Model;
use Farol360\Vestibular2017\Model\Curso;

class OrigemModel extends Model
{
    public function add(Curso $origem)
    {
        $sql = "
            INSERT INTO origem (nome)
            VALUES (:nome)
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':nome' => $origem->nome
        ];
        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $origem): bool
    {
        $sql = "DELETE FROM origem WHERE id = :id";
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
                origem
            WHERE
                id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Origem::class);
        return $stmt->fetch();
    }

    public function getAll(): array
    {
        $sql = "
            SELECT
                *
            FROM
                origem
           ";
        $stmt = $this->db->prepare($sql);

        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Origem::class);
        return $stmt->fetchAll();
    }


    public function update(Inscricao $origem): bool
    {
        $sql = "
            UPDATE
                origem
            SET
                nome = :nome,
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':nome' => $origem->nome

        ];
        return $stmt->execute($parameters);
    }
}
