<?php
declare(strict_types=1);

namespace Farol360\Vestibular2017\Model;

use Farol360\Vestibular2017\Model;
use Farol360\Vestibular2017\Model\Curso;

class LocalProvaModel extends Model
{
    public function add(Curso $local_prova)
    {
        $sql = "
            INSERT INTO local_prova (nome)
            VALUES (:nome)
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':nome' => $local_prova->nome
        ];
        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM local_prova WHERE id = :id";
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
                local_prova
            WHERE
                id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, LocalProva::class);
        return $stmt->fetch();
    }

    public function getAll(): array
    {
        $sql = "
            SELECT
                *
            FROM
                local_prova
           ";
        $stmt = $this->db->prepare($sql);

        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, LocalProva::class);
        return $stmt->fetchAll();
    }


    public function update(LocalProva $local_prova): bool
    {
        $sql = "
            UPDATE
                local_prova
            SET
                nome = :nome,
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':nome' => $local_prova->nome

        ];
        return $stmt->execute($parameters);
    }
}
