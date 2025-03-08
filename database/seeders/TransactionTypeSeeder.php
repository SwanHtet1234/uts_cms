<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TransactionType;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactionTypes = [
            'Education',
            'Food & Dining',
            'Groceries',
            'Healthcare',
            'Transportation',
            'Entertainment',
            'Travel',
            'Utilities',
            'Rent',
            'Mortgage',
            'Insurance',
            'Clothing',
            'Electronics',
            'Gifts',
            'Charity',
            'Savings',
            'Investments',
            'Loan Payment',
            'Credit Card Payment',
            'Taxes',
            'Fuel',
            'Automotive',
            'Home Improvement',
            'Childcare',
            'Pet Care',
            'Subscriptions',
            'Fitness',
            'Hobbies',
            'Personal Care',
            'Miscellaneous',
        ];

        foreach ($transactionTypes as $type) {
            TransactionType::create([
                'typeName' => $type,
            ]);
        }
    }
}
