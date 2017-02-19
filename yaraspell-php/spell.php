<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>بدون اسم</title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <meta name="generator" content="Geany 1.23.1" />
</head>

<body dir='rtl'>

<?php
// Report all errors except E_NOTICE
ini_set('display_errors','On'); 
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
echo "<h2>تجربة المدقق الإمل</ائيh2>";
include 'SpellCorrector.php';
$sp = new SpellCorrector();
echo "<h6>التدقيق</h6>";
echo "<table>";
echo "<tr><th>الأصل</th><th>تصحيح</th></tr>";
$words = array('tah', 'toohi', 'انتظار', 'استعمال', 'hohi', "إنتظار", "الإستعمال",  "الضلام", "يستخدمو", "شلام", "سلام");
foreach( $words as $word)
{    $exists = $sp->lookup($word);
    if ($exists)
    echo "<tr><td>$word</td><td>ok</td></tr>";
    //~ echo $word." ok <br>";
    else
    { 
        $sugs = $sp->correct($word);
        echo "<tr><td>$word</td><td>".implode(', ', $sugs)."</td></tr>";
  }
}
echo "</table>";
//it will output *october*
?>
    
</body>
