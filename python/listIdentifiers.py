#!/usr/local/bin/python
# -*- coding: latin-1 -*-
# Web scraping
import urllib2
import os, sys, string
import xml.dom.minidom
import mysql.connector

# DataBase
mydb = mysql.connector.connect(
  host="localhost",
  user="root",
  passwd="root"
)

print(mydb)

url = 'http://www.scielo.br/oai/scielo-oai.php?verb=ListIdentifiers&metadataPrefix=oai_dc_openaire&set=0103-3786&resumptionToken=HR__S0103-37862003000200008:0103-3786:::oai_dc'
# url = 'http://www.ufrgs.br/ufrgs/inicial'
print(url)

# Carrega o website e retorna o html para a variável 'page'
page = urllib2.urlopen(url)
xml = page.read()

# XML
from xml.dom.minidom import parse, parseString

document = """\
<slideshow>
<title>Demo slideshow</title>
<slide><title>Slide title</title>
<point>This is a demo</point>
<point>Of a program for processing slides</point>
</slide>

<slide><title>Another demo slide</title>
<point>It is important</point>
<point>To have more than</point>
<point>one slide</point>
</slide>
</slideshow>
"""



if __name__ == "__main__":
    #x = xml.dom.minidom.parseString(xml)
    dom = xml.dom.minidom.parseString(document)
    nos = dom.documentElement
    print "|-> %s" % nos.nodeName
    filhos1 = [no for no in nos.childNodes if no.nodeType == \
                  x.ELEMENT_NODE]
    for pai in filhos1:
        print "|--> %s" % pai.nodeName
        filhos2 = [no for no in pai.childNodes if no.nodeType == \
                      x.ELEMENT_NODE]
        for filho in filhos2:
            print "|---> %s" % filho.nodeName
            print "|-----> %s" % filho.getAttribute('atributo1')
            print "|-----> %s" % filho.getAttribute('atributo2')
