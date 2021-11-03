<?php
declare(strict_types=1);

namespace Farol360\Vestibular2017\Model;

use Farol360\Vestibular2017\Model;
use Farol360\Vestibular2017\Model\Inscricao;

class InscricaoModel extends Model
{
    public function add(Inscricao $inscricao)
    {
        $sql = "
            INSERT INTO inscricao (nome, cpf, rg, cep, estado, cidade, bairro, rua, numero, complemento, curso, telefone1, telefone2, email, origem, especial, especial_txt, enem,  enem_login, enem_senha, local_prova, data_cadastro, status)
            VALUES (:nome, :cpf, :rg, :cep, :estado, :cidade, :bairro, :rua, :numero, :complemento, :curso, :telefone1, :telefone2, :email, :origem, :especial, :especial_txt, :enem, :enem_login, :enem_senha, :local_prova, :data_cadastro, :status)
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':nome' => $inscricao->nome,
            ':cpf' => $inscricao->cpf,
            ':rg' => $inscricao->rg,
            ':cep' => $inscricao->cep,
            ':estado' => $inscricao->estado,
            ':cidade' => $inscricao->cidade,
            ':bairro' => $inscricao->bairro,
            ':rua' => $inscricao->rua,
            ':numero' => $inscricao->numero,
            ':complemento' => $inscricao->complemento,
            ':curso' => $inscricao->curso,
            ':telefone1' => $inscricao->telefone1,
            ':telefone2' => $inscricao->telefone2,
            ':email' => $inscricao->email,
            ':origem' => $inscricao->origem,
            ':especial' => intval($inscricao->especial),
            ':especial_txt' => $inscricao->especial_txt,
            ':enem' => $inscricao->enem,
            ':enem_login' => $inscricao->enem_login,
            ':enem_senha' => $inscricao->enem_senha,
            ':local_prova' => $inscricao->localProva,
            ':data_cadastro' => date("Y-m-d H:i:s"),
            ':status' => 1
        ];
        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $inscricao): bool
    {
        $sql = "DELETE FROM inscricao WHERE id = :id";
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
                inscricao
            WHERE
                id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Inscricao::class);
        return $stmt->fetch();
    }

    public function getAll(int $offset = 0, int $limit = PHP_INT_MAX): array
    {
        $sql = "
            SELECT
                *
            FROM
                inscricao
            ORDER BY
                id DESC
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Inscricao::class);
        return $stmt->fetchAll();
    }

    public function getAmount()
    {
        $sql = "
            SELECT
                COUNT(id) AS amount
            FROM
                post

        ";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetch();
    }


    public function update(Inscricao $inscricao): bool
    {
        $sql = "
            UPDATE
                inscricao
            SET
                nome = :nome,
                endereco = :endereco
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => intval($page_statistic->id),
            ':nome' => $inscricao->nome,
            ':endereco' => $inscricao->endereco
        ];
        return $stmt->execute($parameters);
    }
}
