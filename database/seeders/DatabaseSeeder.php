<?php

namespace Database\Seeders;

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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin Control',
            'email' => 'admin@anim-ed.io',
            'password' => bcrypt('adminsecret'),
            'is_admin' => true,
        ]);

        \App\Models\Ad::create([
            'title' => 'Crunchyroll Premium - Stream Anime Now!',
            'image_url' => 'https://images.unsplash.com/photo-1578632767115-351597cf2477?w=300&q=80',
            'target_url' => 'https://crunchyroll.com',
            'type' => 'watch',
        ]);

        \App\Models\Ad::create([
            'title' => 'BookWalker - Manga Digital Store',
            'image_url' => 'https://images.unsplash.com/photo-1607604276583-eef5d076aa5f?w=300&q=80',
            'target_url' => 'https://bookwalker.jp',
            'type' => 'read',
        ]);

        \App\Models\Ad::create([
            'title' => 'Anim-ed Premium Membership',
            'image_url' => 'https://images.unsplash.com/photo-1618336753974-aae8e04506aa?w=300&q=80',
            'target_url' => 'https://anim-ed.io/premium',
            'type' => 'both',
        ]);
    }
}
