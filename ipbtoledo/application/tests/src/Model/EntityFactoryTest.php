<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Tests\Model;

use Farol360\AncoraEad\Model\Attachment;
use Farol360\AncoraEad\Model\Course;
use Farol360\AncoraEad\Model\EntityFactory;
use Farol360\AncoraEad\Model\Level;
use Farol360\AncoraEad\Model\Module;
use Farol360\AncoraEad\Model\Order;
use Farol360\AncoraEad\Model\Permission;
use Farol360\AncoraEad\Model\Role;
use Farol360\AncoraEad\Model\User;
use PHPUnit\Framework\TestCase;

/**
 * @covers Farol360\AncoraEad\Model\EntityFactory
 */
final class EntityFactoryTest extends TestCase
{
    /**
     * Test for create methods
     *
     * @dataProvider providerTestCreate
     * @return void
     */
    public function testCreate($expected, $actual)
    {
        $this->assertInstanceOf($expected, $actual);
    }

    /**
    * Data provider for testCreate
    *
    * @return array
    */
    public function providerTestCreate()
    {
        $entityFactory = new EntityFactory();
        return [
            [Attachment::class, $entityFactory->createAttachment()],
            [Course::class, $entityFactory->createCourse()],
            [Level::class, $entityFactory->createLevel()],
            [Module::class, $entityFactory->createModule()],
            [Order::class, $entityFactory->createOrder()],
            [Permission::class, $entityFactory->createPermission()],
            [Role::class, $entityFactory->createRole()],
            [User::class, $entityFactory->createUser()],
        ];
    }
}
