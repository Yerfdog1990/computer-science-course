# -*- coding: utf-8 -*-
"""
Created on Wed Feb 28 11:00:27 2024

@author: Yerdog
"""

from math import floor

def BinarySearch(r,j,k,C):
    found = False
    if j <= k:
        mid = floor((j + k)/2)
        print('probing_at_position_' +str(mid))
        if r[mid] == C:
            location = mid
            found = True
            print('found_in_position_' +str(location))
            return location
        else:
            if r[mid] > C:
                BinarySearch(r, j, mid-1, C)
            else:
                BinarySearch(r, mid+1, k, C)
    else: 
            print('not_found')
            return False
s=[1,9,13,16,30,31,32,33,36,37,38,45,49,50,52,61,63,64,69,77,79,80,81,83,86,90,93,96]
BinarySearch(s,0,len(s)-1,30)
        
        