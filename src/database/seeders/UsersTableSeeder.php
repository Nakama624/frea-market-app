<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $param = [
    'name' => 'test',
    'email' => 'test@gmail.com',
    'password' => '123456789',
    'postcode' => '1111111',
    'address' => '東京都品川区11',
    'building' => 'テストビルディング',
    ];
    DB::table('users')->insert($param);
    $param = [
    'name' => 'test1',
    'email' => 'test1@gmail.com',
    'password' => '987654321',
    'postcode' => '2222222',
    'address' => '東京都品川区22',
    'building' => 'ビルディング',
    ];
    DB::table('users')->insert($param);
  }
}
