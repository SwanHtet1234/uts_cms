<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SecurityQuestion;

class SecurityQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            "What is your mother's maiden name?",
            "What was the name of your first pet?",
            "What city were you born in?",
            "What is the name of your favorite teacher?",
            "What was the make and model of your first car?",
            "What is your favorite movie?",
            "What is the name of your childhood best friend?",
            "What is the name of the street you grew up on?",
            "What is your favorite book?",
            "What is the name of your favorite sports team?"
        ];

        foreach ($questions as $question) {
            SecurityQuestion::create([
                'question' => $question,
            ]);
        }
    }
}
