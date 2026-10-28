<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder {
    public function run(): void {
        $this->call([
            InstitutoSeeder::class,
            EstudiantesSeeder::class,
            AdminSeeder::class,
            DemoAcademicoSeeder::class,
        ]);
    }
}
