<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Providers\AuthServiceProvider;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Config;
use Laratrust\Laratrust;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param array|string $role
     * @param Model $thing
     * @param Laratrust $laratrust
     */
    protected function hasRoleAndOwns($role, $thing, Laratrust $laratrust): void
    {
        if ($laratrust->hasRole($role) && $laratrust->owns($thing) === false) {
            abort(403, 'Forbidden');
        }
    }

    /**
     * @param array|string $role
     * @param Model $thing
     * @param Laratrust $laratrust
     */
    protected function ownsOrHasRole($role, $thing, Laratrust $laratrust, $foreignKeyName = null): void
    {
        if ($laratrust->hasRole($role) === false && $laratrust->owns($thing, $foreignKeyName) === false) {
            abort(403, 'Forbidden');
        }
    }

    /**
     * @param string $role
     * @param Laratrust $laratrust
     */
    protected function hasRole(string $role, Laratrust $laratrust): void
    {
        if ($laratrust->hasRole($role) === false) {
            abort(403, 'Forbidden');
        }
    }

    /**
     * @param Collection $teams
     * @param Laratrust $laratrust
     * @return bool
     */
    protected function inTeam($teams, Laratrust $laratrust)
    {
        foreach ($teams as $team) {
            if ($laratrust->user()->hasRole(AuthServiceProvider::ROLE_STAFF, $team)) {
                return true;
            }
        }

        return false;
    }
}
