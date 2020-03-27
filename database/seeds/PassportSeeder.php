<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class PassportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('APP_ENV') !== "production") {
            DB::table('oauth_clients')->truncate();
            DB::table('oauth_clients')->insert([
                [
                    'name' => 'Lumen Personal Access Client',
                    'secret' => 'utmqsWGT3e3hdnvNstMsRyUyKGDJ1g19xjRmCGH6',
                    'redirect' => 'http://localhost',
                    'personal_access_client' => 1,
                    'password_client' => 0,
                    'revoked' => 0
                ],
                [
                    'name' => 'Lumen Password Grant Client',
                    'secret' => 'LyJWiWoOdiOZ5mWzJAA8b30odTuSNELovgqNeAx1',
                    'redirect' => 'http://localhost',
                    'personal_access_client' => 0,
                    'password_client' => 1,
                    'revoked' => 0
                ]
            ]);
        }
    }
}
