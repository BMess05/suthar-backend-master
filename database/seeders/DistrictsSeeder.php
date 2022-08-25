<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\District;

class DistrictsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = [
            [  
                "state" => "Haryana",
                "districts" => [  
                   "Ambala",
                   "Bhiwani",
                   "Charkhi Dadri",
                   "Faridabad",
                   "Fatehabad",
                   "Gurgaon",
                   "Hisar",
                   "Jhajjar",
                   "Jind",
                   "Kaithal",
                   "Karnal",
                   "Kurukshetra",
                   "Mahendragarh",
                   "Mewat",
                   "Palwal",
                   "Panchkula",
                   "Panipat",
                   "Rewari",
                   "Rohtak",
                   "Sirsa",
                   "Sonipat",
                   "Yamunanagar"
                ]
            ],
            [
                "state" => "Punjab",
                "districts" => [  
                    "Amritsar",
                    "Barnala",
                    "Bathinda",
                    "Faridkot",
                    "Fatehgarh Sahib",
                    "Fazilka",
                    "Ferozepur",
                    "Gurdaspur",
                    "Hoshiarpur",
                    "Jalandhar",
                    "Kapurthala",
                    "Ludhiana",
                    "Mansa",
                    "Moga",
                    "Muktsar",
                    "Nawanshahr (Shahid Bhagat Singh Nagar)",
                    "Pathankot",
                    "Patiala",
                    "Rupnagar",
                    "Sahibzada Ajit Singh Nagar (Mohali)",
                    "Sangrur",
                    "Tarn Taran"
                ]
            ],
            [
                "state" => "Delhi",
                "districts" => [  
                    "Central Delhi",
                    "East Delhi",
                    "New Delhi",
                    "North Delhi",
                    "North East  Delhi",
                    "North West  Delhi",
                    "Shahdara",
                    "South Delhi",
                    "South East Delhi",
                    "South West  Delhi",
                    "West Delhi"
                ]
            ],
            [
                "state" => "Uttar Pradesh",
                "districts" => [  
                    "Agra",
                    "Aligarh",
                    "Allahabad",
                    "Ambedkar Nagar",
                    "Amethi (Chatrapati Sahuji Mahraj Nagar)",
                    "Amroha (J.P. Nagar)",
                    "Auraiya",
                    "Azamgarh",
                    "Baghpat",
                    "Bahraich",
                    "Ballia",
                    "Balrampur",
                    "Banda",
                    "Barabanki",
                    "Bareilly",
                    "Basti",
                    "Bhadohi",
                    "Bijnor",
                    "Budaun",
                    "Bulandshahr",
                    "Chandauli",
                    "Chitrakoot",
                    "Deoria",
                    "Etah",
                    "Etawah",
                    "Faizabad",
                    "Farrukhabad",
                    "Fatehpur",
                    "Firozabad",
                    "Gautam Buddha Nagar",
                    "Ghaziabad",
                    "Ghazipur",
                    "Gonda",
                    "Gorakhpur",
                    "Hamirpur",
                    "Hapur (Panchsheel Nagar)",
                    "Hardoi",
                    "Hathras",
                    "Jalaun",
                    "Jaunpur",
                    "Jhansi",
                    "Kannauj",
                    "Kanpur Dehat",
                    "Kanpur Nagar",
                    "Kanshiram Nagar (Kasganj)",
                    "Kaushambi",
                    "Kushinagar (Padrauna)",
                    "Lakhimpur - Kheri",
                    "Lalitpur",
                    "Lucknow",
                    "Maharajganj",
                    "Mahoba",
                    "Mainpuri",
                    "Mathura",
                    "Mau",
                    "Meerut",
                    "Mirzapur",
                    "Moradabad",
                    "Muzaffarnagar",
                    "Pilibhit",
                    "Pratapgarh",
                    "RaeBareli",
                    "Rampur",
                    "Saharanpur",
                    "Sambhal (Bhim Nagar)",
                    "Sant Kabir Nagar",
                    "Shahjahanpur",
                    "Shamali (Prabuddh Nagar)",
                    "Shravasti",
                    "Siddharth Nagar",
                    "Sitapur",
                    "Sonbhadra",
                    "Sultanpur",
                    "Unnao",
                    "Varanasi"
                ]
            ],
            [
                "state" => "Himachal Pradesh",
                "districts" => [  
                    "Bilaspur",
                    "Chamba",
                    "Hamirpur",
                    "Kangra",
                    "Kinnaur",
                    "Kullu",
                    "Lahaul &amp; Spiti",
                    "Mandi",
                    "Shimla",
                    "Sirmaur (Sirmour)",
                    "Solan",
                    "Una"
                ]
            ]
        ];
        foreach($array as $row) {
            foreach($row['districts'] as $district) {
                $dist = new District();
                $dist->state = $row['state'];
                $dist->district = $district;
                $dist->save();
            }
        }
    }
}
