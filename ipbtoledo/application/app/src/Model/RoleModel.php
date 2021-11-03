<?php

namespace Ead\Model;

class RoleModel extends Model
{
    public function getAll()
    {
        $sql = "
            SELECT
                id,
                role,
                name,
                access_level
            FROM
                role
            ORDER BY
                access_level ASC
        ";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public function add($role, $name, $accessLevel)
    {
        $sql = "
            INSERT INTO role (role, name, access_level)
            VALUES (:role, :name, :access_level)
        ";
        $query = $this->db->prepare($sql);
        $parameters = [
            ':role' => $role,
            ':name' => $name,
            ':access_level' => $accessLevel
        ];
        if ($query->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete($id)
    {
        $sql = "DELETE FROM role WHERE id = :id";
        $query = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        return $query->execute($parameters);
    }

    public function get($id)
    {
        $sql = "
            SELECT
                id,
                role,
                name,
                access_level
            FROM
                role
            WHERE
                id = :id
            LIMIT 1
        ";
        $query = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        $query->execute($parameters);
        return $query->fetch();
    }

    public function update($id, $role, $name, $accessLevel)
    {
        $sql = "
            UPDATE
                role
            SET
                role = :role,
                name = :name,
                access_level = :access_level
            WHERE
                id = :id
        ";
        $query = $this->db->prepare($sql);
        $parameters = [
            ':role' => $role,
            ':name' => $name,
            ':access_level' => $accessLevel,
            ':id' => $id
        ];
        return $query->execute($parameters);
    }
}
