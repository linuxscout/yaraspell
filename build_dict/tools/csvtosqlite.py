#!/usr/bin/python 
# -*- coding: utf-8 -*-
#---------------------------------------------------------------------
# Name:        cvs2sqlite
# Purpose:     convert spell dict to sqlite format 
# Author:      Taha Zerrouki (taha.zerrouki[at]gmail.com) 
# Created:     2015-03-25
#  Copyright:   (c) Taha Zerrouki 2011 # Licence:     GPL 
#---------------------------------------------------------------------

"""
    convert spell dict from csv to sqlite format  
"""

import re
import sys
sys.path.append("../../lib")
LIMIT = 10000000
CSVFILENAMEAFFIX = '../output/arabic.aff'
CSVFILENAMEWORDS = '../output/arabic.dic'
DBFILENAME = '../output/spellcheck.sqlite'

import sqlite3
def convert(inputfile = CSVFILENAMEAFFIX, dbfile = DBFILENAME):
    """
    convert the affixes file from csv format to sqlite
    @param inputfile: affixes file name
    @type  inputfile: string
    @param dbfile: sqlite db file
    @type  dbfile: string
    """
    conn = sqlite3.connect( dbfile )
    conn.text_factory = str  #bugger 8-bit bytestrings
    cur = conn.cursor()
    cur.execute('CREATE TABLE IF NOT EXISTS affix (flag VARCHAR, affix VARCHAR UNIQUE NULL, stats integer)')

    try:
        myfile = open(inputfile, "r+")
    except:
        print "ERROR: can't open file"
        import sys
        sys.exit()
    line = myfile.readline().decode('utf8')
    line_nb = 1
    while line and line_nb < LIMIT:
        fields = line.strip("\n").split("\t")
        if len(fields) >= 3:
            flag = fields[0]
            affix = fields[1]
            stats = int(fields[2])
            #print u";".join([flag, affix, str(stats)]).encode('utf8')
            cur.execute(u'INSERT OR IGNORE INTO affix (flag, affix, stats) VALUES (?,?, ?)', (flag, affix, stats))

        line = myfile.readline().decode('utf8')
        line_nb += 1

    myfile.close()

    conn.commit()
    # create index 
    cur.execute('CREATE INDEX IF NOT EXISTS idxaffix ON affix (affix)')
    conn.commit()
    conn.close()
    # print affix codes

def convert_words(inputfile = CSVFILENAMEWORDS, dbfile = DBFILENAME):
    """
    convert the words file from csv format to sqlite
    @param inputfile: words file name
    @type  inputfile: string
    @param dbfile: sqlite db file
    @type  dbfile: string
    """
    conn = sqlite3.connect( dbfile )
    conn.text_factory = str  #bugger 8-bit bytestrings
    cur = conn.cursor()
    cur.execute('CREATE TABLE IF NOT EXISTS words (stem VARCHAR UNIQUE NULL, flags TEXT)')
    cur.execute('CREATE TABLE IF NOT EXISTS costum (word VARCHAR PRIMARY KEY  NOT NULL  UNIQUE )')

    try:
        myfile = open(inputfile, "r+")
    except:
        print "ERROR: can't open file"
        import sys
        sys.exit()
    line = myfile.readline().decode('utf8')
    line_nb = 1
    while line and line_nb < LIMIT:
        fields = line.strip("\n").split("\t")
        if len(fields) >= 2:
            stem = fields[0]
            flags = fields[1]
            #print u";".join([stem, flags]).encode('utf8')
            cur.execute(u'INSERT OR IGNORE INTO words (stem, flags) VALUES (?,?)', (stem, flags))

        line = myfile.readline().decode('utf8')
        line_nb += 1

    myfile.close()
    conn.commit()
    # create index 
    cur.execute('CREATE INDEX IF NOT EXISTS idxwords ON words (stem)')
    conn.commit()
    conn.close()       

if __name__ == "__main__":

    convert()
    convert_words()
