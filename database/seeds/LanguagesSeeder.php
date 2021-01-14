<?php

use Illuminate\Database\Seeder;

class LanguagesSeeder extends Seeder
{

    public function run()
    {
        $arrLanguages = '[{
    "locale": "en",
    "name": "English",
    "native_name": "English"
}, {
    "locale": "ab",
    "name": "Abkhaz",
    "native_name": "аҧсуа"
}, {
    "locale": "aa",
    "name": "Afar",
    "native_name": "Afaraf"
}, {
    "locale": "af",
    "name": "Afrikaans",
    "native_name": "Afrikaans"
}, {
    "locale": "ak",
    "name": "Akan",
    "native_name": "Akan"
}, {
    "locale": "sq",
    "name": "Albanian",
    "native_name": "Shqip"
}, {
    "locale": "am",
    "name": "Amharic",
    "native_name": "አማርኛ"
}, {
    "locale": "ar",
    "name": "Arabic",
    "native_name": "العربية"
}, {
    "locale": "an",
    "name": "Aragonese",
    "native_name": "Aragonés"
}, {
    "locale": "hy",
    "name": "Armenian",
    "native_name": "Հայերեն"
}, {
    "locale": "as",
    "name": "Assamese",
    "native_name": "অসমীয়া"
}, {
    "locale": "av",
    "name": "Avaric",
    "native_name": "авар мацӀ, магӀарул мацӀ"
}, {
    "locale": "ae",
    "name": "Avestan",
    "native_name": "avesta"
}, {
    "locale": "ay",
    "name": "Aymara",
    "native_name": "aymar aru"
}, {
    "locale": "az",
    "name": "Azerbaijani",
    "native_name": "azərbaycan dili"
}, {
    "locale": "bm",
    "name": "Bambara",
    "native_name": "bamanankan"
}, {
    "locale": "ba",
    "name": "Bashkir",
    "native_name": "башҡорт теле"
}, {
    "locale": "eu",
    "name": "Basque",
    "native_name": "euskara, euskera"
}, {
    "locale": "be",
    "name": "Belarusian",
    "native_name": "Беларуская"
}, {
    "locale": "bn",
    "name": "Bengali",
    "native_name": "বাংলা"
}, {
    "locale": "bh",
    "name": "Bihari",
    "native_name": "भोजपुरी"
}, {
    "locale": "bi",
    "name": "Bislama",
    "native_name": "Bislama"
}, {
    "locale": "bs",
    "name": "Bosnian",
    "native_name": "bosanski jezik"
}, {
    "locale": "br",
    "name": "Breton",
    "native_name": "brezhoneg"
}, {
    "locale": "bg",
    "name": "Bulgarian",
    "native_name": "български език"
}, {
    "locale": "my",
    "name": "Burmese",
    "native_name": "ဗမာစာ"
}, {
    "locale": "ca",
    "name": "Catalan; Valencian",
    "native_name": "Català"
}, {
    "locale": "ch",
    "name": "Chamorro",
    "native_name": "Chamoru"
}, {
    "locale": "ce",
    "name": "Chechen",
    "native_name": "нохчийн мотт"
}, {
    "locale": "ny",
    "name": "Chichewa; Chewa; Nyanja",
    "native_name": "chiCheŵa, chinyanja"
}, {
    "locale": "zh",
    "name": "Chinese",
    "native_name": "中文 (Zhōngwén), 汉语, 漢語"
}, {
    "locale": "cv",
    "name": "Chuvash",
    "native_name": "чӑваш чӗлхи"
}, {
    "locale": "kw",
    "name": "Cornish",
    "native_name": "Kernewek"
}, {
    "locale": "co",
    "name": "Corsican",
    "native_name": "corsu, lingua corsa"
}, {
    "locale": "cr",
    "name": "Cree",
    "native_name": "ᓀᐦᐃᔭᐍᐏᐣ"
}, {
    "locale": "hr",
    "name": "Croatian",
    "native_name": "hrvatski"
}, {
    "locale": "cs",
    "name": "Czech",
    "native_name": "česky, čeština"
}, {
    "locale": "da",
    "name": "Danish",
    "native_name": "dansk"
}, {
    "locale": "dv",
    "name": "Divehi; Dhivehi; Maldivian;",
    "native_name": "ދިވެހި"
}, {
    "locale": "nl",
    "name": "Dutch",
    "native_name": "Nederlands, Vlaams"
}, {
    "locale": "eo",
    "name": "Esperanto",
    "native_name": "Esperanto"
}, {
    "locale": "et",
    "name": "Estonian",
    "native_name": "eesti, eesti keel"
}, {
    "locale": "ee",
    "name": "Ewe",
    "native_name": "Eʋegbe"
}, {
    "locale": "fo",
    "name": "Faroese",
    "native_name": "føroyskt"
}, {
    "locale": "fj",
    "name": "Fijian",
    "native_name": "vosa Vakaviti"
}, {
    "locale": "fi",
    "name": "Finnish",
    "native_name": "suomi, suomen kieli"
}, {
    "locale": "fr",
    "name": "French",
    "native_name": "français, langue française"
}, {
    "locale": "ff",
    "name": "Fula; Fulah; Pulaar; Pular",
    "native_name": "Fulfulde, Pulaar, Pular"
}, {
    "locale": "gl",
    "name": "Galician",
    "native_name": "Galego"
}, {
    "locale": "ka",
    "name": "Georgian",
    "native_name": "ქართული"
}, {
    "locale": "de",
    "name": "German",
    "native_name": "Deutsch"
}, {
    "locale": "el",
    "name": "Greek, Modern",
    "native_name": "Ελληνικά"
}, {
    "locale": "gn",
    "name": "Guaraní",
    "native_name": "Avañeẽ"
}, {
    "locale": "gu",
    "name": "Gujarati",
    "native_name": "ગુજરાતી"
}, {
    "locale": "ht",
    "name": "Haitian; Haitian Creole",
    "native_name": "Kreyòl ayisyen"
}, {
    "locale": "ha",
    "name": "Hausa",
    "native_name": "Hausa, هَوُسَ"
}, {
    "locale": "he",
    "name": "Hebrew (modern)",
    "native_name": "עברית"
}, {
    "locale": "hz",
    "name": "Herero",
    "native_name": "Otjiherero"
}, {
    "locale": "hi",
    "name": "Hindi",
    "native_name": "हिन्दी, हिंदी"
}, {
    "locale": "ho",
    "name": "Hiri Motu",
    "native_name": "Hiri Motu"
}, {
    "locale": "hu",
    "name": "Hungarian",
    "native_name": "Magyar"
}, {
    "locale": "ia",
    "name": "Interlingua",
    "native_name": "Interlingua"
}, {
    "locale": "id",
    "name": "Indonesian",
    "native_name": "Bahasa Indonesia"
}, {
    "locale": "ie",
    "name": "Interlingue",
    "native_name": "Originally called Occidental; then Interlingue after WWII"
}, {
    "locale": "ga",
    "name": "Irish",
    "native_name": "Gaeilge"
}, {
    "locale": "ig",
    "name": "Igbo",
    "native_name": "Asụsụ Igbo"
}, {
    "locale": "ik",
    "name": "Inupiaq",
    "native_name": "Iñupiaq, Iñupiatun"
}, {
    "locale": "io",
    "name": "Ido",
    "native_name": "Ido"
}, {
    "locale": "is",
    "name": "Icelandic",
    "native_name": "Íslenska"
}, {
    "locale": "it",
    "name": "Italian",
    "native_name": "Italiano"
}, {
    "locale": "iu",
    "name": "Inuktitut",
    "native_name": "ᐃᓄᒃᑎᑐᑦ"
}, {
    "locale": "ja",
    "name": "Japanese",
    "native_name": "日本語 (にほんご／にっぽんご)"
}, {
    "locale": "jv",
    "name": "Javanese",
    "native_name": "basa Jawa"
}, {
    "locale": "kl",
    "name": "Kalaallisut, Greenlandic",
    "native_name": "kalaallisut, kalaallit oqaasii"
}, {
    "locale": "kn",
    "name": "Kannada",
    "native_name": "ಕನ್ನಡ"
}, {
    "locale": "kr",
    "name": "Kanuri",
    "native_name": "Kanuri"
}, {
    "locale": "ks",
    "name": "Kashmiri",
    "native_name": "कश्मीरी, كشميري‎"
}, {
    "locale": "kk",
    "name": "Kazakh",
    "native_name": "Қазақ тілі"
}, {
    "locale": "km",
    "name": "Khmer",
    "native_name": "ភាសាខ្មែរ"
}, {
    "locale": "ki",
    "name": "Kikuyu, Gikuyu",
    "native_name": "Gĩkũyũ"
}, {
    "locale": "rw",
    "name": "Kinyarwanda",
    "native_name": "Ikinyarwanda"
}, {
    "locale": "ky",
    "name": "Kirghiz, Kyrgyz",
    "native_name": "кыргыз тили"
}, {
    "locale": "kv",
    "name": "Komi",
    "native_name": "коми кыв"
}, {
    "locale": "kg",
    "name": "Kongo",
    "native_name": "KiKongo"
}, {
    "locale": "ko",
    "name": "Korean",
    "native_name": "한국어 (韓國語), 조선말 (朝鮮語)"
}, {
    "locale": "ku",
    "name": "Kurdish",
    "native_name": "Kurdî, كوردی‎"
}, {
    "locale": "kj",
    "name": "Kwanyama, Kuanyama",
    "native_name": "Kuanyama"
}, {
    "locale": "la",
    "name": "Latin",
    "native_name": "latine, lingua latina"
}, {
    "locale": "lb",
    "name": "Luxembourgish, Letzeburgesch",
    "native_name": "Lëtzebuergesch"
}, {
    "locale": "lg",
    "name": "Luganda",
    "native_name": "Luganda"
}, {
    "locale": "li",
    "name": "Limburgish, Limburgan, Limburger",
    "native_name": "Limburgs"
}, {
    "locale": "ln",
    "name": "Lingala",
    "native_name": "Lingála"
}, {
    "locale": "lo",
    "name": "Lao",
    "native_name": "ພາສາລາວ"
}, {
    "locale": "lt",
    "name": "Lithuanian",
    "native_name": "lietuvių kalba"
}, {
    "locale": "lu",
    "name": "Luba-Katanga",
    "native_name": ""
}, {
    "locale": "lv",
    "name": "Latvian",
    "native_name": "latviešu valoda"
}, {
    "locale": "gv",
    "name": "Manx",
    "native_name": "Gaelg, Gailck"
}, {
    "locale": "mk",
    "name": "Macedonian",
    "native_name": "македонски јазик"
}, {
    "locale": "mg",
    "name": "Malagasy",
    "native_name": "Malagasy fiteny"
}, {
    "locale": "ms",
    "name": "Malay",
    "native_name": "bahasa Melayu, بهاس ملايو‎"
}, {
    "locale": "ml",
    "name": "Malayalam",
    "native_name": "മലയാളം"
}, {
    "locale": "mt",
    "name": "Maltese",
    "native_name": "Malti"
}, {
    "locale": "mi",
    "name": "Māori",
    "native_name": "te reo Māori"
}, {
    "locale": "mr",
    "name": "Marathi (Marāṭhī)",
    "native_name": "मराठी"
}, {
    "locale": "mh",
    "name": "Marshallese",
    "native_name": "Kajin M̧ajeļ"
}, {
    "locale": "mn",
    "name": "Mongolian",
    "native_name": "монгол"
}, {
    "locale": "na",
    "name": "Nauru",
    "native_name": "Ekakairũ Naoero"
}, {
    "locale": "nv",
    "name": "Navajo, Navaho",
    "native_name": "Diné bizaad, Dinékʼehǰí"
}, {
    "locale": "nb",
    "name": "Norwegian Bokmål",
    "native_name": "Norsk bokmål"
}, {
    "locale": "nd",
    "name": "North Ndebele",
    "native_name": "isiNdebele"
}, {
    "locale": "ne",
    "name": "Nepali",
    "native_name": "नेपाली"
}, {
    "locale": "ng",
    "name": "Ndonga",
    "native_name": "Owambo"
}, {
    "locale": "nn",
    "name": "Norwegian Nynorsk",
    "native_name": "Norsk nynorsk"
}, {
    "locale": "no",
    "name": "Norwegian",
    "native_name": "Norsk"
}, {
    "locale": "ii",
    "name": "Nuosu",
    "native_name": "ꆈꌠ꒿ Nuosuhxop"
}, {
    "locale": "nr",
    "name": "South Ndebele",
    "native_name": "isiNdebele"
}, {
    "locale": "oc",
    "name": "Occitan",
    "native_name": "Occitan"
}, {
    "locale": "oj",
    "name": "Ojibwe, Ojibwa",
    "native_name": "ᐊᓂᔑᓈᐯᒧᐎᓐ"
}, {
    "locale": "cu",
    "name": "Old Church Slavonic, Church Slavic, Church Slavonic, Old Bulgarian, Old Slavonic",
    "native_name": "ѩзыкъ словѣньскъ"
}, {
    "locale": "om",
    "name": "Oromo",
    "native_name": "Afaan Oromoo"
}, {
    "locale": "or",
    "name": "Oriya",
    "native_name": "ଓଡ଼ିଆ"
}, {
    "locale": "os",
    "name": "Ossetian, Ossetic",
    "native_name": "ирон æвзаг"
}, {
    "locale": "pa",
    "name": "Panjabi, Punjabi",
    "native_name": "ਪੰਜਾਬੀ, پنجابی‎"
}, {
    "locale": "pi",
    "name": "Pāli",
    "native_name": "पाऴि"
}, {
    "locale": "fa",
    "name": "Persian",
    "native_name": "فارسی"
}, {
    "locale": "pl",
    "name": "Polish",
    "native_name": "polski"
}, {
    "locale": "ps",
    "name": "Pashto, Pushto",
    "native_name": "پښتو"
}, {
    "locale": "pt",
    "name": "Portuguese",
    "native_name": "Português"
}, {
    "locale": "qu",
    "name": "Quechua",
    "native_name": "Runa Simi, Kichwa"
}, {
    "locale": "rm",
    "name": "Romansh",
    "native_name": "rumantsch grischun"
}, {
    "locale": "rn",
    "name": "Kirundi",
    "native_name": "kiRundi"
}, {
    "locale": "ro",
    "name": "Romanian, Moldavian, Moldovan",
    "native_name": "română"
}, {
    "locale": "ru",
    "name": "Russian",
    "native_name": "русский язык"
}, {
    "locale": "sa",
    "name": "Sanskrit (Saṁskṛta)",
    "native_name": "संस्कृतम्"
}, {
    "locale": "sc",
    "name": "Sardinian",
    "native_name": "sardu"
}, {
    "locale": "sd",
    "name": "Sindhi",
    "native_name": "सिन्धी, سنڌي، سندھی‎"
}, {
    "locale": "se",
    "name": "Northern Sami",
    "native_name": "Davvisámegiella"
}, {
    "locale": "sm",
    "name": "Samoan",
    "native_name": "gagana faa Samoa"
}, {
    "locale": "sg",
    "name": "Sango",
    "native_name": "yângâ tî sängö"
}, {
    "locale": "sr",
    "name": "Serbian",
    "native_name": "српски језик"
}, {
    "locale": "gd",
    "name": "Scottish Gaelic; Gaelic",
    "native_name": "Gàidhlig"
}, {
    "locale": "sn",
    "name": "Shona",
    "native_name": "chiShona"
}, {
    "locale": "si",
    "name": "Sinhala, Sinhalese",
    "native_name": "සිංහල"
}, {
    "locale": "sk",
    "name": "Slovak",
    "native_name": "slovenčina"
}, {
    "locale": "sl",
    "name": "Slovene",
    "native_name": "slovenščina"
}, {
    "locale": "so",
    "name": "Somali",
    "native_name": "Soomaaliga, af Soomaali"
}, {
    "locale": "st",
    "name": "Southern Sotho",
    "native_name": "Sesotho"
}, {
    "locale": "es",
    "name": "Spanish; Castilian",
    "native_name": "español, castellano"
}, {
    "locale": "su",
    "name": "Sundanese",
    "native_name": "Basa Sunda"
}, {
    "locale": "sw",
    "name": "Swahili",
    "native_name": "Kiswahili"
}, {
    "locale": "ss",
    "name": "Swati",
    "native_name": "SiSwati"
}, {
    "locale": "sv",
    "name": "Swedish",
    "native_name": "svenska"
}, {
    "locale": "ta",
    "name": "Tamil",
    "native_name": "தமிழ்"
}, {
    "locale": "te",
    "name": "Telugu",
    "native_name": "తెలుగు"
}, {
    "locale": "tg",
    "name": "Tajik",
    "native_name": "тоҷикӣ, toğikī, تاجیکی‎"
}, {
    "locale": "th",
    "name": "Thai",
    "native_name": "ไทย"
}, {
    "locale": "ti",
    "name": "Tigrinya",
    "native_name": "ትግርኛ"
}, {
    "locale": "bo",
    "name": "Tibetan Standard, Tibetan, Central",
    "native_name": "བོད་ཡིག"
}, {
    "locale": "tk",
    "name": "Turkmen",
    "native_name": "Türkmen, Түркмен"
}, {
    "locale": "tl",
    "name": "Tagalog",
    "native_name": "Wikang Tagalog, ᜏᜒᜃᜅ᜔ ᜆᜄᜎᜓᜄ᜔"
}, {
    "locale": "tn",
    "name": "Tswana",
    "native_name": "Setswana"
}, {
    "locale": "to",
    "name": "Tonga (Tonga Islands)",
    "native_name": "faka Tonga"
}, {
    "locale": "tr",
    "name": "Turkish",
    "native_name": "Türkçe"
}, {
    "locale": "ts",
    "name": "Tsonga",
    "native_name": "Xitsonga"
}, {
    "locale": "tt",
    "name": "Tatar",
    "native_name": "татарча, tatarça, تاتارچا‎"
}, {
    "locale": "tw",
    "name": "Twi",
    "native_name": "Twi"
}, {
    "locale": "ty",
    "name": "Tahitian",
    "native_name": "Reo Tahiti"
}, {
    "locale": "ug",
    "name": "Uighur, Uyghur",
    "native_name": "Uyƣurqə, ئۇيغۇرچە‎"
}, {
    "locale": "uk",
    "name": "Ukrainian",
    "native_name": "українська"
}, {
    "locale": "ur",
    "name": "Urdu",
    "native_name": "اردو"
}, {
    "locale": "uz",
    "name": "Uzbek",
    "native_name": "zbek, Ўзбек, أۇزبېك‎"
}, {
    "locale": "ve",
    "name": "Venda",
    "native_name": "Tshivenḓa"
}, {
    "locale": "vi",
    "name": "Vietnamese",
    "native_name": "Tiếng Việt"
}, {
    "locale": "vo",
    "name": "Volapük",
    "native_name": "Volapük"
}, {
    "locale": "wa",
    "name": "Walloon",
    "native_name": "Walon"
}, {
    "locale": "cy",
    "name": "Welsh",
    "native_name": "Cymraeg"
}, {
    "locale": "wo",
    "name": "Wolof",
    "native_name": "Wollof"
}, {
    "locale": "fy",
    "name": "Western Frisian",
    "native_name": "Frysk"
}, {
    "locale": "xh",
    "name": "Xhosa",
    "native_name": "isiXhosa"
}, {
    "locale": "yi",
    "name": "Yiddish",
    "native_name": "ייִדיש"
}, {
    "locale": "yo",
    "name": "Yoruba",
    "native_name": "Yorùbá"
}, {
    "locale": "za",
    "name": "Zhuang, Chuang",
    "native_name": "Saɯ cueŋƅ, Saw cuengh"
}]';

        $languages = json_decode($arrLanguages, true);

        foreach ($languages as $language) {
            \App\Models\Language::create($language);
        }
    }
}
