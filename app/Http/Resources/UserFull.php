<?php

namespace App\Http\Resources;

use App\Models\User as UserModel;
use App\Models\UserData;
use Laravel\Passport\Passport;

class UserFull extends BasicResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $basicData    = [UserModel::BASIC_GROUP => parent::toArray($request)];
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
            if (empty($row[UserData::NAME]) === false) {
                $name = $row[UserData::NAME];
                unset($row[UserData::NAME]);

                $filtered = array_diff_key($row, array_flip(UserData::REQUIRED_FIELDS));

                $result[$name] = $filtered;
            }
        }

        return $result;
    }

}
