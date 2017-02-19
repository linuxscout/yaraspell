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
ini_set('display_errors','On'); 
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require('config.php');
    $affixdict = array();
        # this dict contains a cache for requested words, it will be updaterequested words,7
        # in order to reduce database access
$stemdict = array();

        # this list contains costomized words added by users,
$costumdict = array();
        # get the database path
$db_connect;
    function __construct()
    {
        /*
        initialisation of dictionary from a data dictionary, 
        create indexes to speed up the access.
        @param databasefile: database file name
        @type databasefile: string
        */
        global $host,$user,$password, $db_name;
        # get the database path
         $db_connect = mysql_connect($host,$user,$password);
        if($db_connect )
        {
          if(mysql_select_db($db_name))
          {
            echo 'Succès de connexion.<br>';

        } else {
            die('Echec de connexion à la base.');
          }
          mysql_close();
        } else {
          die('Echec de connexion au serveur de base de données.');
        }        

        
    }
    

__construct();
?>

