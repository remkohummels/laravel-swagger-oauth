<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ETag
{

    const ERROR = [
        'http_code' => 412,
        'code' => 'PRECONDITION_FAILED',
        'message' => 'Precondition failed.'
    ];


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /** @var JsonResource $response */
        $response = $next($request);
        if (($request->isMethod('get') === false && $request->isMethod('head') === false)
                || $response->getStatusCode() !== 200
                || $request->expectsJson() === false
        ) {
            return $response;
        }

        $eTag = md5($response->getContent());
        $response->header('ETag', $eTag);

        $ifMatch     = $request->header('If-Match', '');
        $matchList = collect(explode(',', $ifMatch))->filter();

        $ifNotMatch   = $request->header('If-None-Match', '');
        $notMatchList = collect(explode(',', $ifNotMatch))->filter();

        $eTagSearch = function($value) use ($eTag) {
            // '-gzip' can be added to etag
            return $value === '*' || Str::startsWith($value, $eTag);
        };

        if ($matchList->isNotEmpty() && $matchList->contains($eTagSearch) === false) {
            $response = response(['error' => self::ERROR], 412);
        } else if ($notMatchList->contains($eTagSearch)) {
            $response->setNotModified();
        }

        return $response;
    }


}