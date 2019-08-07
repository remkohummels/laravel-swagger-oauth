<?php
namespace App\Traits;

use App\Database\Eloquent\Builder\CachedBuilder;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use GeneaLabs\LaravelModelCaching\Traits\Caching;
use GeneaLabs\LaravelModelCaching\Traits\ModelCaching;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

/**
 * Trait Cachable
 * @package App\Traits
 */
trait Cachable
{
    use Caching;
    use ModelCaching;
    use PivotEventTrait {
        ModelCaching::newBelongsToMany insteadof PivotEventTrait;
    }

    /**
     * @param $query
     * @return CachedBuilder|EloquentBuilder
     */
    public function newEloquentBuilder($query)
    {
        if (! $this->isCachable()) {
            $this->isCachable = true;

            return new EloquentBuilder($query);
        }

        $builder = new CachedBuilder($query);

        return $builder;
    }
}
