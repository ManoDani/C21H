<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\Model\Attachment;

class AttachmentModel extends Model
{
    public function add(Attachment $attachment)
    {
        $sql = "
            INSERT INTO attachments (file_name, path, level_id)
            VALUES (:file_name, :path, :level_id)
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':file_name' => $attachment->fileName,
            ':path' => $attachment->path,
            ':level_id' => $attachment->level_id
        ];
        if ($stmt->execute($parameters)) {
            return $this->db->lastInsertId();
        } else {
            return null;
        }
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM attachments WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        return $stmt->execute($parameters);
    }

    public function get(int $id)
    {
        $sql = "
            SELECT
                id,
                file_name,
                path,
                created_at,
                level_id
            FROM
                attachments
            WHERE
                id = :id
            LIMIT 1
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':id' => $id];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Attachment::class);
        return $stmt->fetch();
    }

    public function getAll(int $offset = 0, int $limit = PHP_INT_MAX): array
    {
        $sql = "
            SELECT
                id,
                file_name,
                path,
                created_at,
                level_id
            FROM
                attachments
            LIMIT ? , ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Attachment::class);
        return $stmt->fetchAll();
    }

    public function getAttachments(int $levelId): array
    {
        $sql = "
            SELECT
                id,
                file_name,
                path,
                created_at,
                level_id
            FROM
                attachments
            WHERE
                level_id = :level_id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [':level_id' => $levelId];
        $stmt->execute($parameters);
        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, Attachment::class);
        return $stmt->fetchAll();
    }

    public function update(Attachment $attachment): bool
    {
        $sql = "
            UPDATE
                attachments
            SET
                file_name = :file_name,
                path = :path,
                level_id = :level_id
            WHERE
                id = :id
        ";
        $stmt = $this->db->prepare($sql);
        $parameters = [
            ':id' => $attachment->id,
            ':file_name' => $attachment->file_name,
            ':path' => $attachment->path,
            ':level_id' => $attachment->level_id
        ];
        return $stmt->execute($parameters);
    }
}
