<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Tests;

use Farol360\AncoraEad\User;
use PHPUnit\Framework\TestCase;

/**
 * @covers Farol360\AncoraEad\User
 */
final class UserTest extends TestCase
{
    public function testIsAuth()
    {
        $this->assertFalse(User::isAuth());
    }
}
