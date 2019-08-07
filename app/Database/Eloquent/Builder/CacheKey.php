<?php
namespace App\Database\Eloquent\Builder;

use GeneaLabs\LaravelModelCaching\CacheKey as OriginalCacheKey;

class CacheKey extends OriginalCacheKey
{
    protected function getInAndNotInClauses(array $where) : string
    {
        if (! in_array($where["type"], ["In", "NotIn"])) {
            return "";
        }

        $type = strtolower($where["type"]);
        $subquery = $this->getValuesFromWhere($where);
        $values = collect();
        if ($this->query->bindings["where"]) {
            $values = collect($this->query->bindings["where"][$this->currentBinding]);
            $this->currentBinding++;
        }
        $subquery = collect(vsprintf(str_replace("?", "%s", $subquery), $values->toArray()));
        $values = $this->recursiveImplode($subquery->toArray(), "_");

        return "-{$where["column"]}_{$type}{$values}";
    }
}
