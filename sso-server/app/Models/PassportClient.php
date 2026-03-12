<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Client as BaseClient;
use Illuminate\Contracts\Auth\Authenticatable;


class PassportClient extends BaseClient
{
    /**
     * Indicates if the IDs are UUIDs.
     */
    public $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    public function skipsAuthorization(Authenticatable $user, array $scopes): bool
    {
        // Skip authorization for trusted clients
        return in_array($this->name, ['ecommerce-client', 'foodpanda-client', 'foodpanda-client-new']);
    }
}
