<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndustriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('industries')->delete();
        
        DB::table('industries')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Defense & Space',
            ),
            1 => 
            array (
                'id' => 3,
                'name' => 'Computer Hardware',
            ),
            2 => 
            array (
                'id' => 4,
                'name' => 'Computer Software',
            ),
            3 => 
            array (
                'id' => 5,
                'name' => 'Computer Networking',
            ),
            4 => 
            array (
                'id' => 6,
                'name' => 'Internet',
            ),
            5 => 
            array (
                'id' => 7,
                'name' => 'Semiconductors',
            ),
            6 => 
            array (
                'id' => 8,
                'name' => 'Telecommunications',
            ),
            7 => 
            array (
                'id' => 9,
                'name' => 'Law Practice',
            ),
            8 => 
            array (
                'id' => 10,
                'name' => 'Legal Services',
            ),
            9 => 
            array (
                'id' => 11,
                'name' => 'Management Consulting',
            ),
            10 => 
            array (
                'id' => 12,
                'name' => 'Biotechnology',
            ),
            11 => 
            array (
                'id' => 13,
                'name' => 'Medical Practice',
            ),
            12 => 
            array (
                'id' => 14,
                'name' => 'Hospital & Health Care',
            ),
            13 => 
            array (
                'id' => 15,
                'name' => 'Pharmaceuticals',
            ),
            14 => 
            array (
                'id' => 16,
                'name' => 'Veterinary',
            ),
            15 => 
            array (
                'id' => 17,
                'name' => 'Medical Devices',
            ),
            16 => 
            array (
                'id' => 18,
                'name' => 'Cosmetics',
            ),
            17 => 
            array (
                'id' => 19,
                'name' => 'Apparel & Fashion',
            ),
            18 => 
            array (
                'id' => 20,
                'name' => 'Sporting Goods',
            ),
            19 => 
            array (
                'id' => 21,
                'name' => 'Tobacco',
            ),
            20 => 
            array (
                'id' => 22,
                'name' => 'Supermarkets',
            ),
            21 => 
            array (
                'id' => 23,
                'name' => 'Food Production',
            ),
            22 => 
            array (
                'id' => 24,
                'name' => 'Consumer Electronics',
            ),
            23 => 
            array (
                'id' => 25,
                'name' => 'Consumer Goods',
            ),
            24 => 
            array (
                'id' => 26,
                'name' => 'Furniture',
            ),
            25 => 
            array (
                'id' => 27,
                'name' => 'Retail',
            ),
            26 => 
            array (
                'id' => 28,
                'name' => 'Entertainment',
            ),
            27 => 
            array (
                'id' => 29,
                'name' => 'Gambling & Casinos',
            ),
            28 => 
            array (
                'id' => 30,
                'name' => 'Leisure, Travel & Tourism',
            ),
            29 => 
            array (
                'id' => 31,
                'name' => 'Hospitality',
            ),
            30 => 
            array (
                'id' => 32,
                'name' => 'Restaurants',
            ),
            31 => 
            array (
                'id' => 33,
                'name' => 'Sports',
            ),
            32 => 
            array (
                'id' => 34,
                'name' => 'Food & Beverages',
            ),
            33 => 
            array (
                'id' => 35,
                'name' => 'Motion Pictures and Film',
            ),
            34 => 
            array (
                'id' => 36,
                'name' => 'Broadcast Media',
            ),
            35 => 
            array (
                'id' => 37,
                'name' => 'Museums and Institutions',
            ),
            36 => 
            array (
                'id' => 38,
                'name' => 'Fine Art',
            ),
            37 => 
            array (
                'id' => 39,
                'name' => 'Performing Arts',
            ),
            38 => 
            array (
                'id' => 40,
                'name' => 'Recreational Facilities and Services',
            ),
            39 => 
            array (
                'id' => 41,
                'name' => 'Banking',
            ),
            40 => 
            array (
                'id' => 42,
                'name' => 'Insurance',
            ),
            41 => 
            array (
                'id' => 43,
                'name' => 'Financial Services',
            ),
            42 => 
            array (
                'id' => 44,
                'name' => 'Real Estate',
            ),
            43 => 
            array (
                'id' => 45,
                'name' => 'Investment Banking',
            ),
            44 => 
            array (
                'id' => 46,
                'name' => 'Investment Management',
            ),
            45 => 
            array (
                'id' => 47,
                'name' => 'Accounting',
            ),
            46 => 
            array (
                'id' => 48,
                'name' => 'Construction',
            ),
            47 => 
            array (
                'id' => 49,
                'name' => 'Building Materials',
            ),
            48 => 
            array (
                'id' => 50,
                'name' => 'Architecture & Planning',
            ),
            49 => 
            array (
                'id' => 51,
                'name' => 'Civil Engineering',
            ),
            50 => 
            array (
                'id' => 52,
                'name' => 'Aviation & Aerospace',
            ),
            51 => 
            array (
                'id' => 53,
                'name' => 'Automotive',
            ),
            52 => 
            array (
                'id' => 54,
                'name' => 'Chemicals',
            ),
            53 => 
            array (
                'id' => 55,
                'name' => 'Machinery',
            ),
            54 => 
            array (
                'id' => 56,
                'name' => 'Mining & Metals',
            ),
            55 => 
            array (
                'id' => 57,
                'name' => 'Oil & Energy',
            ),
            56 => 
            array (
                'id' => 58,
                'name' => 'Shipbuilding',
            ),
            57 => 
            array (
                'id' => 59,
                'name' => 'Utilities',
            ),
            58 => 
            array (
                'id' => 60,
                'name' => 'Textiles',
            ),
            59 => 
            array (
                'id' => 61,
                'name' => 'Paper & Forest Products',
            ),
            60 => 
            array (
                'id' => 62,
                'name' => 'Railroad Manufacture',
            ),
            61 => 
            array (
                'id' => 63,
                'name' => 'Farming',
            ),
            62 => 
            array (
                'id' => 64,
                'name' => 'Ranching',
            ),
            63 => 
            array (
                'id' => 65,
                'name' => 'Dairy',
            ),
            64 => 
            array (
                'id' => 66,
                'name' => 'Fishery',
            ),
            65 => 
            array (
                'id' => 67,
                'name' => 'Primary/Secondary Education',
            ),
            66 => 
            array (
                'id' => 68,
                'name' => 'Higher Education',
            ),
            67 => 
            array (
                'id' => 69,
                'name' => 'Education Management',
            ),
            68 => 
            array (
                'id' => 70,
                'name' => 'Research',
            ),
            69 => 
            array (
                'id' => 71,
                'name' => 'Military',
            ),
            70 => 
            array (
                'id' => 72,
                'name' => 'Legislative Office',
            ),
            71 => 
            array (
                'id' => 73,
                'name' => 'Judiciary',
            ),
            72 => 
            array (
                'id' => 74,
                'name' => 'International Affairs',
            ),
            73 => 
            array (
                'id' => 75,
                'name' => 'Government Administration',
            ),
            74 => 
            array (
                'id' => 76,
                'name' => 'Executive Office',
            ),
            75 => 
            array (
                'id' => 77,
                'name' => 'Law Enforcement',
            ),
            76 => 
            array (
                'id' => 78,
                'name' => 'Public Safety',
            ),
            77 => 
            array (
                'id' => 79,
                'name' => 'Public Policy',
            ),
            78 => 
            array (
                'id' => 80,
                'name' => 'Marketing and Advertising',
            ),
            79 => 
            array (
                'id' => 81,
                'name' => 'Newspapers',
            ),
            80 => 
            array (
                'id' => 82,
                'name' => 'Publishing',
            ),
            81 => 
            array (
                'id' => 83,
                'name' => 'Printing',
            ),
            82 => 
            array (
                'id' => 84,
                'name' => 'Information Services',
            ),
            83 => 
            array (
                'id' => 85,
                'name' => 'Libraries',
            ),
            84 => 
            array (
                'id' => 86,
                'name' => 'Environmental Services',
            ),
            85 => 
            array (
                'id' => 87,
                'name' => 'Package/Freight Delivery',
            ),
            86 => 
            array (
                'id' => 88,
                'name' => 'Individual & Family Services',
            ),
            87 => 
            array (
                'id' => 89,
                'name' => 'Religious Institutions',
            ),
            88 => 
            array (
                'id' => 90,
                'name' => 'Civic & Social Organization',
            ),
            89 => 
            array (
                'id' => 91,
                'name' => 'Consumer Services',
            ),
            90 => 
            array (
                'id' => 92,
                'name' => 'Transportation/Trucking/Railroad',
            ),
            91 => 
            array (
                'id' => 93,
                'name' => 'Warehousing',
            ),
            92 => 
            array (
                'id' => 94,
                'name' => 'Airlines/Aviation',
            ),
            93 => 
            array (
                'id' => 95,
                'name' => 'Maritime',
            ),
            94 => 
            array (
                'id' => 96,
                'name' => 'Information Technology and Services',
            ),
            95 => 
            array (
                'id' => 97,
                'name' => 'Market Research',
            ),
            96 => 
            array (
                'id' => 98,
                'name' => 'Public Relations and Communications',
            ),
            97 => 
            array (
                'id' => 99,
                'name' => 'Design',
            ),
            98 => 
            array (
                'id' => 100,
                'name' => 'Non-Profit Organization Management',
            ),
            99 => 
            array (
                'id' => 101,
                'name' => 'Fund-Raising',
            ),
            100 => 
            array (
                'id' => 102,
                'name' => 'Program Development',
            ),
            101 => 
            array (
                'id' => 103,
                'name' => 'Writing and Editing',
            ),
            102 => 
            array (
                'id' => 104,
                'name' => 'Staffing and Recruiting',
            ),
            103 => 
            array (
                'id' => 105,
                'name' => 'Professional Training & Coaching',
            ),
            104 => 
            array (
                'id' => 106,
                'name' => 'Venture Capital & Private Equity',
            ),
            105 => 
            array (
                'id' => 107,
                'name' => 'Political Organization',
            ),
            106 => 
            array (
                'id' => 108,
                'name' => 'Translation and Localization',
            ),
            107 => 
            array (
                'id' => 109,
                'name' => 'Computer Games',
            ),
            108 => 
            array (
                'id' => 110,
                'name' => 'Events Services',
            ),
            109 => 
            array (
                'id' => 111,
                'name' => 'Arts and Crafts',
            ),
            110 => 
            array (
                'id' => 112,
                'name' => 'Electrical/Electronic Manufacturing',
            ),
            111 => 
            array (
                'id' => 113,
                'name' => 'Online Media',
            ),
            112 => 
            array (
                'id' => 114,
                'name' => 'Nanotechnology',
            ),
            113 => 
            array (
                'id' => 115,
                'name' => 'Music',
            ),
            114 => 
            array (
                'id' => 116,
                'name' => 'Logistics and Supply Chain',
            ),
            115 => 
            array (
                'id' => 117,
                'name' => 'Plastics',
            ),
            116 => 
            array (
                'id' => 118,
                'name' => 'Computer & Network Security',
            ),
            117 => 
            array (
                'id' => 119,
                'name' => 'Wireless',
            ),
            118 => 
            array (
                'id' => 120,
                'name' => 'Alternative Dispute Resolution',
            ),
            119 => 
            array (
                'id' => 121,
                'name' => 'Security and Investigations',
            ),
            120 => 
            array (
                'id' => 122,
                'name' => 'Facilities Services',
            ),
            121 => 
            array (
                'id' => 123,
                'name' => 'Outsourcing/Offshoring',
            ),
            122 => 
            array (
                'id' => 124,
                'name' => 'Health, Wellness and Fitness',
            ),
            123 => 
            array (
                'id' => 125,
                'name' => 'Alternative Medicine',
            ),
            124 => 
            array (
                'id' => 126,
                'name' => 'Media Production',
            ),
            125 => 
            array (
                'id' => 127,
                'name' => 'Animation',
            ),
            126 => 
            array (
                'id' => 128,
                'name' => 'Commercial Real Estate',
            ),
            127 => 
            array (
                'id' => 129,
                'name' => 'Capital Markets',
            ),
            128 => 
            array (
                'id' => 130,
                'name' => 'Think Tanks',
            ),
            129 => 
            array (
                'id' => 131,
                'name' => 'Philanthropy',
            ),
            130 => 
            array (
                'id' => 132,
                'name' => 'E-Learning',
            ),
            131 => 
            array (
                'id' => 133,
                'name' => 'Wholesale',
            ),
            132 => 
            array (
                'id' => 134,
                'name' => 'Import and Export',
            ),
            133 => 
            array (
                'id' => 135,
                'name' => 'Mechanical or Industrial Engineering',
            ),
            134 => 
            array (
                'id' => 136,
                'name' => 'Photography',
            ),
            135 => 
            array (
                'id' => 137,
                'name' => 'Human Resources',
            ),
            136 => 
            array (
                'id' => 138,
                'name' => 'Business Supplies and Equipment',
            ),
            137 => 
            array (
                'id' => 139,
                'name' => 'Mental Health Care',
            ),
            138 => 
            array (
                'id' => 140,
                'name' => 'Graphic Design',
            ),
            139 => 
            array (
                'id' => 141,
                'name' => 'International Trade and Development',
            ),
            140 => 
            array (
                'id' => 142,
                'name' => 'Wine and Spirits',
            ),
            141 => 
            array (
                'id' => 143,
                'name' => 'Luxury Goods & Jewelry',
            ),
            142 => 
            array (
                'id' => 144,
                'name' => 'Renewables & Environment',
            ),
            143 => 
            array (
                'id' => 145,
                'name' => 'Glass, Ceramics & Concrete',
            ),
            144 => 
            array (
                'id' => 146,
                'name' => 'Packaging and Containers',
            ),
            145 => 
            array (
                'id' => 147,
                'name' => 'Industrial Automation',
            ),
            146 => 
            array (
                'id' => 148,
                'name' => 'Government Relations',
            ),
        ));
        
        
    }
}