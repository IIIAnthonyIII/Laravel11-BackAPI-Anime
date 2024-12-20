<?php

namespace Database\Seeders;

use App\Models\Type;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder {
    public function run(): void {
        $types = [
            ['id'=>1, 'name'=>'Serie', 'color'=>'#003cff', 'user_create'=>1],
            ['id'=>2, 'name'=>'Ova', 'color'=>'#ff0000', 'user_create'=>1],
            ['id'=>3, 'name'=>'Ona', 'color'=>'#f200ff', 'user_create'=>1],
            ['id'=>4, 'name'=>'Pelicula', 'color'=>'#00ff95', 'user_create'=>1],
            ['id'=>5, 'name'=>'Especial', 'color'=>'#f2ff00', 'user_create'=>1]
        ];
        foreach ($types as $type) {
            Type::create($type);
        }
    }
}
