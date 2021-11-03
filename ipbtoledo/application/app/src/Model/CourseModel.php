<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\Model\Course;

class CourseModel extends Model
{
    public function add(Course $course)
    {
        $sql = "
            INSERT INTO courses (
                title,
                price,
                description,
                long_description,
                image
            ) VALUES (
                :title,
                :price,
                :description,
                :long_description,
                :image
            )
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':title' => $course->title,
            ':price' => $course->price,
            ':description' => $course->description,
            ':long_description' => $course->long_description,
            ':image' => $course->image
        ];
        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM courses WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        return $stmt->execute($parameters);
    }

    public function disable(int $id): bool
    {
        $sql = "
            UPDATE
                courses
            SET
                status = 0
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $id,
        ];
        return $stmt->execute($parameters);
    }

    public function enable(int $id): bool
    {
        $sql = "
            UPDATE
                courses
            SET
                status = 1
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $id,
        ];
        return $stmt->execute($parameters);
    }

    public function get(int $id)
    {
        $sql = "
            SELECT
                id,
                title,
                price,
                description,
                long_description,
                image,
                status,
                created_at
            FROM
                courses
            WHERE
                id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Course::class);
        return $stmt->fetch();
    }

    public function getAll(int $offset = 0, int $limit = PHP_INT_MAX): array
    {
        $sql = "
            SELECT
                id,
                title,
                price,
                description,
                long_description,
                image,
                status,
                created_at
            FROM
                courses
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Course::class);
        return $stmt->fetchAll();
    }

    public function update(Course $course): bool
    {
        $sql = "
            UPDATE
                courses
            SET
                title = :title,
                price = :price,
                description = :description,
                long_description = :long_description,
                image = :image
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $course->id,
            ':title' => $course->title,
            ':price' => $course->price,
            ':description' => $course->description,
            ':long_description' => $course->long_description,
            ':image' => $course->image
        ];
        return $stmt->execute($parameters);
    }
}
