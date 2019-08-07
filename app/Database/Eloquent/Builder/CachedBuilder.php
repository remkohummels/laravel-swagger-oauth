<?php
namespace App\Database\Eloquent\Builder;

use GeneaLabs\LaravelModelCaching\CachedBuilder as GeneaCachedBuilder;

/**
 * Class CacheBuilder wchich overrides GeneaLabs CacheBuilder to get columns from each query directly
 * @package App\Database\Eloquent\Builder
 */
class CachedBuilder extends GeneaCachedBuilder
{


    /**
     * @param array $columns
     * @return CachedBuilder[]|\Illuminate\Database\Eloquent\Collection|mixed
     */
    public function get($columns = ["*"])
    {
        if (! $this->isCachable()) {
            return parent::get($columns);
        }

        $defaultFields = \request('fields', '');
        $cacheKey      = $this->makeCacheKey($columns, null, $defaultFields);

        return $this->cachedValue(func_get_args(), $cacheKey);
    }


    protected function makeCacheKey(
        array $columns = ['*'],
        $idColumn = null,
        string $keyDifferentiator = ''
    ) : string {
        $eagerLoad = $this->eagerLoad ?? [];
        $model = $this->model ?? $this;
        $query = $this->query ?? app('db')->query();

        return (new CacheKey($eagerLoad, $model, $query))
            ->make($columns, $idColumn, $keyDifferentiator);
    }
}