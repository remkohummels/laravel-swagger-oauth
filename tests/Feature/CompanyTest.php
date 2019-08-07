<?php

namespace Tests\Feature;

use App\Models\Company;

/**
 * Class CompanyTest.
 */
class CompanyTest extends BasicHttpStatusTest
{
    const ENDPOINT = self::BASE_URI.'/companies';
    const FACTORY = Company::class;
}
