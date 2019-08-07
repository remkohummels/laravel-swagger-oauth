<?php

namespace App\Database\Eloquent\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;

/**
 * Class InvalidFieldQuery
 * @package App\Database\Eloquent\Exceptions
 */
class InvalidFieldQuery extends InvalidQuery
{
    /** @var \Illuminate\Support\Collection */
    public $unknownFields;

    /** @var \Illuminate\Support\Collection */
    public $allowedFields;


    /**
     * InvalidFieldQuery constructor.
     * @param Collection $unknownFields
     * @param Collection $allowedFields
     */
    public function __construct(Collection $unknownFields, Collection $allowedFields)
    {
        $this->unknownFields = $unknownFields;
        $this->allowedFields = $allowedFields;

        $unknownFields = $unknownFields->implode(', ');
        $allowedFields = $allowedFields->implode(', ');
        $message       = "Given field(s) `{$unknownFields}` are not allowed. Allowed field(s) are `{$allowedFields}`.";

        parent::__construct(Response::HTTP_BAD_REQUEST, $message);
    }


    /**
     * @param Collection $unknownFields
     * @param Collection $allowedFields
     * @return InvalidFieldQuery
     */
    public static function fieldsNotAllowed(Collection $unknownFields, Collection $allowedFields)
    {
        return new static(...func_get_args());
    }


}
