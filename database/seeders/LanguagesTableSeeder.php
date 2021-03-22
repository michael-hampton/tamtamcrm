<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('languages')->delete();
        
        DB::table('languages')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'English',
                'locale' => 'en',
                'native_name' => 'English',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Abkhaz',
                'locale' => 'ab',
                'native_name' => 'аҧсуа',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Afar',
                'locale' => 'aa',
                'native_name' => 'Afaraf',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Afrikaans',
                'locale' => 'af',
                'native_name' => 'Afrikaans',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Akan',
                'locale' => 'ak',
                'native_name' => 'Akan',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Albanian',
                'locale' => 'sq',
                'native_name' => 'Shqip',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Amharic',
                'locale' => 'am',
                'native_name' => 'አማርኛ',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Arabic',
                'locale' => 'ar',
                'native_name' => 'العربية',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Aragonese',
                'locale' => 'an',
                'native_name' => 'Aragonés',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Armenian',
                'locale' => 'hy',
                'native_name' => 'Հայերեն',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Assamese',
                'locale' => 'as',
                'native_name' => 'অসমীয়া',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Avaric',
                'locale' => 'av',
                'native_name' => 'авар мацӀ, магӀарул мацӀ',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'Avestan',
                'locale' => 'ae',
                'native_name' => 'avesta',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'Aymara',
                'locale' => 'ay',
                'native_name' => 'aymar aru',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'Azerbaijani',
                'locale' => 'az',
                'native_name' => 'azərbaycan dili',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'Bambara',
                'locale' => 'bm',
                'native_name' => 'bamanankan',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'Bashkir',
                'locale' => 'ba',
                'native_name' => 'башҡорт теле',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'Basque',
                'locale' => 'eu',
                'native_name' => 'euskara, euskera',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'Belarusian',
                'locale' => 'be',
                'native_name' => 'Беларуская',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'Bengali',
                'locale' => 'bn',
                'native_name' => 'বাংলা',
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'Bihari',
                'locale' => 'bh',
                'native_name' => 'भोजपुरी',
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'Bislama',
                'locale' => 'bi',
                'native_name' => 'Bislama',
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'Bosnian',
                'locale' => 'bs',
                'native_name' => 'bosanski jezik',
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'Breton',
                'locale' => 'br',
                'native_name' => 'brezhoneg',
            ),
            24 => 
            array (
                'id' => 25,
                'name' => 'Bulgarian',
                'locale' => 'bg',
                'native_name' => 'български език',
            ),
            25 => 
            array (
                'id' => 26,
                'name' => 'Burmese',
                'locale' => 'my',
                'native_name' => 'ဗမာစာ',
            ),
            26 => 
            array (
                'id' => 27,
                'name' => 'Catalan; Valencian',
                'locale' => 'ca',
                'native_name' => 'Català',
            ),
            27 => 
            array (
                'id' => 28,
                'name' => 'Chamorro',
                'locale' => 'ch',
                'native_name' => 'Chamoru',
            ),
            28 => 
            array (
                'id' => 29,
                'name' => 'Chechen',
                'locale' => 'ce',
                'native_name' => 'нохчийн мотт',
            ),
            29 => 
            array (
                'id' => 30,
                'name' => 'Chichewa; Chewa; Nyanja',
                'locale' => 'ny',
                'native_name' => 'chiCheŵa, chinyanja',
            ),
            30 => 
            array (
                'id' => 31,
                'name' => 'Chinese',
                'locale' => 'zh',
            'native_name' => '中文 (Zhōngwén), 汉语, 漢語',
            ),
            31 => 
            array (
                'id' => 32,
                'name' => 'Chuvash',
                'locale' => 'cv',
                'native_name' => 'чӑваш чӗлхи',
            ),
            32 => 
            array (
                'id' => 33,
                'name' => 'Cornish',
                'locale' => 'kw',
                'native_name' => 'Kernewek',
            ),
            33 => 
            array (
                'id' => 34,
                'name' => 'Corsican',
                'locale' => 'co',
                'native_name' => 'corsu, lingua corsa',
            ),
            34 => 
            array (
                'id' => 35,
                'name' => 'Cree',
                'locale' => 'cr',
                'native_name' => 'ᓀᐦᐃᔭᐍᐏᐣ',
            ),
            35 => 
            array (
                'id' => 36,
                'name' => 'Croatian',
                'locale' => 'hr',
                'native_name' => 'hrvatski',
            ),
            36 => 
            array (
                'id' => 37,
                'name' => 'Czech',
                'locale' => 'cs',
                'native_name' => 'česky, čeština',
            ),
            37 => 
            array (
                'id' => 38,
                'name' => 'Danish',
                'locale' => 'da',
                'native_name' => 'dansk',
            ),
            38 => 
            array (
                'id' => 39,
                'name' => 'Divehi; Dhivehi; Maldivian;',
                'locale' => 'dv',
                'native_name' => 'ދިވެހި',
            ),
            39 => 
            array (
                'id' => 40,
                'name' => 'Dutch',
                'locale' => 'nl',
                'native_name' => 'Nederlands, Vlaams',
            ),
            40 => 
            array (
                'id' => 41,
                'name' => 'Esperanto',
                'locale' => 'eo',
                'native_name' => 'Esperanto',
            ),
            41 => 
            array (
                'id' => 42,
                'name' => 'Estonian',
                'locale' => 'et',
                'native_name' => 'eesti, eesti keel',
            ),
            42 => 
            array (
                'id' => 43,
                'name' => 'Ewe',
                'locale' => 'ee',
                'native_name' => 'Eʋegbe',
            ),
            43 => 
            array (
                'id' => 44,
                'name' => 'Faroese',
                'locale' => 'fo',
                'native_name' => 'føroyskt',
            ),
            44 => 
            array (
                'id' => 45,
                'name' => 'Fijian',
                'locale' => 'fj',
                'native_name' => 'vosa Vakaviti',
            ),
            45 => 
            array (
                'id' => 46,
                'name' => 'Finnish',
                'locale' => 'fi',
                'native_name' => 'suomi, suomen kieli',
            ),
            46 => 
            array (
                'id' => 47,
                'name' => 'French',
                'locale' => 'fr',
                'native_name' => 'français, langue française',
            ),
            47 => 
            array (
                'id' => 48,
                'name' => 'Fula; Fulah; Pulaar; Pular',
                'locale' => 'ff',
                'native_name' => 'Fulfulde, Pulaar, Pular',
            ),
            48 => 
            array (
                'id' => 49,
                'name' => 'Galician',
                'locale' => 'gl',
                'native_name' => 'Galego',
            ),
            49 => 
            array (
                'id' => 50,
                'name' => 'Georgian',
                'locale' => 'ka',
                'native_name' => 'ქართული',
            ),
            50 => 
            array (
                'id' => 51,
                'name' => 'German',
                'locale' => 'de',
                'native_name' => 'Deutsch',
            ),
            51 => 
            array (
                'id' => 52,
                'name' => 'Greek, Modern',
                'locale' => 'el',
                'native_name' => 'Ελληνικά',
            ),
            52 => 
            array (
                'id' => 53,
                'name' => 'Guaraní',
                'locale' => 'gn',
                'native_name' => 'Avañeẽ',
            ),
            53 => 
            array (
                'id' => 54,
                'name' => 'Gujarati',
                'locale' => 'gu',
                'native_name' => 'ગુજરાતી',
            ),
            54 => 
            array (
                'id' => 55,
                'name' => 'Haitian; Haitian Creole',
                'locale' => 'ht',
                'native_name' => 'Kreyòl ayisyen',
            ),
            55 => 
            array (
                'id' => 56,
                'name' => 'Hausa',
                'locale' => 'ha',
                'native_name' => 'Hausa, هَوُسَ',
            ),
            56 => 
            array (
                'id' => 57,
            'name' => 'Hebrew (modern)',
                'locale' => 'he',
                'native_name' => 'עברית',
            ),
            57 => 
            array (
                'id' => 58,
                'name' => 'Herero',
                'locale' => 'hz',
                'native_name' => 'Otjiherero',
            ),
            58 => 
            array (
                'id' => 59,
                'name' => 'Hindi',
                'locale' => 'hi',
                'native_name' => 'हिन्दी, हिंदी',
            ),
            59 => 
            array (
                'id' => 60,
                'name' => 'Hiri Motu',
                'locale' => 'ho',
                'native_name' => 'Hiri Motu',
            ),
            60 => 
            array (
                'id' => 61,
                'name' => 'Hungarian',
                'locale' => 'hu',
                'native_name' => 'Magyar',
            ),
            61 => 
            array (
                'id' => 62,
                'name' => 'Interlingua',
                'locale' => 'ia',
                'native_name' => 'Interlingua',
            ),
            62 => 
            array (
                'id' => 63,
                'name' => 'Indonesian',
                'locale' => 'id',
                'native_name' => 'Bahasa Indonesia',
            ),
            63 => 
            array (
                'id' => 64,
                'name' => 'Interlingue',
                'locale' => 'ie',
                'native_name' => 'Originally called Occidental; then Interlingue after WWII',
            ),
            64 => 
            array (
                'id' => 65,
                'name' => 'Irish',
                'locale' => 'ga',
                'native_name' => 'Gaeilge',
            ),
            65 => 
            array (
                'id' => 66,
                'name' => 'Igbo',
                'locale' => 'ig',
                'native_name' => 'Asụsụ Igbo',
            ),
            66 => 
            array (
                'id' => 67,
                'name' => 'Inupiaq',
                'locale' => 'ik',
                'native_name' => 'Iñupiaq, Iñupiatun',
            ),
            67 => 
            array (
                'id' => 68,
                'name' => 'Ido',
                'locale' => 'io',
                'native_name' => 'Ido',
            ),
            68 => 
            array (
                'id' => 69,
                'name' => 'Icelandic',
                'locale' => 'is',
                'native_name' => 'Íslenska',
            ),
            69 => 
            array (
                'id' => 70,
                'name' => 'Italian',
                'locale' => 'it',
                'native_name' => 'Italiano',
            ),
            70 => 
            array (
                'id' => 71,
                'name' => 'Inuktitut',
                'locale' => 'iu',
                'native_name' => 'ᐃᓄᒃᑎᑐᑦ',
            ),
            71 => 
            array (
                'id' => 72,
                'name' => 'Japanese',
                'locale' => 'ja',
            'native_name' => '日本語 (にほんご／にっぽんご)',
            ),
            72 => 
            array (
                'id' => 73,
                'name' => 'Javanese',
                'locale' => 'jv',
                'native_name' => 'basa Jawa',
            ),
            73 => 
            array (
                'id' => 74,
                'name' => 'Kalaallisut, Greenlandic',
                'locale' => 'kl',
                'native_name' => 'kalaallisut, kalaallit oqaasii',
            ),
            74 => 
            array (
                'id' => 75,
                'name' => 'Kannada',
                'locale' => 'kn',
                'native_name' => 'ಕನ್ನಡ',
            ),
            75 => 
            array (
                'id' => 76,
                'name' => 'Kanuri',
                'locale' => 'kr',
                'native_name' => 'Kanuri',
            ),
            76 => 
            array (
                'id' => 77,
                'name' => 'Kashmiri',
                'locale' => 'ks',
                'native_name' => 'कश्मीरी, كشميري‎',
            ),
            77 => 
            array (
                'id' => 78,
                'name' => 'Kazakh',
                'locale' => 'kk',
                'native_name' => 'Қазақ тілі',
            ),
            78 => 
            array (
                'id' => 79,
                'name' => 'Khmer',
                'locale' => 'km',
                'native_name' => 'ភាសាខ្មែរ',
            ),
            79 => 
            array (
                'id' => 80,
                'name' => 'Kikuyu, Gikuyu',
                'locale' => 'ki',
                'native_name' => 'Gĩkũyũ',
            ),
            80 => 
            array (
                'id' => 81,
                'name' => 'Kinyarwanda',
                'locale' => 'rw',
                'native_name' => 'Ikinyarwanda',
            ),
            81 => 
            array (
                'id' => 82,
                'name' => 'Kirghiz, Kyrgyz',
                'locale' => 'ky',
                'native_name' => 'кыргыз тили',
            ),
            82 => 
            array (
                'id' => 83,
                'name' => 'Komi',
                'locale' => 'kv',
                'native_name' => 'коми кыв',
            ),
            83 => 
            array (
                'id' => 84,
                'name' => 'Kongo',
                'locale' => 'kg',
                'native_name' => 'KiKongo',
            ),
            84 => 
            array (
                'id' => 85,
                'name' => 'Korean',
                'locale' => 'ko',
            'native_name' => '한국어 (韓國語), 조선말 (朝鮮語)',
            ),
            85 => 
            array (
                'id' => 86,
                'name' => 'Kurdish',
                'locale' => 'ku',
                'native_name' => 'Kurdî, كوردی‎',
            ),
            86 => 
            array (
                'id' => 87,
                'name' => 'Kwanyama, Kuanyama',
                'locale' => 'kj',
                'native_name' => 'Kuanyama',
            ),
            87 => 
            array (
                'id' => 88,
                'name' => 'Latin',
                'locale' => 'la',
                'native_name' => 'latine, lingua latina',
            ),
            88 => 
            array (
                'id' => 89,
                'name' => 'Luxembourgish, Letzeburgesch',
                'locale' => 'lb',
                'native_name' => 'Lëtzebuergesch',
            ),
            89 => 
            array (
                'id' => 90,
                'name' => 'Luganda',
                'locale' => 'lg',
                'native_name' => 'Luganda',
            ),
            90 => 
            array (
                'id' => 91,
                'name' => 'Limburgish, Limburgan, Limburger',
                'locale' => 'li',
                'native_name' => 'Limburgs',
            ),
            91 => 
            array (
                'id' => 92,
                'name' => 'Lingala',
                'locale' => 'ln',
                'native_name' => 'Lingála',
            ),
            92 => 
            array (
                'id' => 93,
                'name' => 'Lao',
                'locale' => 'lo',
                'native_name' => 'ພາສາລາວ',
            ),
            93 => 
            array (
                'id' => 94,
                'name' => 'Lithuanian',
                'locale' => 'lt',
                'native_name' => 'lietuvių kalba',
            ),
            94 => 
            array (
                'id' => 95,
                'name' => 'Luba-Katanga',
                'locale' => 'lu',
                'native_name' => '',
            ),
            95 => 
            array (
                'id' => 96,
                'name' => 'Latvian',
                'locale' => 'lv',
                'native_name' => 'latviešu valoda',
            ),
            96 => 
            array (
                'id' => 97,
                'name' => 'Manx',
                'locale' => 'gv',
                'native_name' => 'Gaelg, Gailck',
            ),
            97 => 
            array (
                'id' => 98,
                'name' => 'Macedonian',
                'locale' => 'mk',
                'native_name' => 'македонски јазик',
            ),
            98 => 
            array (
                'id' => 99,
                'name' => 'Malagasy',
                'locale' => 'mg',
                'native_name' => 'Malagasy fiteny',
            ),
            99 => 
            array (
                'id' => 100,
                'name' => 'Malay',
                'locale' => 'ms',
                'native_name' => 'bahasa Melayu, بهاس ملايو‎',
            ),
            100 => 
            array (
                'id' => 101,
                'name' => 'Malayalam',
                'locale' => 'ml',
                'native_name' => 'മലയാളം',
            ),
            101 => 
            array (
                'id' => 102,
                'name' => 'Maltese',
                'locale' => 'mt',
                'native_name' => 'Malti',
            ),
            102 => 
            array (
                'id' => 103,
                'name' => 'Māori',
                'locale' => 'mi',
                'native_name' => 'te reo Māori',
            ),
            103 => 
            array (
                'id' => 104,
            'name' => 'Marathi (Marāṭhī)',
                'locale' => 'mr',
                'native_name' => 'मराठी',
            ),
            104 => 
            array (
                'id' => 105,
                'name' => 'Marshallese',
                'locale' => 'mh',
                'native_name' => 'Kajin M̧ajeļ',
            ),
            105 => 
            array (
                'id' => 106,
                'name' => 'Mongolian',
                'locale' => 'mn',
                'native_name' => 'монгол',
            ),
            106 => 
            array (
                'id' => 107,
                'name' => 'Nauru',
                'locale' => 'na',
                'native_name' => 'Ekakairũ Naoero',
            ),
            107 => 
            array (
                'id' => 108,
                'name' => 'Navajo, Navaho',
                'locale' => 'nv',
                'native_name' => 'Diné bizaad, Dinékʼehǰí',
            ),
            108 => 
            array (
                'id' => 109,
                'name' => 'Norwegian Bokmål',
                'locale' => 'nb',
                'native_name' => 'Norsk bokmål',
            ),
            109 => 
            array (
                'id' => 110,
                'name' => 'North Ndebele',
                'locale' => 'nd',
                'native_name' => 'isiNdebele',
            ),
            110 => 
            array (
                'id' => 111,
                'name' => 'Nepali',
                'locale' => 'ne',
                'native_name' => 'नेपाली',
            ),
            111 => 
            array (
                'id' => 112,
                'name' => 'Ndonga',
                'locale' => 'ng',
                'native_name' => 'Owambo',
            ),
            112 => 
            array (
                'id' => 113,
                'name' => 'Norwegian Nynorsk',
                'locale' => 'nn',
                'native_name' => 'Norsk nynorsk',
            ),
            113 => 
            array (
                'id' => 114,
                'name' => 'Norwegian',
                'locale' => 'no',
                'native_name' => 'Norsk',
            ),
            114 => 
            array (
                'id' => 115,
                'name' => 'Nuosu',
                'locale' => 'ii',
                'native_name' => 'ꆈꌠ꒿ Nuosuhxop',
            ),
            115 => 
            array (
                'id' => 116,
                'name' => 'South Ndebele',
                'locale' => 'nr',
                'native_name' => 'isiNdebele',
            ),
            116 => 
            array (
                'id' => 117,
                'name' => 'Occitan',
                'locale' => 'oc',
                'native_name' => 'Occitan',
            ),
            117 => 
            array (
                'id' => 118,
                'name' => 'Ojibwe, Ojibwa',
                'locale' => 'oj',
                'native_name' => 'ᐊᓂᔑᓈᐯᒧᐎᓐ',
            ),
            118 => 
            array (
                'id' => 119,
                'name' => 'Old Church Slavonic, Church Slavic, Church Slavonic, Old Bulgarian, Old Slavonic',
                'locale' => 'cu',
                'native_name' => 'ѩзыкъ словѣньскъ',
            ),
            119 => 
            array (
                'id' => 120,
                'name' => 'Oromo',
                'locale' => 'om',
                'native_name' => 'Afaan Oromoo',
            ),
            120 => 
            array (
                'id' => 121,
                'name' => 'Oriya',
                'locale' => 'or',
                'native_name' => 'ଓଡ଼ିଆ',
            ),
            121 => 
            array (
                'id' => 122,
                'name' => 'Ossetian, Ossetic',
                'locale' => 'os',
                'native_name' => 'ирон æвзаг',
            ),
            122 => 
            array (
                'id' => 123,
                'name' => 'Panjabi, Punjabi',
                'locale' => 'pa',
                'native_name' => 'ਪੰਜਾਬੀ, پنجابی‎',
            ),
            123 => 
            array (
                'id' => 124,
                'name' => 'Pāli',
                'locale' => 'pi',
                'native_name' => 'पाऴि',
            ),
            124 => 
            array (
                'id' => 125,
                'name' => 'Persian',
                'locale' => 'fa',
                'native_name' => 'فارسی',
            ),
            125 => 
            array (
                'id' => 126,
                'name' => 'Polish',
                'locale' => 'pl',
                'native_name' => 'polski',
            ),
            126 => 
            array (
                'id' => 127,
                'name' => 'Pashto, Pushto',
                'locale' => 'ps',
                'native_name' => 'پښتو',
            ),
            127 => 
            array (
                'id' => 128,
                'name' => 'Portuguese',
                'locale' => 'pt',
                'native_name' => 'Português',
            ),
            128 => 
            array (
                'id' => 129,
                'name' => 'Quechua',
                'locale' => 'qu',
                'native_name' => 'Runa Simi, Kichwa',
            ),
            129 => 
            array (
                'id' => 130,
                'name' => 'Romansh',
                'locale' => 'rm',
                'native_name' => 'rumantsch grischun',
            ),
            130 => 
            array (
                'id' => 131,
                'name' => 'Kirundi',
                'locale' => 'rn',
                'native_name' => 'kiRundi',
            ),
            131 => 
            array (
                'id' => 132,
                'name' => 'Romanian, Moldavian, Moldovan',
                'locale' => 'ro',
                'native_name' => 'română',
            ),
            132 => 
            array (
                'id' => 133,
                'name' => 'Russian',
                'locale' => 'ru',
                'native_name' => 'русский язык',
            ),
            133 => 
            array (
                'id' => 134,
            'name' => 'Sanskrit (Saṁskṛta)',
                'locale' => 'sa',
                'native_name' => 'संस्कृतम्',
            ),
            134 => 
            array (
                'id' => 135,
                'name' => 'Sardinian',
                'locale' => 'sc',
                'native_name' => 'sardu',
            ),
            135 => 
            array (
                'id' => 136,
                'name' => 'Sindhi',
                'locale' => 'sd',
                'native_name' => 'सिन्धी, سنڌي، سندھی‎',
            ),
            136 => 
            array (
                'id' => 137,
                'name' => 'Northern Sami',
                'locale' => 'se',
                'native_name' => 'Davvisámegiella',
            ),
            137 => 
            array (
                'id' => 138,
                'name' => 'Samoan',
                'locale' => 'sm',
                'native_name' => 'gagana faa Samoa',
            ),
            138 => 
            array (
                'id' => 139,
                'name' => 'Sango',
                'locale' => 'sg',
                'native_name' => 'yângâ tî sängö',
            ),
            139 => 
            array (
                'id' => 140,
                'name' => 'Serbian',
                'locale' => 'sr',
                'native_name' => 'српски језик',
            ),
            140 => 
            array (
                'id' => 141,
                'name' => 'Scottish Gaelic; Gaelic',
                'locale' => 'gd',
                'native_name' => 'Gàidhlig',
            ),
            141 => 
            array (
                'id' => 142,
                'name' => 'Shona',
                'locale' => 'sn',
                'native_name' => 'chiShona',
            ),
            142 => 
            array (
                'id' => 143,
                'name' => 'Sinhala, Sinhalese',
                'locale' => 'si',
                'native_name' => 'සිංහල',
            ),
            143 => 
            array (
                'id' => 144,
                'name' => 'Slovak',
                'locale' => 'sk',
                'native_name' => 'slovenčina',
            ),
            144 => 
            array (
                'id' => 145,
                'name' => 'Slovene',
                'locale' => 'sl',
                'native_name' => 'slovenščina',
            ),
            145 => 
            array (
                'id' => 146,
                'name' => 'Somali',
                'locale' => 'so',
                'native_name' => 'Soomaaliga, af Soomaali',
            ),
            146 => 
            array (
                'id' => 147,
                'name' => 'Southern Sotho',
                'locale' => 'st',
                'native_name' => 'Sesotho',
            ),
            147 => 
            array (
                'id' => 148,
                'name' => 'Spanish; Castilian',
                'locale' => 'es',
                'native_name' => 'español, castellano',
            ),
            148 => 
            array (
                'id' => 149,
                'name' => 'Sundanese',
                'locale' => 'su',
                'native_name' => 'Basa Sunda',
            ),
            149 => 
            array (
                'id' => 150,
                'name' => 'Swahili',
                'locale' => 'sw',
                'native_name' => 'Kiswahili',
            ),
            150 => 
            array (
                'id' => 151,
                'name' => 'Swati',
                'locale' => 'ss',
                'native_name' => 'SiSwati',
            ),
            151 => 
            array (
                'id' => 152,
                'name' => 'Swedish',
                'locale' => 'sv',
                'native_name' => 'svenska',
            ),
            152 => 
            array (
                'id' => 153,
                'name' => 'Tamil',
                'locale' => 'ta',
                'native_name' => 'தமிழ்',
            ),
            153 => 
            array (
                'id' => 154,
                'name' => 'Telugu',
                'locale' => 'te',
                'native_name' => 'తెలుగు',
            ),
            154 => 
            array (
                'id' => 155,
                'name' => 'Tajik',
                'locale' => 'tg',
                'native_name' => 'тоҷикӣ, toğikī, تاجیکی‎',
            ),
            155 => 
            array (
                'id' => 156,
                'name' => 'Thai',
                'locale' => 'th',
                'native_name' => 'ไทย',
            ),
            156 => 
            array (
                'id' => 157,
                'name' => 'Tigrinya',
                'locale' => 'ti',
                'native_name' => 'ትግርኛ',
            ),
            157 => 
            array (
                'id' => 158,
                'name' => 'Tibetan Standard, Tibetan, Central',
                'locale' => 'bo',
                'native_name' => 'བོད་ཡིག',
            ),
            158 => 
            array (
                'id' => 159,
                'name' => 'Turkmen',
                'locale' => 'tk',
                'native_name' => 'Türkmen, Түркмен',
            ),
            159 => 
            array (
                'id' => 160,
                'name' => 'Tagalog',
                'locale' => 'tl',
                'native_name' => 'Wikang Tagalog, ᜏᜒᜃᜅ᜔ ᜆᜄᜎᜓᜄ᜔',
            ),
            160 => 
            array (
                'id' => 161,
                'name' => 'Tswana',
                'locale' => 'tn',
                'native_name' => 'Setswana',
            ),
            161 => 
            array (
                'id' => 162,
            'name' => 'Tonga (Tonga Islands)',
                'locale' => 'to',
                'native_name' => 'faka Tonga',
            ),
            162 => 
            array (
                'id' => 163,
                'name' => 'Turkish',
                'locale' => 'tr',
                'native_name' => 'Türkçe',
            ),
            163 => 
            array (
                'id' => 164,
                'name' => 'Tsonga',
                'locale' => 'ts',
                'native_name' => 'Xitsonga',
            ),
            164 => 
            array (
                'id' => 165,
                'name' => 'Tatar',
                'locale' => 'tt',
                'native_name' => 'татарча, tatarça, تاتارچا‎',
            ),
            165 => 
            array (
                'id' => 166,
                'name' => 'Twi',
                'locale' => 'tw',
                'native_name' => 'Twi',
            ),
            166 => 
            array (
                'id' => 167,
                'name' => 'Tahitian',
                'locale' => 'ty',
                'native_name' => 'Reo Tahiti',
            ),
            167 => 
            array (
                'id' => 168,
                'name' => 'Uighur, Uyghur',
                'locale' => 'ug',
                'native_name' => 'Uyƣurqə, ئۇيغۇرچە‎',
            ),
            168 => 
            array (
                'id' => 169,
                'name' => 'Ukrainian',
                'locale' => 'uk',
                'native_name' => 'українська',
            ),
            169 => 
            array (
                'id' => 170,
                'name' => 'Urdu',
                'locale' => 'ur',
                'native_name' => 'اردو',
            ),
            170 => 
            array (
                'id' => 171,
                'name' => 'Uzbek',
                'locale' => 'uz',
                'native_name' => 'zbek, Ўзбек, أۇزبېك‎',
            ),
            171 => 
            array (
                'id' => 172,
                'name' => 'Venda',
                'locale' => 've',
                'native_name' => 'Tshivenḓa',
            ),
            172 => 
            array (
                'id' => 173,
                'name' => 'Vietnamese',
                'locale' => 'vi',
                'native_name' => 'Tiếng Việt',
            ),
            173 => 
            array (
                'id' => 174,
                'name' => 'Volapük',
                'locale' => 'vo',
                'native_name' => 'Volapük',
            ),
            174 => 
            array (
                'id' => 175,
                'name' => 'Walloon',
                'locale' => 'wa',
                'native_name' => 'Walon',
            ),
            175 => 
            array (
                'id' => 176,
                'name' => 'Welsh',
                'locale' => 'cy',
                'native_name' => 'Cymraeg',
            ),
            176 => 
            array (
                'id' => 177,
                'name' => 'Wolof',
                'locale' => 'wo',
                'native_name' => 'Wollof',
            ),
            177 => 
            array (
                'id' => 178,
                'name' => 'Western Frisian',
                'locale' => 'fy',
                'native_name' => 'Frysk',
            ),
            178 => 
            array (
                'id' => 179,
                'name' => 'Xhosa',
                'locale' => 'xh',
                'native_name' => 'isiXhosa',
            ),
            179 => 
            array (
                'id' => 180,
                'name' => 'Yiddish',
                'locale' => 'yi',
                'native_name' => 'ייִדיש',
            ),
            180 => 
            array (
                'id' => 181,
                'name' => 'Yoruba',
                'locale' => 'yo',
                'native_name' => 'Yorùbá',
            ),
            181 => 
            array (
                'id' => 182,
                'name' => 'Zhuang, Chuang',
                'locale' => 'za',
                'native_name' => 'Saɯ cueŋƅ, Saw cuengh',
            ),
        ));
        
        
    }
}