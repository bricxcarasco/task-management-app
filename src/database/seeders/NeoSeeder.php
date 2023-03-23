<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Factories\NeoFactory;
use Database\Factories\NeoProfileFactory;

class NeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NeoFactory::new()
            ->count(2)
            ->has(NeoProfileFactory::new(), 'neo_profile')
            ->create();
    }
}
