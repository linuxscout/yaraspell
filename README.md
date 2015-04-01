YaraSpell is an arabic spell checker designed to be embeded on applications
Yaraspell is open source
It's a simple spellechecker which will be implemented in different programming languages to allow developpers to integrate it on their applications
مدقق اليراع مدقق عربي مفتوح المصدر مصمم ليكون مدمجا في التطبيقات وعلى رأسها الوب
ه مدقق مبسّط سيبرمج بلغات مختلفة ليتيح للمطورين تضمين خدمة التدقيق الإملائي في تطبيقاتهم المختلفة

تتكون المكتبة من قسمين:
1- قسم القاموس: 
=================
القاموس مكون من قاعدة بيانات sql  مكونة من جدول أساسين
أ- جدول الزوائد affix يتكون كل مدخل من معلومات هي الزائدة affix وعلامتها flag وتكرار الزائدة stats
ب- جدول الكلمات words: يتكون من مدخلين هما الجذع الأصلي stem وهو كلمة أو جزء من كلمة، والعلامات المقبولة للجذع flags وهي تمثل كل الزوائد التي تلتصق بالجذع
2- قسم التدقيق:
================
آلية التدقيق
----------------
 تعتمد على تجذيع للكلمة segmentation يعطي نتيجة وحيدة، بها الزائدة المركبة affix  والجذع stem، يبحث أولا عن الزائدة في قاموس الزوائد، إن وجدت يأخذ علامتها ويتحقق من وجود الجذع stem في قاموس الجذوع ثم يتحقق أنّ علامة الزائدة مقبولة في زوائد الجذع، وإلا تكون النتيجة خاطئة
آلية اقتراح التصحيحات:
---------------------
تقوم وحدة الاقتراح بتوليد الحالات الممكنة من حذف وتبديل موضع و زيادة حروف، التقسيم
تلك الكلمات المقترحة تمرر على المدقق لاختيار الصحيحة منها، والصحيحة تظهر في قائمة الاقتراحات

ألية ترتيب المتقرحات:
--------------------
للعمل

كيف يستعمل؟
---------------


بنية المجلدات
================
.
└── yaraspell           The library
    ├── spelldb.py      database class for spellchecker
    ├── spelldict.py    class for spellcheker
    └── spelltools.py   tools and functions used by spelldict
├── data
│   └── spellcheck.sqlite   The database file in salite format
├── build_dict              scripts used to construct database from wordlist
│   ├── data                the datafiles
│   │   └── Arabic-Wordlist-1.6
│   │       └── arabic-wordlist-1.6.txt   The arabic wordlist from Mohamed Attia (open source)
│   ├── output
│   │   ├── arabic.aff      the result of buildict.py script as affix dictionary csv file
│   │   ├── arabic.dic      the result of buildict.py script as word dictionary csv file
│   │   └── spellcheck.sqlite  the result of csvtosqlite.py script as sqlitefile
│   ├── tests               
│   └── tools
│       ├── buildict.py     generate affixes and words dictionary in csv format
│       └── csvtosqlite.py  convert csv format to sqlite format
├── docs
│   └── README              readme file
├── lib
│   ├── pyarabic            basic library to handle araic words and texts
│   │   ├── araby_const.py  basic constants used in pyarabic like letters
│   │   ├── araby.py        basic functions used in pyarabic especialy strip_tashkeel
│   │   └── others...       others files are not used
│   └── tashaphyne
│       ├── arabic_const.py basic constants used in pyarabic like letters
│       ├── stem_const.py   basic constants used in stemming like letters
│       ├── stemming.py     basic functions used to stem words
│       └── others...       others files are not used
├── tests 
│   └── output  


