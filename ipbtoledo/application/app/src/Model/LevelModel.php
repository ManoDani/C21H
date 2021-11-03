<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\Model\Level;

class LevelModel extends Model
{
    public function add(Level $level)
    {
        $sql = "
            INSERT INTO levels (number, title, content, video, module_id)
            VALUES (:number, :title, :content, :video, :module_id)
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':number' => $level->number,
            ':title' => $level->title,
            ':content' => $level->content,
            ':video' => $level->video,
            ':module_id' => $level->module_id
        ];
        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $id, int $module, int $number): bool
    {
        $this->fixNumbers($module, $number);
        $sql = "
            DELETE FROM
                levels
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $id,
        ];
        return $stmt->execute($parameters);
    }

    private function fixNumbers(int $module, int $number): bool
    {
        $sql = "
            UPDATE
                levels
            SET
                number = number - 1
            WHERE
                module_id = :module_id AND
                number > :number
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':module_id' => $module,
            ':number' => $number,
        ];
        return $stmt->execute($parameters);
    }

    public function get(int $id)
    {
        $sql = "
            SELECT
                levels.id,
                levels.number,
                levels.title,
                levels.content,
                levels.video,
                levels.created_at,
                levels.module_id,
                modules.title AS module_title,
                modules.course_id,
                courses.title AS course_title

            FROM
                levels
                LEFT JOIN modules ON modules.id = levels.module_id
                LEFT JOIN courses ON courses.id = modules.course_id
            WHERE
                levels.id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Level::class);
        return $stmt->fetch();
    }

    public function getAll(int $offset = 0, int $limit = PHP_INT_MAX): array
    {
        $sql = "
            SELECT
                id,
                number,
                title,
                content,
                video,
                created_at,
                module_id
            FROM
                levels
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Level::class);
        return $stmt->fetchAll();
    }

    public function getGreatestNumber(int $moduleId)
    {
        $sql = "
            SELECT
                MAX(number) as max_number
            FROM
                levels
            WHERE
                module_id = :module_id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':module_id' => $moduleId];
        $stmt->execute($parameters);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getLevels(int $moduleId): array
    {
        $sql = "
            SELECT
                id,
                number,
                title,
                content,
                video,
                created_at,
                module_id
            FROM
                levels
            WHERE
                module_id = :module_id
            ORDER BY
                number
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':module_id' => $moduleId];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Level::class);
        return $stmt->fetchAll();
    }

    protected function swap(int $id, string $action = 'up'): bool
    {
        $level = $this->get($id);
        if ($action == 'up') {
            $next = $level->number - 1;
        } elseif ($action == 'down') {
            $next = $level->number + 1;
        }
        $sql = "
            UPDATE
                levels
            SET
                number = :number
            WHERE
                number = :next AND
                module_id = :module_id;
            UPDATE
                levels
            SET
                number = :next
            WHERE
                id = :id;
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $level->id,
            ':number' => $level->number,
            ':next' => $next,
            ':module_id' => $level->module_id
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

    public function update(Level $level): bool
    {
        $oldLevel = $this->get((int)$level->id);
        if ($oldLevel->module_id != $level->module_id) {
            $this->fixNumbers((int)$level->module_id, (int)$level->number);
            $level->number = $this->getGreatestNumber((int)$level->module_id)['max_number'] + 1;
        }
        $sql = "
            UPDATE
                levels
            SET
                number = :number,
                title = :title,
                video = :video,
                content = :content,
                module_id = :module_id
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $level->id,
            ':number' => $level->number,
            ':title' => $level->title,
            ':content' => $level->content,
            ':video' => $level->video,
            ':module_id' => $level->module_id,
        ];
        return $stmt->execute($parameters);
    }
}
