<?php

use Illuminate\Database\Seeder;

class ApplicantsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        foreach(range(1, 20) as $i) {
            $data[] = array(
                'email' => $faker->email,
                'name' => $faker->firstName,
                'isHired' => $faker->boolean,
                'created_at' => $faker->dateTimeBetween($startDate = '-8 months', $endDate = 'now', $timezone = null)
            );
        }

        DB::table('applicants')->insert($data);
    }
}
