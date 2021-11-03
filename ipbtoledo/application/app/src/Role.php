<?php
declare(strict_types=1);

namespace Farol360\AncoraEad;

class Role
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    protected function getAccessLevel($roleId)
    {
        $sql = "
            SELECT
                access_level
            FROM
                roles
            WHERE
                roles.id = :role_id
        ";
        $query = $this->db->prepare($sql);
        $query->execute([":role_id" => $roleId]);
        return $query->fetch()->access_level;
    }

    public function hasPermission($permission, $roleId)
    {
        $accessLevel = $this->getAccessLevel($roleId);
        $sql = "
            SELECT
                permissions.id
            FROM
                roles
                JOIN permissions ON permissions.role_id = roles.id
            WHERE
                permissions.resource = :permission_resource AND
                (roles.access_level < :access_level OR roles.id = :role_id)
        ";
        $query = $this->db->prepare($sql);
        $params = [
            ":permission_resource" => $permission,
            ":access_level" => $accessLevel,
            ":role_id" => $roleId
        ];
        $query->execute($params);
        if ($query->fetch()) {
            return true;
        }
        return false;
    }
}
