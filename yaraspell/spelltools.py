#!/usr/bin/python 
# -*- coding: utf-8 -*-
# Name:        spelltools
# Purpose:     functions and tools for Arabic spellchecker
# Author:      Taha Zerrouki (taha.zerrouki[at]gmail.com) 
# Created:     2015-03-25
# Copyright:   (c) Taha Zerrouki 2015
# Licence:     GPL 
# Source : http://norvig.com/spell-correct.html How to Write a Spelling Corrector, Peter Norvig (GPL)
#---------------------------------------------------------------------

"""
    Arabic spell checker functions 
"""
import re
from collections import Counter
alphabet = u'ذضصثقفغعهخحجدطكمنتالبيسشظزوةىرؤءئأإآ'
forbiden_bigrams=u"""هغ
هإ
هأ
هء
هخ
هح
أء
ىع
ىظ
ىغ
ىر
ىذ
ىس
ىز
ىص
ىش
ىط
ىض
ىة
ىب
ىث
ىت
ىح
ىج
ىد
ىخ
ىأ
ىآ
ىإ
ىؤ
ىا
ىئ
ىى
ىو
ىي
ىف
ىك
ىق
ىم
ىل
ىه
ىن
حء
قإ
حأ
قخ
قغ
حخ
يإ
مإ
ؤئ
ؤؤ
ؤإ
ؤآ
ؤأ
ؤء
ؤغ
ؤظ
ؤع
ؤض
ةع
ةظ
ةغ
ةر
ةذ
ةس
ةز
ةص
ةش
ةط
ةض
ةة
ةب
ةث
ةت
ةح
ةج
ةد
ةخ
ةء
ةأ
ةآ
ةإ
ةؤ
ةا
ةئ
ةى
ةو
ةي
ةف
ةك
ةق
ةم
ةل
ةه
ةن
جإ
جء
ظخ
اآ
شض
شث
شإ
اى
نء
نإ
آإ
آا
آآ
آأ
آض
آظ
آع
دإ
دظ
دط
دض
دص
دذ
إة
إإ
إؤ
إا
إئ
إء
إأ
إآ
قء
إى
ظث
ظد
ظج
ظح
ظأ
ظء
تإ
تء
عغ
غح
غخ
غؤ
غإ
غئ
غء
غآ
غأ
غع
غغ
حإ
حئ
حع
حغ
ذز
ذس
ذض
ذط
ذش
ذص
ذغ
ذظ
ذإ
ذث
ثح
ثظ
صظ
صش
صض
صذ
صس
صز
صج
صث
صإ
صء
صآ
ظإ
ظز
ظس
ظذ
ظض
ظط
ظش
ظص
ثإ
ظق
ثس
ثز
ثذ
ثط
ثض
ثص
ثش
ئء
ئآ
ئأ
ئؤ
ئإ
ئئ
سظ
سز
سض
سص
سث
سإ
ءء
ءإ
ءؤ
ءئ
ءث
ءح
ءج
ءد
ءخ
ءر
ءذ
ءس
ءز
ءص
ءش
ءط
ءض
ءع
ءظ
ءغ
خح
خء
خأ
خإ
خئ
خظ
خغ
ءف
ءق
ضء
ضإ
ضث
ضذ
ضز
ضس
ضش
ضص
رإ
عح
عخ
عء
عأ
عآ
عإ
عؤ
عئ
زش
زص
زض
زذ
زظ
زإ
زث
طإ
طث
طض
طص
طذ
طظ
""".split("\n")
def edits1(word):
   splits     = [(word[:i], word[i:]) for i in range(len(word) + 1)]
   deletes    = [a + b[1:] for a, b in splits if b]
   transposes = [a + b[1] + b[0] + b[2:] for a, b in splits if len(b)>1]
   replaces   = [a + c + b[1:] for a, b in splits for c in alphabet if b]
   inserts    = [a + c + b     for a, b in splits for c in alphabet]
   return set(transposes + replaces + inserts + deletes)
def getbigrams(word):
    """
    extract all bigrams from the word with stats
    """
    text = " "+word+" "
    bigrams = Counter(x+y for x, y in zip(*[text[i:] for i in range(2)]))
    return bigrams

def is_valid(word):
    # test if the word is arabic form
    if not araby.is_arabicword(word):
        return False
    # test some cases 
    # the teh marbuta is before any arabic letter
    # the hamza online is after 
    bigrams = getbigrams(word)
    for bi in bigrams:
        if bi in forbiden_bigrams:
            return False
    return True
import pyarabic.araby as araby

LETTER_MAP={
araby.ALEF_HAMZA_BELOW :u"a",
araby.ALEF:u"a",
araby.ALEF_HAMZA_ABOVE:u"a",
araby.ALEF_MADDA:u"a",

araby.HAMZA :u"e",
araby.WAW_HAMZA:u"e",
araby.YEH_HAMZA:u"e",

araby.ZAH:u"d",
araby.DAD:u"d",

araby.DAL:u"c",
araby.THAL:u"c",

araby.TEH_MARBUTA:u"t",
araby.TEH:u"t",

araby.TEH_MARBUTA:u"t",
araby.HEH:u"t",


araby.ALEF_MAKSURA:u"y",
araby.YEH:u"y",
}
def normalize(word):
    new_word =[]
    for c in word:
        new_word.append(LETTER_MAP.get(c,c))
    return u"".join(new_word)

        
def mainly():
    """
    main test
    """
    words =u"""ضلام ألام ضلال لام ظلام ضام غلام إلام نلام هلام ضخام سلام ملام ضلا ضمام تلام علام يلام ضلان كلام ضلتم""".split(" ")

    source = u"ضلام"
    normsource = normalize(source)
    normlist = [normalize(word) for word in words]
    for word in words:
        print u"\t".join([word, normalize(word)]).encode("utf8")
    condidates = filter(lambda w: normalize(w) == normsource, words)
    print "condidates", u"\t".join(condidates).encode('utf8')
    editlist =  edits1(source)
    print "len(editlist)", len(editlist)
    validwords = [word for word in editlist if araby.is_arabicword(word)]
    print "len(validwords)", len(validwords)
    print u"\n".join(editlist).encode('utf8')
    
if __name__ == "__main__":
    mainly()
