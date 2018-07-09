import PyPDF2
pdfFileObj = open('../_documments/1691-3388-1-SM.pdf','rb')     #'rb' for read binary mode
pdfReader = PyPDF2.PdfFileReader(pdfFileObj)
pdfReader.numPages
pageObj = pdfReader.getPage(9)          #'9' is the page number
pageObj.extractText()