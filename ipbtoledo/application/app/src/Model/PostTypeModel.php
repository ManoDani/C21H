<?php

namespace Farol360\AncoraEad\Model;

use Farol360\AncoraEad\Model;

/**
*
*/
class PostTypeModel extends Model {

    function get($id)
    {
       $sql = "
            SELECT
                *
            FROM
                post_type
            WHERE
                $id = id
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
                post_type
            ORDER BY
                id DESC
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, PostType::class);
        return $stmt->fetchAll();
    }
}
