<?php
declare(strict_types=1);

namespace Farol360\Vestibular2017\Model;

use Farol360\Vestibular2017\Model;
use Farol360\Vestibular2017\Model\PageStatistic;

class PageStatisticModel extends Model
{
    public function add(PageStatistic $page_statistic)
    {
        $sql = "
            INSERT INTO page_statistic (page_statistic_name, page_statistic_value)
            VALUES (:page_statistic_name, :page_statistic_value)
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':page_statistic_name' => $page_statistic->page_statistic_name,
            ':page_statistic_value' => $page_statistic->page_statistic_value
        ];
        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $id_page_statistic): bool
    {
        $sql = "DELETE FROM page_statistic WHERE id_page_statistic = :id_page_statistic";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id_page_statistic' => $id_page_statistic];
        return $stmt->execute($parameters);
    }

    public function get(int $id_page_statistic)
    {
        $sql = "
            SELECT
                id_page_statistic,
                page_statistic_name,
                page_statistic_value
            FROM
                page_statistic
            WHERE
                id_page_statistic = :id_page_statistic
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id_page_statistic' => $id_page_statistic];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, PageStatistic::class);
        return $stmt->fetch();
    }


    public function update(PageStatistic $page_statistic): bool
    {
        $sql = "
            UPDATE
                page_statistic
            SET
                page_statistic_name = :page_statistic_name,
                page_statistic_value = :page_statistic_value
            WHERE
                id_page_statistic = :id_page_statistic
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id_page_statistic' => intval($page_statistic->id_page_statistic),
            ':page_statistic_name' => $page_statistic->page_statistic_name,
            ':page_statistic_value' => $page_statistic->page_statistic_value
        ];
        return $stmt->execute($parameters);
    }
}
