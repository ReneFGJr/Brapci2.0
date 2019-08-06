import requests

def web(url):
 req = requests.get(url)
 if req.status_code == 200:
  print('Requisição bem sucedida!')
  content = req.content
  content = content.decode("uft8")
  
  f = open("guru99.txt","w+")
  f.write(content)
  f.close()