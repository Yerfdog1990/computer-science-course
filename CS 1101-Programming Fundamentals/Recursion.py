# -*- coding: utf-8 -*-
"""
Created on Wed Feb 28 11:32:19 2024

@author: Yerdog
"""

def factorial(n):
    if n == 0:
        return 1
    else:
        return n*factorial(n-1)
    
print (factorial(5))