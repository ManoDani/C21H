<?php
declare(strict_types=1);

namespace Farol360\Vestibular2017\Model;

use Farol360\Vestibular2017\Model;
use Farol360\Vestibular2017\Model\Curso;

class CursoModel extends Model
{
    public function add(Curso $curso)
    {
        $sql = "
            INSERT INTO inscricao (nome)
            VALUES (:nome)
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':nome' => $inscricao->nome
        ];
        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $curso): bool
    {
        $sql = "DELETE FROM curso WHERE id = :id";
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
                curso
            WHERE
                id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Curso::class);
        return $stmt->fetch();
    }

    public function getAll(): array
    {
        $sql = "
            SELECT
                *
            FROM
                curso
           ";
        $stmt = $this->db->prepare($sql);

        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Curso::class);
        return $stmt->fetchAll();
    }

    public function update(Inscricao $curso): bool
    {
        $sql = "
            UPDATE
                curso
            SET
                nome = :nome,
                endereco = :endereco
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':nome' => $inscricao->nome

        ];
        return $stmt->execute($parameters);
    }
}
