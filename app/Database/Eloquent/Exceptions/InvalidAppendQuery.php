<?php

namespace App\Database\Eloquent\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;

/**
 * Class InvalidAppendQuery
 * @package App\Database\Eloquent\Exceptions
 */
class InvalidAppendQuery extends InvalidQuery
{
    /** @var \Illuminate\Support\Collection */
    public $appendsNotAllowed;

    /** @var \Illuminate\Support\Collection */
    public $allowedAppends;


    /**
     * InvalidAppendQuery constructor.
     * @param Collection $appendsNotAllowed
     * @param Collection $allowedAppends
     */
    public function __construct(Collection $appendsNotAllowed, Collection $allowedAppends)
    {
        $this->appendsNotAllowed = $appendsNotAllowed;
        $this->allowedAppends    = $allowedAppends;

        $appendsNotAllowed = $appendsNotAllowed->implode(', ');
        $allowedAppends    = $allowedAppends->implode(', ');
        $message = "Given append(s) `{$appendsNotAllowed}` are not allowed. Allowed append(s) are `{$allowedAppends}`.";

        parent::__construct(Response::HTTP_BAD_REQUEST, $message);
    }


    /**
     * @param Collection $appendsNotAllowed
     * @param Collection $allowedAppends
     * @return InvalidAppendQuery
     */
    public static function appendsNotAllowed(Collection $appendsNotAllowed, Collection $allowedAppends)
    {
        return new static(...func_get_args());
    }


}
