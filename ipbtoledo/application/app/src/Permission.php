<?php
declare(strict_types=1);

namespace Farol360\AncoraEad;

class Permission
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        $sql = "
            SELECT
                resource
            FROM
                permissions
        ";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }
}
