<?php

namespace Database\Seeders;

use App\Models\SdgGoal;
use Illuminate\Database\Seeder;

class SdgGoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $goals = [
            ['number' => 1,  'title' => 'No Poverty',                                        'label' => '1: No Poverty'],
            ['number' => 2,  'title' => 'Zero Hunger',                                       'label' => '2: Zero Hunger'],
            ['number' => 3,  'title' => 'Good Health and Well-Being',                         'label' => '3: Good Health and Well-Being'],
            ['number' => 4,  'title' => 'Quality Education',                                  'label' => '4: Quality Education'],
            ['number' => 5,  'title' => 'Gender Equality',                                    'label' => '5: Gender Equality'],
            ['number' => 6,  'title' => 'Clean Water and Sanitation',                         'label' => '6: Clean Water and Sanitation'],
            ['number' => 7,  'title' => 'Affordable and Clean Energy',                        'label' => '7: Affordable and Clean Energy'],
            ['number' => 8,  'title' => 'Decent Work and Economic Growth',                    'label' => '8: Decent Work and Economic Growth'],
            ['number' => 9,  'title' => 'Industry, Innovation, and Infrastructure',           'label' => '9: Industry, Innovation, and Infrastructure'],
            ['number' => 10, 'title' => 'Reduced Inequality',                                 'label' => '10: Reduced Inequality'],
            ['number' => 11, 'title' => 'Sustainable Cities and Communities',                 'label' => '11: Sustainable Cities and Communities'],
            ['number' => 12, 'title' => 'Responsible Consumption and Production',             'label' => '12: Responsible Consumption and Production'],
            ['number' => 13, 'title' => 'Climate Action',                                     'label' => '13: Climate Action'],
            ['number' => 14, 'title' => 'Life Below Water',                                   'label' => '14: Life Below Water'],
            ['number' => 15, 'title' => 'Life on Land',                                       'label' => '15: Life on Land'],
            ['number' => 16, 'title' => 'Peace, Justice, and Strong Institutions',            'label' => '16: Peace, Justice, and Strong Institutions'],
            ['number' => 17, 'title' => 'Partnerships for the Goals',                         'label' => '17: Partnerships for the Goals'],
        ];

        foreach ($goals as $goal) {
            SdgGoal::updateOrCreate(
                ['number' => $goal['number']],
                ['title' => $goal['title'], 'label' => $goal['label']]
            );
        }
    }
}
