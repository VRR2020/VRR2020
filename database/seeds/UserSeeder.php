<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Criar usuário administrador padrão
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@crm.com',
            'password' => Hash::make('123456'),
        ]);

        // Criar usuário de teste
        User::create([
            'name' => 'Victor Rocha',
            'email' => 'victor@crm.com',
            'password' => Hash::make('123456'),
        ]);
    }
}
