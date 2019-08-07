<?php

namespace App\Database\Eloquent\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class InvalidQuery
 * @package App\Database\Eloquent\Exceptions
 */
abstract class InvalidQuery extends HttpException
{
}
