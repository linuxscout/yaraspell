<?php
/*
*************************************************************************** 
*   Copyright (C) 2008 by Felipe Ribeiro                                  * 
*   felipernb@gmail.com                                                   * 
*   http://www.feliperibeiro.com                                          * 
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
 * This class implements the Spell correcting feature, useful for the 
 * "Did you mean" functionality on the search engine. Using a dicionary of words
 * extracted from the product catalog.
 * 
 * Based on the concepts of Peter Norvig: http://norvig.com/spell-correct.html
 * 
 * @author Felipe Ribeiro <felipernb@gmail.com>
 * @date September 18th, 2008
 * @package catalog
 *
 */
require("spelldb.php");
require("stemmer.php");
class SpellCorrector {
      public $NWORDS ;
    //cho "len NWORDS".sizeof($NWORDS);
    public $database; 
    private $stemmer;
    public $worddict;
        public     function __construct()
    {
        
    $this->NWORDS = array("october"=>5, "octobus"=>10, "salam"=>15);
    //cho "len NWORDS".sizeof($NWORDS);
    $this->worddict =array();
    $this->database = new spellDatabase();
    $this->stemmer = new Stemmer();
}
    public function segment($word)
    {
        return $this->stemmer->segment($word);
        //~ return array("stem"=>$word, "affix"=>"-");
    }
    public function lookup($word)
    {
        /*
        Lookup if the word is correct or not
        @param word: input word
        @type  word: unicode
        @return: True if word exists else False
        @rtype: Boolean
        */
        $test = False;
        if (empty($word))
            $test = True;
        # test if the word is previouslly spelled
        # can get True or False
        elseif (array_key_exists( $word, $this->worddict))
            $test = $this->worddict[$word];
        else
        {
            # if the word is not spelled 
            $stemmed = $this->segment($word);      
            $test = $this->database->lookup($word, $stemmed['stem'], $stemmed['affix']);
            $this->worddict[$word] = $test;
        }
        if (isset($stemmed)) 
        {
            $stem = $stemmed['stem'];
        $affix = $stemmed['affix'];
        }else{$stem =""; $affix="";
        }
        //~ if ($test )  echo "# word $word  is $test. [".$stem." ".$affix."<br/>";
        return $test;
    }

    private  function edits1($word) {
        //~ $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        $alphabet = 'ذضصثقفغعهخحجدطكمنتالبيسشظزوةىرؤءئأإآ';
        //~ $alphabet = mb_str_split($alphabet);
        $alphabet = str_split($alphabet);
        $n = mb_strlen($word);
        $edits = array();
        for($i = 0 ; $i<$n;$i++) {
            $edits[] = mb_substr($word,0,$i).mb_substr($word,$i+1);       //deleting one char
            foreach($alphabet as $c) {
                $edits[] = mb_substr($word,0,$i) . $c . mb_substr($word,$i+1); //substituting one char
            }
        }
        for($i = 0; $i < $n-1; $i++) {
            $edits[] = mb_substr($word,0,$i).$word[$i+1].$word[$i].mb_substr($word,$i+2); //swapping chars order
        }
        for($i=0; $i < $n+1; $i++) {
            foreach($alphabet as $c) {
                $edits[] = mb_substr($word,0,$i).$c.mb_substr($word,$i); //inserting one char
            }
        }
        //~ echo "condidate ".implode("; ", $edits)."<br>";

        return $edits;
    }
    
    /**
     * Generate possible "disturbances" in a second level that exist on the dictionary
     *
     * @param string $word
     * @return array
     */
    private  function known_edits2($word) {
        $known = array();
        foreach($this->edits1($word) as $e1) {
            foreach($this->edits1($e1) as $e2) {
                if($this->lookup($e2)) $known[] = $e2;                
            }
        }
        return $known;
    }
    
    /**
     * Given a list of words, returns the subset that is present on the dictionary
     *
     * @param array $words
     * @return array
     */
    private  function known(array $words) {
        $known = array();
        foreach($words as $w) {
            if($this->lookup($w)) {
                $known[] = $w;

            }
        }
        return $known;
    }
    
    
    /**
     * Returns the word that is present on the dictionary that is the most similar (and the most relevant) to the
     * word passed as parameter, 
     *
     * @param string $word
     * @return string
     */
    public function correct($word) {
        $word = trim($word);
        if(empty($word)) return array();
        
        $candidates = array(); 
        if($this->known(array($word))) {
            return array($word);
        } 
        elseif(($tmp_candidates = $this->known($this->edits1($word)))) {
            foreach($tmp_candidates as $candidate) {
                $candidates[] = $candidate;
            }
        } /*elseif(($tmp_candidates = $this->known_edits2($word))) {
            foreach($tmp_candidates as $candidate) {
                $candidates[] = $candidate;
            }
        } */
        else {
            return array($word);
        }
        //~ $max = 0;
        /*foreach($candidates as $c) {
            $value = 10; #$this->NWORDS[$c];
            if( $value > $max) {
                $max = $value;
                $word = $c;
            }
        }*/
        return $candidates;
    }
    
    
}



?>
