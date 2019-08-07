<?php

use App\Models\RolePermissions\Role;
use App\Models\User;
use App\Models\UserData;
use App\Providers\AuthServiceProvider;
use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;

/**
 * Class UserTableSeeder
 */
class UserTableSeeder extends Seeder
{

    public const SUPER_ADMIN_NAME = 'super_administrator';

    /**
     * Run the database seeds.
     */
    public function run(ClientRepository $clients)
    {
        $adminUserData = [
            'name' => self::SUPER_ADMIN_NAME,
            'first_name' => 'Super',
            'last_name' => 'Administrator',
            'email' => 'super-administrator@coffective.com',
            'email_verified_at' => now(),
            'password' => Hash::make('123456789'), // secret
            'status' => 'approved',
        ];

        $administrator = factory(User::class)->create($adminUserData);

        $adminRole = Role::query()->whereName(AuthServiceProvider::ROLE_ADMINISTRATOR)->first();
        $administrator->attachRole($adminRole);

        $redirectUrl = rtrim(\Config::get('app.url'), '/\\') . '/' . \Config::get('l5-swagger.routes.oauth2_callback');

        // First party (trusted) native or user agent client application with simple password grant token
        $passGrantUser = $this->createUser($clients, 'user_agent_first_party_or_native_app_first_party_app', AuthServiceProvider::ROLE_CLIENT_APP);
        $passGrantClientAppName  = 'user_agent_first_party_or_native_app_first_party_app';
        $passGrantClient = $clients->createPasswordGrantClient($passGrantUser->id, $passGrantClientAppName, $redirectUrl);

        $this->command->getOutput()->writeln('Password grant client created successfully.');
        $this->command->getOutput()->writeln('<comment>Client ID:</comment> ' . $passGrantClient->id);
        $this->command->getOutput()->writeln('<comment>Client Secret:</comment> ' . $passGrantClient->secret);

        // Authorization code grant for web apps and third party native apps
        $authCodeGrantUser = $this->createUser($clients, 'web_or_third_party_native_app', AuthServiceProvider::ROLE_CLIENT_APP);
        $authCodeGrantClientAppName  = 'web_or_third_party_native_app';
        $authCodeGrantClientAppName = $clients->create($authCodeGrantUser->id, $authCodeGrantClientAppName, $redirectUrl, false, false);

        $this->command->getOutput()->writeln('Authentication code grant client created successfully.');
        $this->command->getOutput()->writeln('<comment>Client ID:</comment> ' . $authCodeGrantClientAppName->id);
        $this->command->getOutput()->writeln('<comment>Client Secret:</comment> ' . $authCodeGrantClientAppName->secret);

        // End users, his/her data will be used by client applications
        $endUser1 = $this->createUser($clients, 'end_user_1', AuthServiceProvider::ROLE_USER);
        factory(UserData::class)->create([UserData::USER_ID => $endUser1->id]);

        $endUser2 = $this->createUser($clients, 'end_user_2', AuthServiceProvider::ROLE_USER);
        factory(UserData::class)->create([UserData::USER_ID => $endUser2->id]);

        // Staff users, user which are able to manage companies if attached
        $this->createUser($clients, 'staff_1', AuthServiceProvider::ROLE_STAFF);
        $this->createUser($clients, 'staff_2', AuthServiceProvider::ROLE_STAFF);

        // Organisation usersm they are able to CRUD their companies and assign staff to it
        $this->createUser($clients, 'company_1', AuthServiceProvider::ROLE_COMPANY);
        $this->createUser($clients, 'company_2', AuthServiceProvider::ROLE_COMPANY);
    }


    /**
     * @param ClientRepository
     * @param string $name
     * @param string $roleName
     * @return User
     */
    protected function createUser($clients, $name, $roleName): User
    {
        $endUserData['name']  = $name;
        $endUserData['email'] = $name . '@gmail.com';
        $endUserData['password'] = Hash::make('123456789');
        /** @var User $endUser */
        $endUser = factory(User::class)->create($endUserData);

        if (empty($roleName) === false) {
            $role = Role::query()->whereName($roleName)->first();
            $endUser->attachRole($role);
        }

        $redirectUrl = rtrim(\Config::get('app.url'), '/\\') . '/' . \Config::get('l5-swagger.routes.oauth2_callback');

        return $endUser;
    }
}