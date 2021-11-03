<?php

use Phinx\Migration\AbstractMigration;

class CoursesDataMigration extends AbstractMigration
{
    public function up()
    {
        $permissions = [
            [
                'resource' => '/courses',
                'description' => 'Catálogo de cursos',
                'role_id' => 1
            ],
            [
                'resource' => '/course/:id',
                'description' => 'Ver curso',
                'role_id' => 1
            ],
            [
                'resource' => '/course/:id/module/:id/level/:id',
                'description' => 'Ver aula',
                'role_id' => 4
            ],
            [
                'resource' => '/admin/courses',
                'description' => 'Cursos',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/courses/:id',
                'description' => 'Ver curso',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/courses/:id/disable',
                'description' => 'Despublica curso',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/courses/:id/enable',
                'description' => 'Publica curso',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/courses/add',
                'description' => 'Adicionar curso',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/courses/delete/:id',
                'description' => 'Apagar curso',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/courses/edit/:id',
                'description' => 'Editar curso',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/courses/update',
                'description' => 'Atualizar curso',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/courses/remove-image/:id',
                'description' => 'Remove imagem de curso',
                'role_id' => 4
            ],
            [
                'resource' => '/admin/modules/:id',
                'description' => 'Ver módulo',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/modules/add',
                'description' => 'Adicionar módulo',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/modules/delete/:id',
                'description' => 'Apagar módulo',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/modules/edit/:id',
                'description' => 'Editar módulo',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/modules/update',
                'description' => 'Atualizar módulo',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/modules/edit/:id/up',
                'description' => 'Editar número do módulo',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/modules/edit/:id/down',
                'description' => 'Editar número do módulo',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/levels/:id',
                'description' => 'Ver nível',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/levels/add',
                'description' => 'Adicionar nível',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/levels/delete/:id',
                'description' => 'Apagar nível',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/levels/edit/:id',
                'description' => 'Editar nível',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/levels/update',
                'description' => 'Atualizar nível',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/levels/edit/:id/up',
                'description' => 'Editar número do nível',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/levels/edit/:id/down',
                'description' => 'Editar número do nível',
                'role_id' => 2
            ],
            [
                'resource' => '/admin/levels/edit/:id/delete-attachment/:id',
                'description' => 'Apagar anexo',
                'role_id' => 2
            ],
        ];
        $this->insert('permissions', $permissions);
    }

    public function down()
    {
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/courses"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/course/:id"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/course/:id/module/:id/level/:id"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/courses"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/courses/:id"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/courses/add"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/courses/delete/:id"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/courses/edit/:id"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/courses/update"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/courses/remove-image/:id"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/modules/:id"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/modules/add"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/modules/delete/:id"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/modules/edit/:id"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/modules/update"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/modules/edit/:id/up"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/modules/edit/:id/down"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/levels/:id"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/levels/add"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/levels/delete/:id"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/levels/edit/:id"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/levels/update"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/levels/edit/:id/up"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/levels/edit/:id/down"');
        $this->execute('DELETE FROM `permissions` WHERE `resource` = "/admin/levels/edit/:id/delete-attachment/:id"');
    }
}
