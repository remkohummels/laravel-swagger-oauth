<?php

namespace App\Database\Eloquent\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;

/**
 * Class InvalidIncludeQuery
 * @package App\Database\Eloquent\Exceptions
 */
class InvalidIncludeQuery extends InvalidQuery
{
    /** @var \Illuminate\Support\Collection */
    public $unknownIncludes;

    /** @var \Illuminate\Support\Collection */
    public $allowedIncludes;


    /**
     * InvalidIncludeQuery constructor.
     * @param Collection $unknownIncludes
     * @param Collection $allowedIncludes
     */
    public function __construct(Collection $unknownIncludes, Collection $allowedIncludes)
    {
        $this->unknownIncludes = $unknownIncludes;
        $this->allowedIncludes = $allowedIncludes;

        $unknownIncludes = $unknownIncludes->implode(', ');
        $allowedIncludes = $allowedIncludes->implode(', ');
        $message         = "Given include(s) `{$unknownIncludes}` are not allowed. Allowed include(s) are `{$allowedIncludes}`.";

        parent::__construct(Response::HTTP_BAD_REQUEST, $message);
    }


    /**
     * @param Collection $unknownIncludes
     * @param Collection $allowedIncludes
     * @return InvalidIncludeQuery
     */
    public static function includesNotAllowed(Collection $unknownIncludes, Collection $allowedIncludes)
    {
        return new static(...func_get_args());
    }


}
