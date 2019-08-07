<?php

namespace App\Http\Resources;

use App\Models\Company as CompanyModel;
use App\Models\CompanyData;
use Laravel\Passport\Passport;

class CompanyFull extends BasicResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $basicData    = [CompanyModel::BASIC_GROUP => parent::toArray($request)];
        $standardData = self::formatData($this->defaultData()->get()->toArray());
        $appData      = self::formatData($this->applicationData()->get()->toArray());

        return array_merge($basicData, $standardData, $appData);
    }


    /**
     * @param array $data
     * @return array
     */
    static protected function formatData(array $data): array
    {
        $result = [];

        foreach ($data as $row) {
            $name = $row[CompanyData::NAME];
            unset($row[CompanyData::NAME]);

            $filtered = array_diff_key($row, array_flip(CompanyData::REQUIRED_FIELDS));

            $result[$name] = $filtered;
        }

        return $result;
    }

}
