<?php

namespace Database\Seeders;

use App\Models\IllegalQuestionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        IllegalQuestionType::query()->truncate();
        $path = storage_path() . "/json/types.json";
        $types = json_decode(file_get_contents($path), true);
        foreach ($types as $type) {
            IllegalQuestionType::query()->insert([
                'id' => $type['id'],
                'name' => $type['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
