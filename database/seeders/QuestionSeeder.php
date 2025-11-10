<?php

namespace Database\Seeders;

use App\Models\IllegalObjectQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        IllegalObjectQuestion::query()->truncate();
        $path = storage_path() . "/json/questions.json";
        $questions = json_decode(file_get_contents($path), true);
        foreach ($questions as $question) {
            IllegalObjectQuestion::query()->create([
                'id' => $question['id'],
                'name' => $question['description'],
                'role' => $question['group'],
                'ball' => $question['priority'],
                'illegal_question_type_id' => $question['category'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
