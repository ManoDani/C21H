<?php

use Phinx\Migration\AbstractMigration;

class BaseDataMigration extends AbstractMigration
{
    public function up()
    {
        $roles = [
            [
                'id' => 1,
                'name' => 'guest',
                'description' => 'Visitante',
                'access_level' => 0
            ],
            [
                'id' => 2,
                'name' => 'admin',
                'description' => 'Administrador',
                'access_level' => 900
            ],
            [
                'id' => 3,
                'name' => 'root',
                'description' => 'Super Usuário',
                'access_level' => 1000
            ],
            [
                'id' => 4,
                'name' => 'user',
                'description' => 'Cliente',
                'access_level' => 500
            ]
        ];
        $this->insert('roles', $roles);

        $permissions = [
            [
                'resource' => '/',
                'description' => 'Página inicial',
                'role_id' => 1
            ],
            [
                'resource' => '/users/signin',
                'description' => 'Sign in',
                'role_id' => 1
            ],
            [
                'resource' => '/users/signout',
                'description' => 'Sign out',
                'role_id' => 1
            ],
            [
                'resource' => '/users/signup',
                'description' => 'Sign up',
                'role_id' => 1
            ],
            [
                'resource' => '/admin',
                'description' => 'Página administrativa',
                'role_id' => 2,
            ],
            [
                'resource' => '/admin/permission',
                'description' => 'Ver permissões',
                'role_id' => 3,
            ],
            [
                'resource' => '/admin/permission/add',
                'description' => 'Adicionar permissão',
                'role_id' => 3,
            ],
            [
                'resource' => '/admin/permission/delete/:id',
                'description' => 'Apagar permissão',
                'role_id' => 3,
            ],
            [
                'resource' => '/admin/permission/edit/:id',
                'description' => 'Editar permissão',
                'role_id' => 3,
            ],
            [
                'resource' => '/admin/permission/update',
                'description' => 'Atualizar permissão',
                'role_id' => 3,
            ],
            [
                'resource' => '/admin/role',
                'description' => 'Ver cargos',
                'role_id' => 3,
            ],
            [
                'resource' => '/admin/role/add',
                'description' => 'Adicionar cargo',
                'role_id' => 3,
            ],
            [
                'resource' => '/admin/role/delete/:id',
                'description' => 'Apagar cargo',
                'role_id' => 3,
            ],
            [
                'resource' => '/admin/role/edit/:id',
                'description' => 'Editar cargo',
                'role_id' => 3,
            ],
            [
                'resource' => '/admin/role/update',
                'description' => 'Atualizar cargo',
                'role_id' => 3,
            ],
            [
                'resource' => '/admin/user',
                'description' => 'Ver usuários',
                'role_id' => 2,
            ],
            [
                'resource' => '/admin/user/all',
                'description' => 'Ver todos os usuários',
                'role_id' => 2,
            ],
            [
                'resource' => '/admin/user/:id',
                'description' => 'Ver usuário',
                'role_id' => 2,
            ],
            [
                'resource' => '/admin/user/add',
                'description' => 'Adicionar usuário',
                'role_id' => 2,
            ],
            [
                'resource' => '/admin/user/delete/:id',
                'description' => 'Apagar usuário',
                'role_id' => 2,
            ],
            [
                'resource' => '/admin/user/edit/:id',
                'description' => 'Editar usuário',
                'role_id' => 2,
            ],
            [
                'resource' => '/admin/user/update',
                'description' => 'Atualizar usuário',
                'role_id' => 2,
            ],
            [
                'resource' => '/admin/user/export',
                'description' => 'Exportar usuários',
                'role_id' => 2,
            ],
            [
                'resource' => '/users/profile',
                'description' => 'Ver perfil',
                'role_id' => 4,
            ],
            [
                'resource' => '/users/dashboard',
                'description' => 'Painel do usuário',
                'role_id' => 4,
            ],
            [
                'resource' => '/users/recover',
                'description' => 'Recuperar conta',
                'role_id' => 1,
            ],
            [
                'resource' => '/users/recover/token/:token',
                'description' => 'Recuperar conta',
                'role_id' => 1,
            ],
            [
                'resource' => '/users/verify/:token',
                'description' => 'Verificar conta',
                'role_id' => 1,
            ],
            [
                'resource' => '/contato',
                'description' => 'Página de contato',
                'role_id' => 1,
            ],
            [
                'resource' => '/obrigado',
                'description' => 'Página agradecimento de contato',
                'role_id' => 1,
            ],
            [
                'resource' => '/midia',
                'description' => 'Página Midia',
                'role_id' => 1,
            ],
            [
                'resource' => '/igreja',
                'description' => 'Página igreja',
                'role_id' => 1,
            ],
            [
                'resource' => '/oracao',
                'description' => 'Página igreja',
                'role_id' => 1,
            ]
        ];
        $this->insert('permissions', $permissions);

        $password = password_hash('Ancora1337', PASSWORD_DEFAULT);
        $users = [
            [
                'id' => 1,
                'email' => 'root@localhost',
                'name' => 'Super Usuário',
                'password' => $password,
                'role_id' => 3,
                'active' => 1,
            ],
            [
                'id' => 2,
                'email' => 'admin@localhost',
                'name' => 'Administrador',
                'password' => $password,
                'role_id' => 2,
                'active' => 1,
            ]
        ];
        $this->insert('users', $users);
    }

    public function down()
    {
        $this->execute('DELETE FROM roles');
        $this->execute('DELETE FROM permissions');
        $this->execute('DELETE FROM users');
    }
}
