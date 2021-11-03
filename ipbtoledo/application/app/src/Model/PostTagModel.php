<?php

namespace Farol360\AncoraEad\Model;

use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\Model\PostTag;

/**
*
*/
class PostTagModel extends Model {

    function add(PostTag $postTag) {
        $sql = "
            INSERT INTO post_tag (id_post, nome)
            VALUES ( :id_post, :nome)
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id_post' => $postTag->id_post,
            ':nome' => $postTag->nome,
        ];


        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    function get($id)
    {
       $sql = "
            SELECT
                *
            FROM
                post_tag
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

    public function getAll(int $offset = 0, int $limit = PHP_INT_MAX): array
    {
        $sql = "
            SELECT
                *
            FROM
                post_tag
            ORDER BY
                id DESC
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, PostTag::class);
        return $stmt->fetchAll();
    }

    public function getTags($id_post) {
        $sql = "
            SELECT
                *
            FROM
                post_tag
            WHERE
                id_post = :id_post
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id_post' => $id_post];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, PostTag::class);
        return $stmt->fetchAll();
    }

    public function delete($id) {
        $sql = "DELETE FROM post_tag WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        return $stmt->execute($parameters);
    }

    public function deletePost($id_post) {
        $sql = "DELETE FROM post_tag WHERE id_post = :id_post";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id_post' => $id_post];
        return $stmt->execute($parameters);
    }
}
