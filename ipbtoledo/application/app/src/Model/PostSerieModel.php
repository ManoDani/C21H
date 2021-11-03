<?php

namespace Farol360\AncoraEad\Model;

use Farol360\AncoraEad\Model;

/**
*
*/
class PostSerieModel extends Model {

    function add(PostSerie $postSerie) {
        $sql = "
            INSERT INTO post_serie (nome_serie, img_destaque, usr_date, slug, status)
            VALUES ( :nome_serie, :img_destaque, :usr_date, :slug, :status)
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':nome_serie' => $postSerie->nome_serie,
            ':img_destaque' => $postSerie->img_destaque,
            ':usr_date' => $postSerie->usr_date,
            ':slug' => $postSerie->slug,
            ':status' => $postSerie->status,
        ];


        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM post_serie WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        return $stmt->execute($parameters);
    }

    function get($id)
    {
       $sql = "
            SELECT
                *
            FROM
                post_serie
            WHERE
                id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $id
        ];
        $stmt->execute($parameters);
        return $stmt->fetch();
    }

    function getSerieAtual() {
        $sql = "
            SELECT
                value_config
            FROM
                config
            WHERE
                key_config = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => "serie_atual"
        ];
        $stmt->execute($parameters);
        return $stmt->fetch();
    }

    function getProgramaDevocional() {
        $sql = "
            SELECT
                value_config
            FROM
                config
            WHERE
                key_config = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => "programa_devocional"
        ];
        $stmt->execute($parameters);
        return $stmt->fetch();
    }

    function getSerieEspecial() {
        $sql = "
            SELECT
                value_config
            FROM
                config
            WHERE
                key_config = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => "serie_especial"
        ];
        $stmt->execute($parameters);
        return $stmt->fetch();
    }

    public function getAll(int $offset = 0, int $limit = PHP_INT_MAX): array
    {
        $sql = "
            SELECT
                *
            FROM
                post_serie
            ORDER BY
                usr_date DESC
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, PostType::class);
        return $stmt->fetchAll();
    }

    public function getAmount() {
        $sql = "SELECT COUNT(id) AS amount from post_serie";

        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetch();
    }

    public function disable(int $id): bool
    {
        $sql = "
            UPDATE
                post_serie
            SET
                status = 0
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $id,
        ];
        return $stmt->execute($parameters);
    }

    public function enable(int $id): bool
    {
        $sql = "
            UPDATE
                post_serie
            SET
                status = 1
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $id,
        ];
        return $stmt->execute($parameters);
    }

    public function setSerieAtual($id_serie) {
        $sql = "
            UPDATE
                config
            SET
                value_config = :id_serie

            WHERE
                key_config = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id_serie' => $id_serie,
            ':id' => 'serie_atual'
        ];
        return $stmt->execute($parameters);
    }
    public function setProgramaDevocional($id_serie) {
        $sql = "
            UPDATE
                config
            SET
                value_config = :id_serie

            WHERE
                key_config = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id_serie' => $id_serie,
            ':id' => 'programa_devocional'
        ];
        return $stmt->execute($parameters);
    }
    public function setSerieEspecial($id_serie) {
        $sql = "
            UPDATE
                config
            SET
                value_config = :id_serie

            WHERE
                key_config = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id_serie' => $id_serie,
            ':id' => 'serie_especial'
        ];
        return $stmt->execute($parameters);
    }

    public function update(PostSerie $postSerie): bool
    {
        $sql = "
            UPDATE
                post_serie
            SET
                nome_serie = :nome_serie,
                img_destaque = :img_destaque,
                usr_date = :usr_date,
                slug = :slug,
                status = :status

            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $postSerie->id,
            ':nome_serie' => $postSerie->nome_serie,
            ':img_destaque' => $postSerie->img_destaque,
            ':usr_date' => $postSerie->usr_date,
            ':slug' => $postSerie->slug,
            ':status' => $postSerie->status,

        ];
        return $stmt->execute($parameters);
    }

    public function getBySlug(string $slug)
    {
        $sql = "
            SELECT
                *
            FROM
                post_serie
            WHERE
                slug = :slug
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':slug' => $slug];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Post::class);
        return $stmt->fetch();
    }

}
