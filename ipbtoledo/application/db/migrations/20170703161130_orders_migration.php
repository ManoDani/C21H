<?php

use Phinx\Migration\AbstractMigration;

class OrdersMigration extends AbstractMigration
{
    public function change()
    {
        $orders = $this->table('orders');
        $orders->addColumn('reference', 'string');
        $orders->addColumn('amount', 'decimal', [
            'precision' => 9,
            'scale' => 2,
            'default' => 0,
        ]);
        $orders->addColumn('transaction', 'string', ['null' => true]);
        $orders->addColumn('status', 'integer', ['null' => true]);
        $orders->addColumn('raw', 'text', ['null' => true]);
        $orders->addColumn('course_id', 'integer', ['null' => true]);
        $orders->addColumn('user_id', 'integer', ['null' => true]);
        $orders->addIndex(['reference'], ['unique' => true]);
        $orders->addForeignKey('course_id', 'courses', 'id', [
            'delete' => 'SET_NULL',
            'update' => 'NO_ACTION'
        ]);
        $orders->addForeignKey('user_id', 'users', 'id', [
            'delete' => 'SET_NULL',
            'update' => 'NO_ACTION'
        ]);
        $orders->addTimestamps();
        $orders->create();
    }
}
