<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

use Farol360\AncoraEad\Model;

class UserCourseModel extends Model
{
    public function add(int $userId, int $courseId)
    {
        $sql = "
            INSERT INTO users_courses (user_id, course_id)
            VALUES (:user_id, :course_id)
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':user_id' => $userId,
            ':course_id' => $courseId,
        ];
        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function get(int $userId, int $courseId)
    {
        $sql = "
            SELECT
                user_id,
                course_id
            FROM
                users_courses
            WHERE
                user_id = :user_id AND course_id = :course_id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':user_id' => $userId,
            ':course_id' => $courseId,
        ];
        $stmt->execute($parameters);
        return $stmt->fetch();
    }
}
