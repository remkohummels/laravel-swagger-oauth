<?php

use App\Models\Company;
use App\Models\CompanyData;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Class CompaniesTableSeeder
 */
class CompaniesTableSeeder extends Seeder
{


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $user1 = factory(User::class)->create();
        $company1 = factory(Company::class)->create([Company::USER_ID => $user1->id]);
        factory(CompanyData::class)->create([CompanyData::COMPANY_ID => $company1->id]);

        $user2 = factory(User::class)->create();
        /** @var Company $company2 */
        $company2 = factory(Company::class)->create([Company::USER_ID => $user2->id]);
        factory(CompanyData::class)->create([CompanyData::COMPANY_ID => $company2->id]);

//        $company2->teams()
//            ->save(factory(Team::class)->make())
//            ->each(
//                function(Team $team) {
//                    $team->users()->saveMany(factory(User::class, 2)->create());
//                }
//            );

    }


}
