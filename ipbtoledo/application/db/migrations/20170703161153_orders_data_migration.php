<?php

use Phinx\Migration\AbstractMigration;

class OrdersDataMigration extends AbstractMigration
{
    public function up()
    {
        $permissions = [
            [
                'resource' => '/order/:id',
                'description' => 'Carrinho de compras',
                'role_id' => 4
            ],
            [
                'resource' => '/order/pagseguro',
                'description' => 'Comunicação com PagSeguro',
                'role_id' => 1
            ],
            [
                'resource' => '/admin/orders',
                'description' => 'Administração - Pedidos',
                'role_id' => 2
            ],
        ];
        $this->insert('permissions', $permissions);
    }

    public function down()
    {
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/order/:id"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/order/pagseguro"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/orders"');
    }
}
