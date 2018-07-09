import os
import sys
import unittest
import binascii
import string

from PyPDF2 import PdfFileReader 

c = PdfFileReader('pdf2.pdf')	
c.getDocumentInfo()
p = c.getNumPages()
print('Pages',p)

file = open('pdf2.txt','w') 

for x in range(0, p):
	txt = c.getPage(x).extractText()
	txt = txt.replace(chr(13),"=q=")
	txt = txt.replace(chr(10),"")
	file.write(txt)
	print("====================>PAGE",x)
	#// print(txt) 
file.close()

str = "this is string example....wow!!! this is really string"
print(str)
print(str.replace("is", "was"))
print(str.replace(" is ", " was ", 3))