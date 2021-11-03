<?php

namespace Farol360\AncoraEad\Model;

use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\Model\PostFile;

/**
*
*/
class PostFileModel extends Model {

    public function add (PostFile $postFile) {
        $sql = "
            INSERT INTO post_file (id_post, nome, file)
            VALUES (:id_post, :nome, :file)
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id_post' => $postFile->id_post,
            ':nome' => $postFile->nome,
            ':file' => $postFile->file

        ];


        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function get (int $id) {
        $sql = "SELECT
                    *
                FROM
                    post_file
                WHERE
                    id = :id";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, PostFile::class);
        return $stmt->fetch();

    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM post_file WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        return $stmt->execute($parameters);
    }

    public function deleteAll(int $id_post): bool
    {
        $sql = "DELETE FROM post_file WHERE id_post = :id_post";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id_post' => $id_post];
        return $stmt->execute($parameters);
    }

    public function getFiles(int $postId): array
    {
        $sql = "
            SELECT
                *
            FROM
                post_file
            WHERE
                id_post = :id_post
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id_post' => $postId];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, PostFile::class);
        return $stmt->fetchAll();
    }

}
