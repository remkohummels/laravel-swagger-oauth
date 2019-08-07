<?php

namespace App\Traits;


use App\Models\User;
use App\Models\UserData;
use Illuminate\Support\Facades\Hash;

trait SavesUserData
{
    /**
     * @param array $basicData
     * @param array $standardData
     * @param array $appData
     * @return User
     */
    protected function saveUser(array $basicData, array $standardData, array $appData, int $clientId, User $user = null): User
    {
        if (empty($user)) {
            $user = new User($basicData[User::BASIC_GROUP]);
            if ('pending' == config('coffective.registration_type')) {
                $user->status = 'pending';
            } elseif ('approved' == config('coffective.registration_type')) {
                $user->status = 'approved';
            } elseif ('manual' == config('coffective.registration_type')) {
                $user->status = 'approved';
            }

            if (isset($basicData[User::BASIC_GROUP]['password'])) {
                $user->password = Hash::make($basicData[User::BASIC_GROUP]['password']);
            }
        } else if(empty($basicData[User::BASIC_GROUP]) === false) {
            $user->update($basicData[User::BASIC_GROUP]);
        }

        $standardDataObjects = [];
        foreach ($standardData as $standardName => $standardMeta) {
            if (false === is_array($standardMeta)) {
                $standardMeta = ['value' => $standardMeta];
            }
            $standardMeta[UserData::NAME] = $standardName;
            $standardMeta[UserData::USER_ID] = $user->id;

            $standardUserDataObject = $user->defaultData()
                ->where(UserData::NAME, '=', $standardMeta[UserData::NAME])
                ->whereNull(UserData::CLIENT_REFERENCE)
                ->firstOrNew($standardMeta);

            $this->saveUserData($standardUserDataObject, $standardMeta);

            $standardDataObjects []= $standardUserDataObject;
        }

        $appDataObjects = [];
        foreach ($appData as $appName => $appMeta) {
            if (false === is_array($appMeta)) {
                $appMeta = ['value' => $appMeta];
            }
            $appMeta[UserData::NAME] = $appName;
            $appMeta[UserData::USER_ID] = $user->id;
            $appMeta[UserData::CLIENT_REFERENCE] = $clientId;

            $appUserDataObject = $user->applicationData()
                ->where(UserData::NAME, '=', $appMeta[UserData::NAME])
                ->where(UserData::CLIENT_REFERENCE, '=', $clientId)
                ->firstOrNew($appMeta);

            $this->saveUserData($appUserDataObject, $appMeta);

            $appDataObjects []= $appUserDataObject;
        }

        $user->save();

        $user->defaultData()->saveMany($standardDataObjects);
        $user->applicationData()->saveMany($appDataObjects);

        return $user;
    }



    /**
     * @param UserData $userDataObject
     * @param array $noSqlData
     */
    protected function saveUserData(UserData $userDataObject, array $noSqlData): void
    {
        $newDataKeys = array_keys($noSqlData);
        $oldData = collect($userDataObject)->except($newDataKeys)->except(UserData::REQUIRED_FIELDS);
        if ($oldData->isNotEmpty()) {
            $userDataObject->unset($oldData->keys()->toArray());
        }

        $userDataObject->fillable($newDataKeys)->update($noSqlData);
    }
}
