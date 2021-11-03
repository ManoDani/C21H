<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\Model\Module;

class ModuleModel extends Model
{
    public function add(Module $module)
    {
        $sql = "
            INSERT INTO modules (number, title, description, course_id)
            VALUES (:number, :title, :description, :course_id)
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':number' => $module->number,
            ':title' => $module->title,
            ':description' => $module->description,
            ':course_id' => $module->course_id
        ];
        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $id, int $course, int $number): bool
    {
        $sql = "
            UPDATE
                modules
            SET
                number = number - 1
            WHERE
                course_id = :course_id AND
                number > :number;

            DELETE FROM
                modules
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $id,
            ':course_id' => $course,
            ':number' => $number,
        ];
        return $stmt->execute($parameters);
    }

    public function get(int $id)
    {
        $sql = "
            SELECT
                modules.id,
                modules.number,
                modules.title,
                modules.description,
                modules.created_at,
                modules.course_id,
                courses.title AS course_title
            FROM
                modules
                LEFT JOIN courses ON courses.id = modules.course_id
            WHERE
                modules.id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Module::class);
        return $stmt->fetch();
    }

    public function getAll(int $offset = 0, int $limit = PHP_INT_MAX): array
    {
        $sql = "
            SELECT
                modules.id,
                modules.number,
                modules.title,
                modules.description,
                modules.created_at,
                modules.course_id,
                courses.title AS course_title
            FROM
                modules
                LEFT JOIN courses ON courses.id = modules.course_id
            ORDER BY
                number
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Module::class);
        return $stmt->fetchAll();
    }

    public function getGreatestNumber(int $courseId)
    {
        $sql = "
            SELECT
                MAX(number) as max_number
            FROM
                modules
            WHERE
                course_id = :course_id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':course_id' => $courseId];
        $stmt->execute($parameters);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getModules(int $courseId): array
    {
        $sql = "
            SELECT
                id,
                number,
                title,
                description,
                created_at,
                course_id
            FROM
                modules
            WHERE
                course_id = :course_id
            ORDER BY
                number
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':course_id' => $courseId];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Module::class);
        return $stmt->fetchAll();
    }

    protected function swap(int $id, string $action = 'up'): bool
    {
        $module = $this->get($id);
        if ($action == 'up') {
            $next = $module->number - 1;
        } elseif ($action == 'down') {
            $next = $module->number + 1;
        }
        $sql = "
            UPDATE
                modules
            SET
                number = :number
            WHERE
                number = :next AND
                course_id = :course_id;
            UPDATE
                modules
            SET
                number = :next
            WHERE
                id = :id;
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $module->id,
            ':number' => $module->number,
            ':next' => $next,
            ':course_id' => $module->course_id
        ];
        return $stmt->execute($parameters);
    }

    public function swapDown(int $id): bool
    {
        return $this->swap($id, 'down');
    }

    public function swapUp(int $id): bool
    {
        return $this->swap($id, 'up');
    }

    public function update(Module $module): bool
    {
        $sql = "
            UPDATE
                modules
            SET
                number = :number,
                title = :title,
                description = :description,
                course_id = :course_id
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $module->id,
            ':number' => $module->number,
            ':title' => $module->title,
            ':description' => $module->description,
            ':course_id' => $module->course_id
        ];
        return $stmt->execute($parameters);
    }
}
