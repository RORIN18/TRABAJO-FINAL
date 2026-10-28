<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $email = env('ADMIN_EMAIL');
            $password = env('ADMIN_PASSWORD');

            if (filled($email) && filled($password)) {
                User::firstOrCreate(['email' => $email], [
                    'name' => 'Admin',
                    'password' => Hash::make($password),
                    'role' => 'admin',
                ]);
            }

            return;
        }

        User::firstOrCreate(['email'=>'admin@admin.com'],[
            'name'=>'Admin',
            'password'=>Hash::make('admin123'),
            'role'=>'admin'
        ]);
        User::firstOrCreate(['email'=>'visitante@test.com'],[
            'name'=>'Visitante',
            'password'=>Hash::make('visitante123'),
            'role'=>'visitante'
        ]);
    }
}
