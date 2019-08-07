<?php

namespace App\Providers;

use App\Http\Requests\DynamicChildObjectsFormRequest;
use App\Http\Requests\DynamicMetaFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Redirector;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Token;
use Lcobucci\JWT\Parser;

/**
 * Class DynamicRequestServiceProvider
 * @package App\Providers
 */
class DynamicRequestServiceProvider extends ServiceProvider
{


    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->resolving(
            DynamicMetaFormRequest::class,
            function ($request, $app) {
                /** @var DynamicMetaFormRequest $request */
                if ($request->bearerToken() != null) {
                    $clientId = (new Parser())->parse($request->bearerToken())->getClaim('aud');
                } else {
                    $clientId = 2;
                }

                $request = FormRequest::createFrom($app['request'], $request);
                $request->setContainer($app)->setRedirector($app->make(Redirector::class));
                $request->initializeRules((int)$clientId);
            }
        );
    }


    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }


}
