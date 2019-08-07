<?php

namespace App\Console\Commands;

use App\Models\MetaType;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ImportUserMeta extends Command
{

    const CLIENT_ID = 2;
    const GROUP_CLIENT = 'acf';
    const GROUP_SHARED = 'shared';
    const FIELD_ROLES = 'roles';

    const EXCLUDE_KEY_EXACT = ['first_name', 'last_name', 'old_user_id', 'user_litmos_id',
        'admin_color',
        'rich_editing',
        'syntax_highlighting',
        'session_tokens',
        'comment_shortcuts',
        'use_ssl',
        'show_admin_bar_front',
        'reset_pass_hash',
        'icl_admin_language_migrated_to_wp47',
        'icl_admin_language_for_edit',
        'icl_show_hidden_languages',
        'last_update',
        'timestamp',
        'form_id',
        'dismissed_wp_pointers',
        'wc_last_active',
        'reset_pass_hash_token',
        'gform_recent_forms',
        'qm_wpautop',
        'limit_quick_mail_commenters',
        'show_quick_mail_users',
        'show_quick_mail_commenters',
        'managenav-menuscolumnshidden',
        'manageedit-stakeholdercolumnshidden',
        'tgmpa_dismissed_notice_tgmpa',
        'tgmpa_dismissed_notice_wp-mail-smtp',
        'whatthefile-hide-notice',
        'cfs_user_hash',
        'ac_preferences_check-review',
        'ac_preferences_check-addon-available',
        'acf_user_settings',
        'undefined',
        'password_rst_attempts',
        'users_per_page',
        'edit_stakeholder_per_page',
        'edit_page_per_page',
        'edit_product_per_page',
        'edit_resources_per_page',
        'nav_menu_recently_edited',
        'so_panels_directory_enabled',
        'wpcf7_hide_welcome_panel_on',
        'show_welcome_panel',
        'save_quick_mail_addresses',
        'screen_layout_learn',
        'screen_layout_acf-field-group',
        'dismissed_template_files_notice',
        'dismissed_no_secure_connection_notice',
        'dismissed_no_shipping_methods_notice',
        'nonces',
        'rocket_boxes',
        'role'];
    const EXCLUDE_KEY_LIKE = ['wp_*', '_*', 'um_*'];
    const EXCLUDE_VALUE_EXACT = ['', 'NULL', 'null'];
    const EXCLUDE_VALUE_LIKE = ['field_*'];

    const STRING = 'string';
    const EMAIL = 'string';
    const INTEGER = 'integer';
    const LIST = 'list';

    const TYPE = 'type';
    const VALUE = 'value';

    const CLIENT_GROUP_FIELDS = [
        'age' => self::STRING,
        'expecting' => self::INTEGER,
        'get_ready_checklist' => self::LIST,
        'state' => self::STRING,
        'wic_clinic_name' => self::STRING,
        'choose_your_hospital' => self::STRING,
        'name_your_babys_doctor' => self::STRING,
        'doctor' => self::STRING,
        'midwife' => self::STRING,
        'champion_role' => self::STRING,
        'champion_first_name' => self::STRING,
        'champion_last_name' => self::STRING,
        'champion_phone' => self::STRING,
        'champion_email' => self::EMAIL
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:import {user} {usermeta} {step=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Saves user meta data from Postgres wp_usermeta to MongoDb user_data table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$result = \DB::table('users')->whereNotNull('wp_user_id')->delete();
        $step = $this->argument('step');

        if ((int)$step <= 1) {
            $this->info('Step 1');
            $result = DB::statement(DB::raw("set standard_conforming_strings = 'off'"));
            $result = DB::statement(DB::raw("SET backslash_quote = 'on'"));

            $result = Schema::dropIfExists('wp_users');
            $result = DB::statement("CREATE TABLE wp_users (
              ID bigint NOT NULL,
              user_login varchar(60) NOT NULL DEFAULT '',
              user_pass varchar(255)  NOT NULL DEFAULT '',
              user_nicename varchar(50)  NOT NULL DEFAULT '',
              user_email varchar(100) NOT NULL DEFAULT '',
              user_url varchar(100) NOT NULL DEFAULT '',
              user_registered timestamp NOT NULL,
              user_activation_key varchar(255) NOT NULL DEFAULT '',
              user_status int NOT NULL DEFAULT '0',
              display_name varchar(250) NOT NULL DEFAULT '',
              PRIMARY KEY (ID)
              );"
            );

            $userDump = $this->argument('user');
            $result = \DB::unprepared(file_get_contents($userDump));
        }

        if ((int)$step <= 2) {
            $this->info('Step 2');
            $result = Schema::dropIfExists('wp_usermeta');
            $result = DB::statement("CREATE TABLE wp_usermeta (
                               umeta_id bigint NOT NULL,
                               user_id bigint NOT NULL DEFAULT '0',
                               meta_key varchar(255) DEFAULT NULL,
                               meta_value text ,
                               PRIMARY KEY (umeta_id)
                    );"
            );

            $userMetaDump = $this->argument('usermeta');;
            $result = \DB::unprepared(file_get_contents($userMetaDump));
        }

        if ((int)$step <= 3) {
            $this->info('Step 3');
            $result = \DB::statement("insert into users(id, wp_user_id, email, password, created_at, name) 
            select  md5(random()::text || clock_timestamp()::text)::uuid, id, user_email, user_pass, user_registered, display_name from wp_users;");
        }

        if ((int)$step <= 4) {
            $this->info('Step 4');
            $result = \DB::statement("update users
                set first_name = wp_usermeta.meta_value
                from wp_usermeta
                where wp_usermeta.user_id = users.wp_user_id and wp_usermeta.meta_key = 'first_name' and wp_usermeta.meta_value != '';"
            );
            $result = \DB::statement("update users
                set last_name = wp_usermeta.meta_value
                from wp_usermeta
                where wp_usermeta.user_id = users.wp_user_id and wp_usermeta.meta_key = 'last_name' and wp_usermeta.meta_value != '';"
            );
            $result = \DB::statement("update users
                set old_user_id = wp_usermeta.meta_value
                from wp_usermeta
                where wp_usermeta.user_id = users.wp_user_id and wp_usermeta.meta_key = 'old_user_id' and wp_usermeta.meta_value != '';"
            );
            $result = \DB::statement("update users
                set user_litmos_id = wp_usermeta.meta_value
                from wp_usermeta
                where wp_usermeta.user_id = users.wp_user_id and wp_usermeta.meta_key = 'user_litmos_id' and wp_usermeta.meta_value != '';"
            );
            $result = \DB::statement("update users set first_name = split_part(name, ' ', 1)  where (first_name = '' or first_name is null) and array_length(regexp_split_to_array(trim(name), E'\\W+'), 1)  = 2");
            $result = \DB::statement("update users set last_name = split_part(name, ' ', 2)  where (last_name = '' or last_name is null) and array_length(regexp_split_to_array(trim(name), E'\\W+'), 1)  = 2");
        }

        if ((int)$step <= 5) {
            UserData::query()->delete();

            \DB::connection('mongodb')->collection('user_data')->raw(function($collection) {
                return $collection->createIndex([UserData::USER_ID => 1, UserData::CLIENT_REFERENCE => 1]);
            });

            $this->info('Step 5');
            $counter = 0;
            $result = DB::table('users')->orderBy('id')
                ->chunk(1000, function ($users) use (&$counter) {
                    foreach ($users as $user) {
                        $dataToCopy = \Db::table('wp_usermeta')->where('user_id', (int)$user->wp_user_id)->get();
                        if ($dataToCopy->isNotEmpty()) {
                            $userData = [
                                UserData::USER_ID => $user->id,
                                UserData::NAME => UserData::GROUP_WP_IMPORT
                            ];

                            foreach ($dataToCopy as $row) {
                                if (in_array($row->meta_key, self::EXCLUDE_KEY_EXACT) === false
                                    && Str::is(self::EXCLUDE_KEY_LIKE, $row->meta_key) === false
                                    && in_array($row->meta_value, self::EXCLUDE_VALUE_EXACT) === false
                                    && Str::is(self::EXCLUDE_VALUE_LIKE, $row->meta_value) === false
                                ) {
                                    if ($unsetialized = @unserialize($row->meta_value) !== false || $row->meta_value === 'b:0;') {
                                        $userData[$row->meta_key] = unserialize($row->meta_value);
                                    } else {
                                        $userData[$row->meta_key] = $row->meta_value;
                                    }
                                } else if ($row->meta_key === 'wp_capabilities') {
                                    $roles = unserialize($row->meta_value);
                                    $userData[self::FIELD_ROLES] = $roles;
                                }
                            }

                            $userDataObject = UserData::where(UserData::USER_ID, '=', $user->id)->whereNull(UserData::CLIENT_REFERENCE)->first();
                            if (empty($userDataObject)) {
                                $userDataObject = new UserData($userData);
                                $userDataObject->save();
                            } else {
                                $userDataObject->update($userData);
                            }
                        }
                    }

                    $counter += 1000;
                    $this->info($counter . ' users proccessed');
                });
        }

        if ((int)$step <= 6) {
            $this->info('Step 6');

            $administrator = User::where(User::NAME, '=', \UserTableSeeder::SUPER_ADMIN_NAME)->first();
            $sharedMetaTypeData = [
                MetaType::CLIENT_ID => self::CLIENT_ID,
                MetaType::NAME => self::GROUP_CLIENT,
                MetaType::TYPE => MetaType::TYPE_USER_DATA,
                MetaType::USER_ID => $administrator->id,
            ];
            $sharedMetaType = MetaType::where($sharedMetaTypeData)->first();
            if (empty($sharedMetaType)) {
                factory(MetaType::class)->create($sharedMetaTypeData);
            }

            $counter = 0;
            $result = DB::connection('mongodb')->collection('user_data')
                ->where(UserData::NAME, '=', UserData::GROUP_WP_IMPORT)
                ->orderBy(UserData::ID)
                ->chunk(1000, function ($userDatas) use (&$counter) {
                    foreach ($userDatas as $userData) {
                        $dataToCopy = collect($userData)->only(array_keys(self::CLIENT_GROUP_FIELDS));
                        if ($dataToCopy->isNotEmpty()) {
                            $mobileAppData = [
                                UserData::USER_ID => $userData[UserData::USER_ID],
                                UserData::NAME => self::GROUP_CLIENT,
                                UserData::CLIENT_REFERENCE => self::CLIENT_ID,
                            ];

                            foreach ($dataToCopy as $field => $value) {
                                $mobileAppData[$field] = [
                                    self::TYPE => self::CLIENT_GROUP_FIELDS[$field],
                                    self::VALUE => $value
                                ];
                            }

                            $userDataObject = UserData::where('user_id', '=', $userData[UserData::USER_ID])
                                ->whereNull(UserData::CLIENT_REFERENCE)
                                ->where(UserData::NAME, '=', self::GROUP_CLIENT)
                                ->first();

                            if (empty($userDataObject)) {
                                $userDataObject = new UserData($mobileAppData);
                                $userDataObject->save();
                            } else {
                                $userDataObject->update($mobileAppData);
                            }
                        }
                    }

                    $counter += 1000;
                    $this->info($counter . ' users proccessed');
                });
        }

        if ((int)$step <= 7) {
            $this->info('Step 7');
            $administratorsWpIds = DB::table('wp_usermeta')->where('meta_key', '=', 'wp_capabilities')
                ->where('meta_value', 'like','%administrator%')->pluck('user_id')->toArray();
            $administrators = User::whereIn('wp_user_id', $administratorsWpIds)->get();
            foreach ($administrators as $admin) {
                /** @var User $admin */
                $admin->attachRole(1);
            }


            $administrator = User::where(User::NAME, '=', \UserTableSeeder::SUPER_ADMIN_NAME)->first();
            $sharedMetaTypeData = [
                MetaType::NAME => self::GROUP_SHARED,
                MetaType::TYPE => MetaType::TYPE_USER_DATA,
                MetaType::USER_ID => $administrator->id,
            ];
            $sharedMetaType = MetaType::where($sharedMetaTypeData)->first();
            if (empty($sharedMetaType)) {
                factory(MetaType::class)->create($sharedMetaTypeData);
            }

            $counter = 0;
            $result = DB::connection('mongodb')->collection('user_data')
                ->where(UserData::NAME, '=', UserData::GROUP_WP_IMPORT)
                ->orderBy(UserData::ID)
                ->chunk(1000, function ($userDatas) use (&$counter) {
                    foreach ($userDatas as $userData) {
                        $dataToCopy = collect($userData)->only([UserData::USER_ID, self::FIELD_ROLES]);
                        $dataToCopy[self::FIELD_ROLES] = collect($dataToCopy[self::FIELD_ROLES])
                            ->filter(function ($value, $key) {
                                return strpos($key, 'wpml_') === false && (bool)$value === true;
                            })
                            ->keys()
                            ->toArray();

                        if ($dataToCopy->isNotEmpty()) {
                            $rolesData = [
                                UserData::USER_ID => $dataToCopy[UserData::USER_ID],
                                UserData::NAME => self::GROUP_SHARED,
                                self::FIELD_ROLES => [
                                    self::TYPE => self::LIST,
                                    self::VALUE => $dataToCopy[self::FIELD_ROLES]
                                ]
                            ];

                            $rolesDataObject = UserData::where('user_id', '=', $dataToCopy[UserData::USER_ID])
                                ->whereNull(UserData::CLIENT_REFERENCE)
                                ->where(UserData::NAME, '=', self::GROUP_SHARED)
                                ->first();

                            if (empty($rolesDataObject)) {
                                $rolesDataObject = new UserData($rolesData);
                                $rolesDataObject->save();
                            } else {
                                $rolesDataObject->update($rolesData);
                            }
                        }
                    }

                    $counter += 1000;
                    $this->info($counter . ' users proccessed');
                });
        }

        $this->info('FINISH');
    }

}
