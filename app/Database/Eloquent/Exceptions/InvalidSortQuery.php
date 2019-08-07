<?php

namespace App\Database\Eloquent\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;

/**
 * Class InvalidSortQuery
 * @package App\Database\Eloquent\Exceptions
 */
class InvalidSortQuery extends InvalidQuery
{
    /** @var \Illuminate\Support\Collection */
    public $unknownSorts;

    /** @var \Illuminate\Support\Collection */
    public $allowedSorts;


    /**
     * InvalidSortQuery constructor.
     * @param Collection $unknownSorts
     * @param Collection $allowedSorts
     */
    public function __construct(Collection $unknownSorts, Collection $allowedSorts)
    {
        $this->unknownSorts = $unknownSorts;
        $this->allowedSorts = $allowedSorts;

        $allowedSorts = $allowedSorts->implode(', ');
        $unknownSorts = $unknownSorts->implode(', ');
        $message      = "Given sort(s) `{$unknownSorts}` is not allowed. Allowed sort(s) are `{$allowedSorts}`.";

        parent::__construct(Response::HTTP_BAD_REQUEST, $message);
    }


    /**
     * @param Collection $unknownSorts
     * @param Collection $allowedSorts
     * @return InvalidSortQuery
     */
    public static function sortsNotAllowed(Collection $unknownSorts, Collection $allowedSorts)
    {
        return new static(...func_get_args());
    }


}
