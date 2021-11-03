<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\Model\Permission;

class PermissionModel extends Model
{
    public function add(Permission $permission)
    {
        $sql = "
            INSERT INTO permissions (resource, description, role_id)
            VALUES (:resource, :description, :role_id)
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':resource' => $permission->resource,
            ':description' => $permission->description,
            ':role_id' => $permission->role_id
        ];
        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM permissions WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        return $stmt->execute($parameters);
    }

    public function get(int $id)
    {
        $sql = "
            SELECT
                id,
                resource,
                description,
                role_id
            FROM
                permissions
            WHERE
                id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Permission::class);
        return $stmt->fetch();
    }

    public function getAll(): array
    {
        $sql = "
            SELECT
                permissions.id,
                resource,
                permissions.description,
                roles.description as role
            FROM
                permissions
                LEFT JOIN roles ON roles.id = permissions.role_id
            ORDER BY
                permissions.resource
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Permission::class);
        return $stmt->fetchAll();
    }

    public function update(Permission $permission): bool
    {
        $sql = "
            UPDATE
                permissions
            SET
                resource = :resource,
                description = :description,
                role_id = :role_id
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $permission->id,
            ':resource' => $permission->resource,
            ':description' => $permission->description,
            ':role_id' => $permission->role_id
        ];
        return $stmt->execute($parameters);
    }
}
