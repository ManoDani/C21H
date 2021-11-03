<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

use Farol360\AncoraEad\Model\Attachment;
use Farol360\AncoraEad\Model\Banner;
use Farol360\AncoraEad\Model\Course;
use Farol360\AncoraEad\Model\Level;
use Farol360\AncoraEad\Model\Module;
use Farol360\AncoraEad\Model\Order;
use Farol360\AncoraEad\Model\Post;
use Farol360\AncoraEad\Model\PostFile;
use Farol360\AncoraEad\Model\PostSerie;
use Farol360\AncoraEad\Model\PostTag;
use Farol360\AncoraEad\Model\Permission;
use Farol360\AncoraEad\Model\Role;
use Farol360\AncoraEad\Model\User;

class EntityFactory
{
    public function createAttachment(array $data = []): Attachment
    {
        return new Attachment($data);
    }
    public function createBanner(array $data = []): Banner
    {
        return new Banner($data);
    }

    public function createCourse(array $data = []): Course
    {
        return new Course($data);
    }

    public function createModule(array $data = []): Module
    {
        return new Module($data);
    }

    public function createLevel(array $data = []): Level
    {
        return new Level($data);
    }

    public function createOrder(array $data = []): Order
    {
        return new Order($data);
    }

    public function createPost(array $data = []): Post
    {
        return new Post($data);
    }

    public function createPostFile(array $data = []): PostFile
    {
        return new PostFile($data);
    }

    public function createPostSerie(array $data = []): PostSerie
    {
        return new PostSerie($data);
    }

    public function createPostTag(array $data = []): PostTag
    {
        return new PostTag($data);
    }

    public function createPermission(array $data = []): Permission
    {
        return new Permission($data);
    }

    public function createRole(array $data = []): Role
    {
        return new Role($data);
    }

    public function createUser(array $data = []): User
    {
        return new User($data);
    }
}
