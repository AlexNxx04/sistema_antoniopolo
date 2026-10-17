<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;

class CedulaUserProvider implements UserProvider
{
    protected $hasher;

    protected $model;

    public function __construct($hasher, $model)
    {
        $this->hasher = $hasher;
        $this->model = $model;
    }

    public function retrieveById($identifier)
    {
        $model = $this->createModel();

        return $model->newQuery()
            ->where($model->getAuthIdentifierName(), $identifier)
            ->first();
    }

    public function retrieveByToken($identifier, $token)
    {
        return null;
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        //
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return null;
        }

        $cedula = $credentials['cedula'] ?? null;
        $password = $credentials['password'] ?? null;

        if (! $cedula || ! $password) {
            return null;
        }

        $user = $this->createModel()
            ->newQuery()
            ->where('cedula', $cedula)
            ->first();

        if (! $user) {
            return null;
        }

        if (! $user->activo) {
            return null;
        }

        if (! Hash::check($password, $user->getAuthPassword())) {
            return null;
        }

        return $user;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return Hash::check($credentials['password'], $user->getAuthPassword());
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        //
    }

    protected function createModel()
    {
        return new $this->model;
    }
}
