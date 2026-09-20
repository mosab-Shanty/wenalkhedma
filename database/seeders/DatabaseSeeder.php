<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\Location;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Users (Admin, Moderator, Demo User)
        $admin = User::create([
            'full_name' => 'الأدمن الرئيسي',
            'email' => 'admin@win.com',
            'phone' => '0599000001',
            'password_hash' => Hash::make('12345678'),
            'role' => 'admin',
            'status' => 'active'
        ]);

        $moderator = User::create([
            'full_name' => 'المشرف العام',
            'email' => 'moderator@win.com',
            'phone' => '0599000002',
            'password_hash' => Hash::make('12345678'),
            'role' => 'moderator',
            'status' => 'active'
        ]);

        $user = User::create([
            'full_name' => 'مستخدم تجريبي',
            'email' => 'user@win.com',
            'phone' => '0599000003',
            'password_hash' => Hash::make('12345678'),
            'role' => 'user',
            'status' => 'active'
        ]);

        // 2. Create Categories
        $categoriesData = [
            'صيدليات' => ['description' => 'صيدليات ومستلزمات طبية ودواية متاحة', 'icon' => '/img/Pharmacy.png'],
            'مخابز' => ['description' => 'مخابز ومنافذ بيع وتوزيع الخبز وطحين', 'icon' => '/img/Water Point.png'],
            'محطات مياه' => ['description' => 'نقاط صهاريج وتوزيع مياه الشرب والتحلية', 'icon' => '/img/Water Point.png'],
            'عيادات ومراكز طبية' => ['description' => 'مراكز الإسعاف الأولي والعيادات الشاملة', 'icon' => '/img/Hospital View.png'],
            'نقاط شحن كهرباء' => ['description' => 'أماكن شحن الهواتف والبطاريات الطاقة الشمسية', 'icon' => '/img/Water Point.png'],
        ];

        $createdCategories = [];
        foreach ($categoriesData as $name => $data) {
            $createdCategories[$name] = ServiceCategory::create([
                'name' => $name,
                'description' => $data['description'],
                'status' => 'active'
            ]);
        }

        // 3. Create Sample Initial Services
        $servicesSeed = [
            [
                'name' => 'صيدلية الرمال المركزية الطبية',
                'category' => 'صيدليات',
                'description' => 'توفير المستلزمات الطبية الأساسية، الأنسولين، وحليب الأطفال متوفر حالياً.',
                'phone' => '0599112233',
                'status' => 'open',
                'city' => 'غزة',
                'area' => 'الرمال الجنوبي',
                'address' => 'شارع عمر المختار بجوار برج الجلاء',
                'lat' => 31.5125,
                'lng' => 34.4468,
                'img' => '/img/Pharmacy.png',
            ],
            [
                'name' => 'مخبز اليازجي الأوتوماتيكي',
                'category' => 'مخابز',
                'description' => 'إنتاج الخبز البلدي والأوتوماتيكي مع خطوط توزيع سريعة للمواطنين.',
                'phone' => '0599445566',
                'status' => 'crowded',
                'city' => 'غزة',
                'area' => 'النصر',
                'address' => 'شارع النصر تقاطع اليرموك',
                'lat' => 31.5200,
                'lng' => 34.4500,
                'img' => '/default-service.png',
            ],
            [
                'name' => 'مخبز العائلات',
                'category' => 'مخابز',
                'description' => 'توفير الخبز الطازج يومياً والمعجنات، خدمة سريعة واستقبال طلبات الأهالي.',
                'phone' => '0599556677',
                'status' => 'open',
                'city' => 'غزة',
                'area' => 'الرمال',
                'address' => 'شارع الوحدة بالقرب من مفترق ضبيط',
                'lat' => 31.5165,
                'lng' => 34.4540,
                'img' => '/default-service.png',
            ],
            [
                'name' => 'نقطة تحلية مياه الشرب النقية',
                'category' => 'محطات مياه',
                'description' => 'تعبئة صهاريج وجالونات مياه الشرب مفلترة ومحلاة مجاناً.',
                'phone' => '0599778899',
                'status' => 'open',
                'city' => 'دير البلح',
                'area' => 'الوسطى',
                'address' => 'شارع البيئة قرب مستشفى الأقصى',
                'lat' => 31.4167,
                'lng' => 34.3500,
                'img' => '/img/Water Point.png',
            ],
            [
                'name' => 'مركز الشفاء الطبي والطوارئ',
                'category' => 'عيادات ومراكز طبية',
                'description' => 'قسم الإسعاف الأولي وعيادة الأطفال الشاملة استقبال الطوارئ 24 ساعة.',
                'phone' => '0599001122',
                'status' => 'open',
                'city' => 'غزة',
                'area' => 'الرمال الشمالي',
                'address' => 'شارع عز الدين القسام',
                'lat' => 31.5250,
                'lng' => 34.4400,
                'img' => '/img/Hospital View.png',
            ],
            [
                'name' => 'نقطة شحن الهواتف بالطاقة الشمسية',
                'category' => 'نقاط شحن كهرباء',
                'description' => 'شحن الهواتف الذكية وبطاريات الإضاءة مجاناً بالطاقة الشمسية.',
                'phone' => '0599334455',
                'status' => 'open',
                'city' => 'خانيونس',
                'area' => 'البلد',
                'address' => 'شارع البحر قرب كراج رفح',
                'lat' => 31.3450,
                'lng' => 34.3050,
                'img' => '/default-service.png',
            ],
        ];

        foreach ($servicesSeed as $seed) {
            $cat = $createdCategories[$seed['category']] ?? reset($createdCategories);

            $service = Service::create([
                'user_id' => $user->user_id,
                'category_id' => $cat->category_id,
                'name' => $seed['name'],
                'description' => $seed['description'],
                'phone' => $seed['phone'],
                'current_status' => $seed['status'],
                'is_verified' => true,
                'approval_status' => 'approved',
            ]);

            Location::create([
                'service_id' => $service->service_id,
                'governorate' => 'غزة',
                'city' => $seed['city'],
                'area' => $seed['area'],
                'address_text' => $seed['address'],
                'latitude' => $seed['lat'],
                'longitude' => $seed['lng'],
            ]);

            Document::create([
                'user_id' => $user->user_id,
                'service_id' => $service->service_id,
                'file_type' => 'image',
                'file_url' => $seed['img'],
                'description' => 'صورة توضيحية للمرفق',
                'status' => 'approved',
            ]);
        }
    }
}
