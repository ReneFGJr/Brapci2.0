#pip install wget
import wget
import requests
import codecs
import os

def fpdf(id):
 url = 'http://revistas.inpi.gov.br/pdf/Patentes'+id+'.pdf'
 filename = wget.download(url)
 src = 'Patentes'+id+'.pdf'
 file = 'Patent-0'+id+'.pdf'
 os.rename(src, file)

def fget(id):
 url = 'http://revistas.inpi.gov.br/txt/P'+id+'.zip'
 filename = wget.download(url)
 src = 'P'+id+'.zip'
 file = 'Patent-0'+id+'.zip'
 os.rename(src, file)

def web(url,file):
 req = requests.get(url)
 if req.status_code == 200:
  print('Requisição bem sucedida!')
  content = req.content
  content = unicode(content,errors='replace')
  content = content.decode("utf-8")

  f = open(file,"w+")
  f.write(content)
  f.close()
 
web('http://revistas.inpi.gov.br/txt/P2535.zip','Patent-2545.zip')