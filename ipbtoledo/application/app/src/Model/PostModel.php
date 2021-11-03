<?php

namespace Farol360\AncoraEad\Model;

use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\Model\Post;

/**
*
*/
class PostModel extends Model
{

    public function add (Post $post) {
        $sql = "
            INSERT INTO post (id_tipo_post, data_cadastro, data_alteracao, id_autor, titulo, descricao, destaque, status, id_categoria)
            VALUES (:id_tipo_post, :data_cadastro, :data_alteracao, :id_autor, :titulo, :descricao, :destaque, :status, :id_categoria)
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id_tipo_post' => $post->id_tipo_post,
            ':data_cadastro' => date("Y-m-d H:i:s"),
            ':data_alteracao' => $post->data_alteracao,
            ':id_autor' => $post->id_autor,
            ':titulo' => $post->titulo,
            ':descricao' => $post->descricao,
            ':destaque' => $post->destaque,
            ':status' => $post->status,
            ':id_categoria' => $post->id_categoria
        ];


        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function get(int $id)
    {
        $sql = "
            SELECT
                *
            FROM
                post
            WHERE
                id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Post::class);
        return $stmt->fetch();
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM post WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        return $stmt->execute($parameters);
    }

    public function deleteTipo(int $id_tipo_post): bool
    {
        $sql = "DELETE FROM post WHERE id_tipo_post = :id_tipo_post";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id_tipo_post' => $id_tipo_post];
        return $stmt->execute($parameters);
    }

    public function deleteSerie (int $id_serie): bool {
        $sql = "UPDATE post SET id_categoria = 0 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $id_serie
        ];
        return $stmt->execute($parameters);
    }

    public function disable(int $id): bool
    {
        $sql = "
            UPDATE
                post
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
                post
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

    public function getAll(int $offset = 0, int $limit = PHP_INT_MAX): array
    {
        $sql = "
            SELECT
                *
            FROM
                post
            ORDER BY
                data_alteracao DESC
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Post::class);
        return $stmt->fetchAll();
    }

    public function getAllAsc(int $offset = 0, int $limit = PHP_INT_MAX): array
    {
        $sql = "
            SELECT
                *
            FROM
                post
            ORDER BY
                data_alteracao             LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Post::class);
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

    public function getByPostSerie(int $id):array {
        $sql = "
            SELECT
                *
            FROM
                post
            WHERE
                id_categoria = ?
            ORDER BY
                data_alteracao

        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $id, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Post::class);
        return $stmt->fetchAll();
    }

    public function update(Post $post): bool
    {
        $sql = "
            UPDATE
                post
            SET
                id_tipo_post = :id_tipo_post,
                data_cadastro = :data_cadastro,
                data_alteracao = :data_alteracao,
                id_autor = :id_autor,
                titulo = :titulo,
                descricao = :descricao,
                destaque = :destaque,
                status = :status,
                id_categoria = :id_categoria
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $post->id,
            ':id_tipo_post' => $post->id_tipo_post,
            ':data_cadastro' => $post->data_cadastro,
            ':data_alteracao' => $post->data_alteracao,
            ':id_autor' => $post->id_autor,
            ':titulo' => $post->titulo,
            ':descricao' => $post->descricao,
            ':destaque' => $post->destaque,
            ':status' => $post->status,
            ':id_categoria' => $post->id_categoria
        ];
        return $stmt->execute($parameters);
    }
}
