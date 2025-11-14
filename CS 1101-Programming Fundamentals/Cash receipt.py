# -*- coding: utf-8 -*-
"""
Created on Wed Feb 28 11:19:35 2024

@author: Yerdog
"""

def create_catalog():
    store_name = "Yerfdog Online Store"
    print(store_name)
    print("--------------------")
    print("Products           Price")
    
    item1 = "Item 1"
    item2 = "Item 2"
    item3 = "Item 3"
    combo1 = "Combo 1(Item 1+2)"
    combo2 = "Combo 2(Item 2+3)"
    combo3 = "Combo 3(Item 1+3)"
    combo4 = "Combo 4(Item 1+2+3)"
    
    item1_price = 200
    item2_price = 400
    item3_price = 600
    combo1_price= 0.9*(item1_price + item2_price)
    combo2_price= 0.9*(item2_price + item3_price)
    combo3_price= 0.9*(item1_price + item3_price)
    combo4_price= 0.75*(item1_price + item2_price + item3_price)
     
    print("Item 1:", item1_price)
    print("Item 2:", item2_price)
    print("Item 3:", item3_price)
    print("Combo 1(Item 1+2):",   combo1_price)
    print("Combo 2(Item 2+3):",   combo2_price)
    print("Combo 3(Item 1+3):",   combo3_price)
    print("Combo 4(Item 1+2+3):", combo4_price)
    print("----------------------")
    print("For delivery contact:+254729073231")
    
# Call the function to generate the catalog
create_catalog()