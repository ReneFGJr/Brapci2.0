import urllib.request
from urllib.parse import quote
from bs4 import BeautifulSoup

key = input("Informe a palavra-chave\n")
safe_key = quote(key)
url = 'http://clients1.google.com/complete/search?hl=pt-br&output=toolbar&q={}'.format(safe_key)
openurl = urllib.request.urlopen(url)
mackup = openurl.read()
soup = BeautifulSoup(mackup, 'html.parser')
for link in soup.find_all('suggestion'):
	print(link.get('data'))