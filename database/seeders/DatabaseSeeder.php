<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        DB::table('meta_data')->insert([
            'key' => 'current_index_iteration',
            'value' => '0',
        ]);
        DB::table('meta_data')->insert([
            'key' => 'current_content_id',
            'value' => '1',
        ]);
        DB::table('meta_data')->insert([
            'key' => 'current_sitemap_iteration',
            'value' => '0',
        ]);
    }
}
