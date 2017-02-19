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
class SpellCorrector {
    public $NWORDS = array("october"=>5, "octobus"=>10, "salam"=>15);
    //cho "len NWORDS".sizeof($NWORDS);
    public $database = new spellDatabase();
    
    /**
     * Reads a text and extracts the list of words
     *
     * @param string $text
     * @return array The list of words
     */
    private  function  words($text) {
        $matches = array();
        preg_match_all("/[a-z]+/",strtolower($text),$matches);
        return $matches[0];
    }
    
    /**
     * Creates a table (dictionary) where the word is the key and the value is it's relevance 
     * in the text (the number of times it appear)
     *
     * @param array $features
     * @return array
     */
    private  function train(array $features) {
        $model = array();
        $count = count($features);
        for($i = 0; $i<$count; $i++) {
            $f = $features[$i];
            $model[$f] +=1;
        }
        return $model;
    }
    
    /**
     * Generates a list of possible "disturbances" on the passed string
     *
     * @param string $word
     * @return array
     */
    private  function edits1($word) {
        $alphabet = 'abcdefghijklmnopqrstuvwxyz';
        $alphabet = str_split($alphabet);
        $n = strlen($word);
        $edits = array();
        for($i = 0 ; $i<$n;$i++) {
            $edits[] = substr($word,0,$i).substr($word,$i+1);       //deleting one char
            foreach($alphabet as $c) {
                $edits[] = substr($word,0,$i) . $c . substr($word,$i+1); //substituting one char
            }
        }
        for($i = 0; $i < $n-1; $i++) {
            $edits[] = substr($word,0,$i).$word[$i+1].$word[$i].substr($word,$i+2); //swapping chars order
        }
        for($i=0; $i < $n+1; $i++) {
            foreach($alphabet as $c) {
                $edits[] = substr($word,0,$i).$c.substr($word,$i); //inserting one char
            }
        }

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
                if(array_key_exists($e2,$this->NWORDS)) $known[] = $e2;                
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
            if(array_key_exists($w,$this->NWORDS)) {
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
        if(empty($word)) return;
        
        $word = strtolower($word);
        
        if(empty(self::$NWORDS)) {
            
            /* To optimize performance, the serialized dictionary can be saved on a file
            instead of parsing every single execution */
            if(!file_exists('serialized_dictionary.txt')) {
                $this->NWORDS = $this->train($this->words(file_get_contents("big.txt")));
                $fp = fopen("serialized_dictionary.txt","w+");
                fwrite($fp,serialize($this->NWORDS));
                fclose($fp);
            } else {
                $this->NWORDS = unserialize(file_get_contents("serialized_dictionary.txt"));
            }
        }
        $candidates = array(); 
        if($this->known(array($word))) {
            return $word;
        } elseif(($tmp_candidates = $this->known($this->edits1($word)))) {
            foreach($tmp_candidates as $candidate) {
                $candidates[] = $candidate;
            }
        } elseif(($tmp_candidates = $this->known_edits2($word))) {
            foreach($tmp_candidates as $candidate) {
                $candidates[] = $candidate;
            }
        } else {
            return $word;
        }
        $max = 0;
        foreach($candidates as $c) {
            $value = $this->NWORDS[$c];
            if( $value > $max) {
                $max = $value;
                $word = $c;
            }
        }
        return $word;
    }
    
    
}

?>
