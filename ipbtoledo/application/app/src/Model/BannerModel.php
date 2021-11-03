<?php

namespace Farol360\AncoraEad\Model;

use Farol360\AncoraEad\Model;

/**
*
*/
class BannerModel extends Model {

    function add(Banner $banner) {
        $sql = "
            INSERT INTO banner (image, is_mobile, priority, upload_date, link)
            VALUES ( :image, :is_mobile, :priority, :upload_date, :link)
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':image' => $banner->image,
            ':is_mobile' => $banner->is_mobile,
            ':priority' => $banner->priority,
            ':upload_date' => $banner->upload_date,
            ':link' => $banner->link,
        ];


        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM banner WHERE id = :id";
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
                banner
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
                banner
            ORDER BY
                priority DESC
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
        $sql = "SELECT COUNT(id) AS amount from banner";

        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetch();
    }

    public function update(Banner $banner): bool
    {
        $sql = "
            UPDATE
                banner
            SET
                image = :image,
                is_mobile = :is_mobile,
                priority = :priority,
                upload_date = :upload_date,
                link = :link

            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $banner->id,
            ':image' => $banner->image,
            ':is_mobile' => $banner->is_mobile,
            ':priority' => $banner->priority,
            ':upload_date' => $banner->upload_date,
            ':link' => $banner->link,

        ];
        return $stmt->execute($parameters);
    }
}
