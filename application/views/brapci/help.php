<div class="container">
	<div class="row">
		<div class="col-md-6">
			<h1>Busca</h1>
			<ul>
				<li>
					<h4>Busca Simples</h4>
					<p>
						Termo exato: <b>Nome do termo</b>
					</p>
					<code>
						ex: Bibliometria
					</code>
					<br/>
					<br/>
				</li>
				
                <li>
                    <h4>Busca por Termo Composto</h4>
                    <p>
                        Termo exato: <b>"Nome do termo"</b> (entre aspas)
                    </p>
                    <code>
                        ex: "Mineração de texto"
                    </code>
                    <br/>
                    <br/>
                </li>				
				
                <li>
                    <h4>Busca pela variações de termos</h4>
                    <p>
                        Termo e sua variação: <b>Nome do termo com asterisco "*"</b>
                    </p>
                    <code>
                        ex: Bibliomet*
                        <br>ex: *metria
                    </code>
                    <br/>
                    <br/>
                </li>
                <hr>
                <li>
                    <h4>Busca pela variação de uma letra dos termos</h4>
                    <p>
                        Termos com início e fim: <b>Nome do termo com asterisco "?"</b>
                    </p>
                    <code>
                        ex: Bibliotecári?
                        <br> Indexad?r -> (Busca Indexador ou Indexadora)
                    </code>
                    <br/>
                    <br/>
                </li>                				
                <li>
                    <h4>Busca pela variação do termo</h4>
                    <p>
                        Termos com início e fim: <b>Nome do termo com asterisco "*"</b>
                    </p>
                    <code>
                        ex: Bib*ca
                        <br>Retorna tudo que começe com BIB e termine com CA
                    </code>
                    <br/>
                    <br/>
                </li>                				
				<li>
					<h4>Busca Composta</h4>			
                    <p>
                        Na busca composta o sistema insere automaticamento o elemento boleano OR entre os termos, recuperando apenas os registros que tenham a ocorrencias.
                    </p>
                    <code>
                        ex: Bibliometria Citação
                        <br>Forma de busca do sistema: Bibliometria OR Citação
                    </code>					
				<hr>
					<p>
						Para delimitar a busca, onde ocorra a ocorrencia dos termos é necessário a inclusão do elemento boleano AND entre os termos, recuperando apenas os registros que tenham as ocorrencias indicadas.
					</p>
					<code>
						ex: Bibliometria Citação
						<br>Forma de busca do sistema: Bibliometria AND Citação
					</code>                    
				<hr>
					<p>
						Em buscas com mais de um termo, pode-se atribuir pesos diferentes para cada um deles, definindo maior "prioridade para um deles", para isso, atribuia a inficação "^" e o peso atribuído
					</p>
					<code>
						ex: Biblioteca AND Universit*^10
						<br>O sistema recupera todas os termos biblioteca e universit*, porém atribui um peso maior para a "universit*", colocando com maior relevância nos resultados.
					</code>                    
				</li>
			</ul>
		</div>
	</div>
</div>
