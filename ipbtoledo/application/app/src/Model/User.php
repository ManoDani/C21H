<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Model;

class User
{
    public $id;
    public $email;
    public $name;
    public $password;
    public $nascimento;
    public $cpf;
    public $tel_area;
    public $tel_numero;
    public $end_rua;
    public $end_numero;
    public $end_complemento;
    public $end_bairro;
    public $end_cidade;
    public $end_estado;
    public $end_cep;
    public $role_id;
    public $recover_token;
    public $verification_token;
    public $active;
    public $session;
    public $deleted;
    public $deleted_at;
    public $created_at;
    public $updated_at;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->email = !empty($data['email']) ? strtolower($data['email']) : null;
        $this->name = $data['name'] ?? null;
        $this->password = !empty($data['password']) ? password_hash($data['password'], PASSWORD_DEFAULT) : null;
        $this->nascimento = !empty($data['nascimento']) ? date('Y-m-d', strtotime($data['nascimento'])) : null;
        $this->cpf = $data['cpf'] ?? null;
        $this->tel_area = $data['tel_area'] ?? null;
        $this->tel_numero = $data['tel_numero'] ?? null;
        $this->end_rua = $data['end_rua'] ?? null;
        $this->end_numero = $data['end_numero'] ?? null;
        $this->end_complemento = $data['end_complemento'] ?? null;
        $this->end_bairro = $data['end_bairro'] ?? null;
        $this->end_cidade = $data['end_cidade'] ?? null;
        $this->end_estado = $data['end_estado'] ?? null;
        $this->end_cep = $data['end_cep'] ?? null;
        $this->role_id = $data['role_id'] ?? null;
        $this->recover_token = $data['recover_token'] ?? null;
        $this->verification_token = $data['verification_token'] ?? null;
        $this->active = $data['active'] ?? null;
        $this->session = $data['session'] ?? null;
        $this->deleted = $data['deleted'] ?? null;
        $this->deleted_at = $data['deleted_at'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }
}
