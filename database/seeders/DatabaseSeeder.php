<?php

namespace Database\Seeders;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@anim-ed.io'], [
            'name' => 'Admin Control',
            'password' => env('ADMIN_SEED_PASSWORD', 'adminsecret'),
            'is_admin' => true,
        ]);

        User::updateOrCreate(['email' => 'demo@anim-ed.io'], [
            'name' => 'Demo Viewer',
            'password' => 'demo1234',
            'avatar' => 'https://api.dicebear.com/7.x/adventurer/svg?seed=Demo%20Viewer',
        ]);

        $ads = [
            ['Crunchyroll Premium - Stream Anime Now!', 'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=300&q=80', 'https://crunchyroll.com', 'watch'],
            ['BookWalker - Manga Digital Store', 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=300&q=80', 'https://bookwalker.jp', 'read'],
            ['Anim-ed Premium Membership', 'https://images.unsplash.com/photo-1618336753974-aae8e04506aa?w=300&q=80', 'https://anim-ed.io/premium', 'both'],
        ];

        foreach ($ads as [$title, $image, $target, $type]) {
            Ad::firstOrCreate(['title' => $title], [
                'image_url' => $image,
                'target_url' => $target,
                'type' => $type,
            ]);
        }
    }
}
