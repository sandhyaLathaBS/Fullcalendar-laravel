<?php

namespace Database\Seeders;

use App\Models\Doctors;
use App\Models\OperationTheatres;
use App\Models\Pateints;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Doctors::factory(10)->create();
        Pateints::factory(10)->create();
        OperationTheatres::insert([
            ['room_no' => 202, 'start_time' => '00:00:00', 'end_time' => '23:59:59', 'is_active' => 1, 'created_at' =>  date("Y-m-d H:i:s"), 'updated_at' =>  date("Y-m-d H:i:s")],
            ['room_no' => 203, 'start_time' => '00:00:00', 'end_time' => '23:59:59', 'is_active' => 1, 'created_at' =>  date("Y-m-d H:i:s"), 'updated_at' =>  date("Y-m-d H:i:s")],
            ['room_no' => 204, 'start_time' => '00:00:00', 'end_time' => '23:59:59', 'is_active' => 1, 'created_at' =>  date("Y-m-d H:i:s"), 'updated_at' =>  date("Y-m-d H:i:s")],
            ['room_no' => 205, 'start_time' => '00:00:00', 'end_time' => '23:59:59', 'is_active' => 1, 'created_at' =>  date("Y-m-d H:i:s"), 'updated_at' =>  date("Y-m-d H:i:s")],
            ['room_no' => 206, 'start_time' => '00:00:00', 'end_time' => '23:59:59', 'is_active' => 1, 'created_at' =>  date("Y-m-d H:i:s"), 'updated_at' =>  date("Y-m-d H:i:s")],
            ['room_no' => 207, 'start_time' => '00:00:00', 'end_time' => '23:59:59', 'is_active' => 1, 'created_at' =>  date("Y-m-d H:i:s"), 'updated_at' =>  date("Y-m-d H:i:s")],
            ['room_no' => 208, 'start_time' => '00:00:00', 'end_time' => '23:59:59', 'is_active' => 1, 'created_at' =>  date("Y-m-d H:i:s"), 'updated_at' =>  date("Y-m-d H:i:s")],
        ]);
    }
}