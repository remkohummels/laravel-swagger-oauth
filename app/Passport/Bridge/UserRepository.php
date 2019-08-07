<?php

namespace App\Passport\Bridge;

use App\Models\User as UserModel;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Bridge\User;
use Laravel\Passport\Bridge\UserRepository as UserRepositoryParent;
use MikeMcLin\WpPassword\Facades\WpPassword;
use RuntimeException;
use Illuminate\Hashing\HashManager;
use Illuminate\Contracts\Hashing\Hasher;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository extends UserRepositoryParent
{
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        $provider = config('auth.guards.api.provider');

        if (is_null($model = config('auth.providers.'.$provider.'.model'))) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }

        if (method_exists($model, 'findForPassport')) {
            $user = (new $model)->findForPassport($username);
        } else {
            $user = (new $model)->where('email', $username)->first();
        }

        if (! $user) {
            return;
        } elseif (method_exists($user, 'validateForPassportPasswordGrant')) {
            if (! $user->validateForPassportPasswordGrant($password) && ! $this->tryWpLogin($password, $user)) {
                return;
            }
        } elseif (! $this->hasher->check($password, $user->getAuthPassword()) && ! $this->tryWpLogin($password, $user)) {
            return;
        }

        return new User($user->getAuthIdentifier());
    }

    /**
     * @param string $enteredPassword
     * @param UserModel $user
     * @return bool
     */
    protected function tryWpLogin($enteredPassword, UserModel $user) : bool
    {
        if (WpPassword::check($enteredPassword, $user->password)) {
            $user->password = Hash::make($enteredPassword);
            $user->save();
            return true;
        } else {
            return false;
        }
    }

}
