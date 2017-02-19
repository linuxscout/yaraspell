<?php
/*
spelldb
 * 
 * Copyright 2015 zerrouki <zerrouki@majd4>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * 
 */
include('config.php');
echo "Hello";
class spellDatabase
{
    /*
        Arabic spell dictionary Class
        Used to allow abstract acces to lexicon of arabic language, 
        can get indexed and hashed entries from the  basic lexicon
        add also, support to extract attributtes from entries
    */
            # this dict contains all affixes, it will be loaded in the first time,
        # when we ask for a correction
        public $affixdict = False;
        # this dict contains a cache for requested words, it will be updaterequested words,7
        # in order to reduce database access
        public $stemdict = False;

        # this list contains costomized words added by users,
        public $costumdict = False;
        # get the database path
        public $db_connect = False;
    public     function __construct()
    {
        /*
        initialisation of dictionary from a data dictionary, 
        create indexes to speed up the access.
        @param databasefile: database file name
        @type databasefile: string
        */
        global $host,$user,$password, $db_name;
        # get the database path
         $this->db_connect = mysql_connect($host,$user,$password);
        if($this->db_connect )
        { 
          if(mysql_select_db($db_name))
          {
            echo 'Succès de connexion.<br>';
            mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $this->db_connect );

        } else {
            die('Echec de connexion à la base.');
          }
          //mysql_close();
        } else {
          die('Echec de connexion au serveur de base de données.');
        }        

        
    }
    
    function  __load_affix()
    {
        
        //load affix form data base into $this->$affixdict, to speed up search
        
        $sql  =  "select * FROM affix"; 
        $result = mysql_query($sql);
        if(!$result)
        {
            echo " Error on request $sql";
            return False;
        }
        else
            {# get one row 
            while($ligne = mysql_fetch_array($result))
                   $this->affixdict[$ligne["affix"]] = $ligne["flag"];

            }
    }
    function __load_costum()
    {
        /*
        load costum dictionary form data base into $this->$costumdict, to speed up search
        */
       $sql  =  "select * FROM costum"; 
        $result = mysql_query($sql);
        if(!$result)
        {
            echo " Error on request $sql";
            return False;
        }
        else
            {# get one row 
            while($ligne = mysql_fetch_array($result))
                   $this->costumdict[] = $ligne["word"];

            }
    }
    
    public function lookup($word, $stem, $affix)
        {
        /*
        look up for word in the dictionary
        @param word: given word.
        @type word: unicode.
        @param stem: the stemmed word.
        @type stem: unicode.
        @param affix: the stemmed word.
        @type affix: unicode.       
        @return: True if exists.
        @rtype: Boolean.
        */
    
        if (empty($this->costumdict))
            $this->__load_costum();
        # test if the word is a costumed word
        if (in_array($word, $this->costumdict))
            return True;
        if (empty($this->affixdict))    $this->__load_affix();

        # the affix dict is not loaded, we load it 
        # if the affix dict is loaded, look up for the input affix in the dict
        if(array_key_exists($affix, $this->affixdict))
            $flag = $this->affixdict[$affix];
        #print (u"$flag '%s' '%s' '%s'" %($flag, affix, stem )).encode('utf8')
        else return False;
        if (($this->stemdict) and (array_key_exists($stem, $this->stemdict)))
            $flags = $this->stemdict[$stem];
        else 
        {
            # if the stem if not looked up previously, let lookup for it a onc
            $sql  =  "select * FROM words WHERE stem = '$stem'";
        $result = mysql_query($sql);
        if(!$result)
        {
            //echo " Error on request $sql\n";
            return False;
        }
        else
            {# get one row 
            $this->stemdict[$stem] = array();
            while($ligne = mysql_fetch_array($result))
                   $this->stemdict[$stem] = explode(";",$ligne["flags"]);
            }
                # extract $flags 
                $flags = $this->stemdict[$stem];
                # save data in stemdict to speed up future lookup
        }
            if (in_array($flag, $flags))
                return True;
            else
                return False;
        return False;
    }

    function add_to_custom($word)
    {
        /*
        Add a new word to custom dictionary
        @param word: the correct word to be added to custom dictionary
        @type word: unicode
        */
        $sql  =  "INSERT INTO costum (word) VALUES ('$word')"; 
        $result = mysql_query($sql);
        if(!$result)
        {
            echo " Error on request $sql";
            return False;
        }
        else
            return True;
        //$this->$cursor.execute.execute(u'INSERT INTO costum (word) VALUES (?)', (word))
    }
} // end class

/*
echo "Spell DB";
$spldb = new spellDatabase();
echo "<table>";

$spldb->__load_affix();

foreach( $spldb->affixdict as $key=>$value )
{
    echo  "<tr><td>".$key."</td><td>".$value."</td></tr>";
    
}
echo "</table>";

$spldb->__load_costum();
echo "<br>".sizeof($spldb->costumdict);
echo "<table>";
foreach( $spldb->costumdict as $value )
{
    echo  "<tr><td>".$value."</td></tr>";
    
}
echo "</table>";
echo "<br>".sizeof($spldb->costumdict);
//echo "add to costum".$spldb->add_to_custom("السلام عليكم");
echo "<br>lookup for a word <br>";
echo "lookup".$spldb->lookup("taha", "taha", "-");
mysql_close($spldb->db_connect);

*/
?>

