<?php
/*
*************************************************************************** 
*   Copyright (C) 2015 by Taha Zerrouki                              * 
*   taha. zerrouki @ gmail                                                   * 
*   http://www.TahaDz.com                                          * 
*                                                                         * 
*   Permission is hereby granted, free of charge, to any person obtaining * 
*   a copy of this software and associated documentation files (the       * 
*   "Software"), to deal in the Software without restriction, including   * 
*   without limitation the rights to use, copy, modify, merge, publish,   * 
*   distribute, sublicense, and/or sell copies of the Software, and to    * 
*   permit persons to whom the Software is furnished to do so, subject to * 
*   the following conditions:                                             * 
*                                                                         * 
*   The above copyright notice and this permission notice shall be        * 
*   included in all copies or substantial portions of the Software.       * 
*                                                                         * 
*   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,       * 
*   EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF    * 
*   MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.* 
*   IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR     * 
*   OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, * 
*   ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR * 
*   OTHER DEALINGS IN THE SOFTWARE.                                       * 
*************************************************************************** 
*/ 

    /**
     * Strip all tashkeel characters from an Arabic text.
     * 
     * @param string $text The text to be stripped.
     *      
     * @return string the stripped text.
     * @author Djihed Afifi <djihed@gmail.com>
     */
     require("stem_const.php");
     function mb_str_split( $string ) {
    # Split at all position not after the start: ^
    # and not before the end: $
    return preg_split('/(?<!^)(?!$)/u', $string );
} 
     $tashkeel = array("ُ","ٌ","َ","ً","ِ","ٍ","ّ", "ْ");
    function strip_tashkeel($text) 
    {
        global $tashkeel;
        return str_replace($tashkeel, "", $text);
    }
    function sortmy($a,$b){
    return mb_strlen($b)-mb_strlen($a);
    }

    class Stemmer
    {
        public $prefix_list ;
        public $suffix_list; 
        function __construct()
         {
             global $DEFAULT_PREFIX_LIST, $DEFAULT_SUFFIX_LIST;
        $this->prefix_list = $DEFAULT_PREFIX_LIST;
        $this->suffix_list = $DEFAULT_SUFFIX_LIST; 
        usort($this->prefix_list,'sortmy');
        usort($this->suffix_list,'sortmy'); 
         }
    function segment($word)
    {
        $stem = strip_tashkeel($word);
        $prefix = "";
        $suffix ="";
       foreach($this->prefix_list as $value)
        {
        
         if (!empty($value) && (mb_strpos($stem, $value) === 0))
            {
            $stem = mb_substr($stem, mb_strlen($value)).'';
            $prefix = ''.$value;
            //~ echo "prefix ".$value." ok <br>";
            break;
            }
        //~ else   echo "prefix ".$value." no <br>";
        }
        foreach($this->suffix_list as $value)
        {
         if (!empty($value) &&(mb_strpos($stem, $value) === (mb_strlen($stem)-mb_strlen($value))))
            {
            $stem = mb_substr($stem,0, mb_strlen($stem)-mb_strlen($value)).'';
            $suffix = ''.$value;
            break;
            }
        }
        //~ echo "<h5>word '$word', stem'$stem', suffix '$suffix', prefix '$prefix', </h5>";
        return array("affix"=>"$prefix-$suffix", "stem"=>"$stem");
    }
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>بدون اسم</title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <meta name="generator" content="Geany 1.23.1" />
</head>

<body>
<?php 
/* test */
    $sp = new Stemmer();
    $words =array('', 'بالاستعمالات', 'المقاولاتية', 'وسيعملونهما', 'بفضلكمانهم');
    foreach( $words as $word)
        $sp->segment($word);
?>  
</body>

</html>

