#!/usr/bin/env python
# -*- coding: utf-8 -*-
#
#  test.py
#  
#  Copyright 2015 zerrouki <zerrouki@majd4>
#  
#  This program is free software; you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation; either version 2 of the License, or
#  (at your option) any later version.
#  
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#  
#  You should have received a copy of the GNU General Public License
#  along with this program; if not, write to the Free Software
#  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
#  MA 02110-1301, USA.
#  
#  

def compact_list(lst):
    sublists =[]
    tmp = []
    for x in lst:
        if not tmp:
            tmp.append(x)
        elif tmp[len(tmp)-1] +1 == x :
            tmp.append(x)
        else:
            sublists.append(tmp)
            tmp = [x]
    if tmp:
        sublists.append(tmp)
    text = ""
    for l in sublists:
        if len(l) > 2:
            text  +=";%s-%s"%(str(l[0]), str(l.pop()))
        elif len(l) == 2:
             text += ";%s;%s"%(str(l[0]), str(l.pop()) )
        else:
             text += ";%s"%str(l[0])
    return text
def extend_flag(start, end):
    cs = start[-1]
    ce = end[-1]
    for x in [start[:-1]+chr(i) for i in range(ord(cs),ord(ce))]:
        print x
def main():
    print  compact_list([1,2,3,5,6,8,9,11])
    print extend_flag("1E", "1x")
    return 0

if __name__ == '__main__':
    main()

