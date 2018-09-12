<?php

use Illuminate\Database\Seeder;

use App\Entities\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::truncate();

        Role::create([
            [
                'name'          => Role::NAME['ADMINISTRATOR'],
                'display_name'  => 'Quản trị viên',
                'description'   => 'Quản trị viên là người có toàn quyền trong hệ thống',
            ],
            [
                'name'          => Role::NAME['CONGTACVIEN'],
                'display_name'  => 'Cộng tác viên',
                'description'   => 'Cộng tác viên là người thay thế quản trị viên đăng bài và bị giới hạng quyền',
            ],
            [
                'name'          => Role::NAME['VIEWER'],
                'display_name'  => 'Người sử dụng',
                'description'   => 'Người sử dụng là người trực tiếp tham gia vào website',
            ]
        ]);
    }
}
