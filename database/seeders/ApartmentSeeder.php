<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Apartment;
use App\Models\ApartmentImages;

class ApartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $apartment = Apartment::create([

            'owner_id' => 3,
            'country' =>'Syria',
            'province'=>'Damascus',
            'description'=>'A beautiful apartment in the heart of the city.',
            'rooms'=>'4',
            'price'=>'250',

          ]);


        ApartmentImages::create(['apartment_id' => $apartment->id, 'image_path' => 'images/apartments/apartment11.jpg',]);
        ApartmentImages::create(['apartment_id' => $apartment->id, 'image_path' => 'images/apartments/apartment12.jpg',]);
        ApartmentImages::create(['apartment_id' => $apartment->id, 'image_path' => 'images/apartments/apartment13.jpg',]);

        $apartment = Apartment::create([

            'owner_id' => 3,
            'country' => 'Syria',
            'province' => 'Homs',
            'description' => 'Palace',
            'rooms' => '10',
            'price' => '25000',

        ]);

        $apartment = Apartment::create([

            'owner_id' => 3,
            'country' => 'Syria',
            'province' => 'Hama',
            'description' => 'Big House',
            'rooms' => '15',
            'price' => '250000',

        ]);

    }
}
