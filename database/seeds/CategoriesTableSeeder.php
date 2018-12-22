<?php

use Illuminate\Database\Seeder;
use App\Entities\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //DU LỊCH ĐÀ NẴNG, ĐIỂM ĐẾN ĐÀ NẴNG, SỰ KIỆN - LỄ HỘI,  ẨM THỰC ĐÀ NẴNG,  CẨM NANG - DỊCH VỤ DU LỊCH, LIÊN HỆ
        Category::truncate();

        $data = [
            [
                'name_category'    => 'DU LỊCH ĐÀ NẴNG',
                'uri_category'     => 'du-lich-da-nang',
                'type_category'    => 'Du lịch',
                'description'      => 'Du lịch đà nẵng nói về các điểm đến khi du khách tham quan đà nẵng. Giới thiệu về những danh lam nổi tiếng và cũng như lịch sử của đà nẵng',
            ],
            [
                'name_category'    => 'ĐIỂM ĐẾN ĐÀ NẴNG',
                'uri_category'     => 'diem-den-da-nang',
                'type_category'    => 'Điểm đến',
                'description'      => 'Điểm đến Đà Nẵng chuyên cập nhật về những địa điểm nhỏ lẻ, những vùng ven tươi đẹp để du khách có thể dễ dàng tận hưởng,  chụp hình ...',
            ],
            [
                'name_category'    => 'SỰ KIỆN - LỄ HỘI',
                'uri_category'     => 'su-kien-le-hoi',
                'type_category'    => 'Sự kiện',
                'description'      => 'Sự kiện - lễ hội chuyên cập nhật về các sự kiện đã và đang diễn ra tại Đà Nẵng, kèm theo đó là các lễ hội thường xuyên xảy ra ở Đà Nẵng',
            ],
            [
                'name_category'    => 'ẨM THỰC ĐÀ NẴNG',
                'uri_category'     => 'am-thuc-da-nang',
                'type_category'    => 'Ẩm thực',
                'description'      => 'Ẩm thực đà nẵng chuyên về các món ăn đặc sản cũng như là các món ăn hải sản khi du khách đến du lịch Đà Nẵng',
            ],
            [
                'name_category'    => 'CẨM NANG - DỊCH VỤ DU LỊCH',
                'uri_category'     => 'cam-nang-dich-vu-da-nang',
                'type_category'    => 'Cẩm nang',
                'description'      => 'Cẩm nang - dịch vụ chuyên cập nhật về các dịch vụ tại đà nẵng như vui chơi, các nhà nghĩ - khách sạn. Kèm theo đó là những mẹo vặt khi đi du lịch Đà Nẵng',
            ]
        ];

        Category::insert($data);
    }
}
