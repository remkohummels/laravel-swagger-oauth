<?php

use App\Models\Company;
use App\Models\MetaType;
use App\Models\ObjectType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Laravel\Passport\Passport;

/**
 * Class MetaTypesTableSeeder
 */
class MetaTypesTableSeeder extends Seeder
{


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultUser   = User::first();
        $defaultClient = Passport::client()->first();

        $metaData = [
            MetaType::USER_ID        => $defaultUser->id,
            MetaType::IS_CUSTOM      => MetaType::IS_STANDARD,
        ];
        factory(MetaType::class, 3)->create($metaData);

        $appMetaData = array_merge(
            $metaData,
            [
                MetaType::USER_ID    => $defaultUser->id,
                MetaType::IS_CUSTOM  => MetaType::IS_APP_SPECIFIC,
                MetaType::CLIENT_ID  => $defaultClient->id,
            ]
        );

        factory(MetaType::class, 3)->create($appMetaData);
    }


}