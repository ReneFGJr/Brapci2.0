<style>
    .parallax_background_1 {
        /* The image used */
        background-image: url("http://[::1]/projeto/Thesa/img/background/background_4.jpg");
    }

    .parallax_background_2 {
        /* The image used */
        background-image: url("http://[::1]/projeto/Thesa/img/background/background_2.jpg");
    }
    .parallax {

        /* Create the parallax scrolling effect */
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
    }
</style>
<div class="container">
                <?php
                require ("form.php");
            ?>
</div>

<div class="container-fluid parallax parallax_background_2">
     	<div class="row" style="padding: 10px;">
            <div class="col-md-6" style="border-radius: 20px; background-color: #ffffff; opacity: 0.7; padding: 20px;">
            <?php
            require ("form.php");
            ?>
                
            </div>
            <div class="col-md-1"></div>
    		<div class="col-md-2" style="border-radius: 20px; background-color: #ffffff; opacity: 0.7; padding: 20px;">
    			<span class="big"><b>PUBLICAÇÕES</b></span>
    			<br>
    			<br>
    			<span class="middle"><b><span class="big"><a href="http://www.brapci.inf.br/index.php/journal">57</b> Revistas Científicas</a></span></br>
    			<br/>
    			<b>19.179</b> Trabalhos em Revistas Científicas</br><b>2.592</b> Trabalhos em Eventos</br><b>2</b> Livros</br><b>1</b> Teses</br>
    			</span>
    		</div>
    		<div class="col-md-1"></div>
    		<div class="col-md-2"  style="border-radius: 20px; background-color: #ffffff; opacity: 0.7; padding: 20px;">
    			<span class="big"><b>AUTORIDADES</b></span>
    			<br>
    			<br>
    			<span class="middle"> <span class="big"><b>16.601</b> Autores</span></br><b>2.043</b> Remissivas de Autores</br>
    				<br>
    				<span class="big"><b>18.458</b> Palavras-chave em Inglês</span></br><b>12</b> Remissivas em Inglês</br>
    				<br>
    				<span class="big"><b>3.834</b> Palavras-chave em Espanhol</span></br><b>1</b> Remissivas em Espanhol</br>
    				<br>
    				<span class="big"><b>107</b> Palavras-chave em Francês</span></br><span class="big"><b>26.927</b> Palavras-chave em Português</span></br><b>52</b> Remissivas em Português</br>
    				<br>
    				1 Tesauro
    				<br>
    			</span>
    		</div>
</div>    
</div>

<!--- SPOT #1 --->

<!-- Container element -->
<div style="background-color:#ffffff; padding: 20px;">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>Apresentação do Thesa</h3>
                <p>
                    O Thesa foi desenvolvido objetivando disponibilizar um instrumento para os estudantes de graduação de biblioteconomia na disciplina de Linguagens Documentárias para a elaboração de tesauros, de modo que possibilite reduzir o trabalho operacional e dar maior atenção ao trabalho de desenvolvimento cognitivo e conceitual referente a modelagem do domínio.
                </p>
                <p>
                    Como norteador do aplicativo, baseou-se nas normas ISO e NISO vigentes, de forma a compatibilizar suas diretrizes com os requisitos semânticos prementes nas novas demandas dos SOCs. Com base na literatura disponível, nas normas de construção de tesauros da ISO e NISO foram identificados os elementos necessários para o desenvolvimento do protótipo, principalmente no que tange ao levantamento das propriedades de ligação entre os conceitos.
                </p>
                <p>
                    A estrutura do Thesa é baseada na concepção das relações entre os conceitos, partido do pressuposto que um conceito pode ser representado por um termo, uma imagem, um som, um link ou qualquer outra forma que possa ser explicitada. Nessa abordagem, o conceito é perene, enquanto a sua representação pode variar conforme o contexto histórico ou social, sendo definida uma forma preferencial, e inúmeras formas alternativas e ocultas.
                </p>
                <p>
                    Como citar: GABRIEL JUNIOR, R. F.; LAIPELT, R. C. Thesa: ferramenta para construção de tesauro semântico aplicado interoperável. <b>Revista P2P & Inovação</b>, Rio de Janeiro, v. 3, n. 2, p.124-145, Mar./Set. 2017.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="parallax parallax_background_2"  style="padding: 100px;">
    <div class="container">
        <div class="row">
            <div class="col-md-3 text-center box">
                <span class="small"><b><?php echo msg("thesauros"); ?></b></span>
                <br>
                <span class="big"><b>ZZ</b></span>
            </div>
            <div class="col-md-3 text-center box">
                <span class="small"><b>ZZ</b></span>
                <br>
                <span class="big"><b>ZZ</b></span>
            </div>
            <div class="col-md-3 text-center box">
                <span class="small"><b>ZZ</b></span>
                <br>
                <span class="big"><b>ZZ</b></span>
            </div>
        </div>
    </div>
</div>

<!-- Container element -->
<div style="background-color:#ffffff; padding: 20px;">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h3>Sobre o Thesa</h3>
                <p>
                    O Thesa foi desenvolvido inicialmente como um protótipo utilizando a linguagem php e banco de dados MySql, de forma a possibilitar o compartilhamento e desenvolvimento colaborativo da ferramenta.
                </p>

                <p>
                    O software funciona em ambiente Web e pode ser baixado gratuitamente, podendo ser utilizado para fins didáticos em disciplinas dos cursos de graduação e pós-graduação ou para uso profissional. O Thesa foi desenvolvido com o princípio de multi-idioma, podendo ser traduzido para qualquer idioma, entretanto sua versão de teste está somente em português, as traduções vão depender de se estabelecerem convênios com instituições nativas de outros idiomas, que demonstrarem interesse pelo uso do software.
                </p>

                <p>
                    O Thesa utiliza uma concepção de múltiplos tesauros, ou múltiplos esquemas, ou seja, o usuário pode criar um número ilimitado de tesauros em diferentes áreas do conhecimento, os usuários/elaboradores desses tesauros, podem deixá-los para uso público ou privado, possibilitando o acesso de outros usuários. No Thesa partiu-se da concepção de URI, empregada pelo SKOS e sistemas baseados na Web Semântica, ou seja, cada conceito é associado a um endereço permanente na Internet e a um identificador único do conceito, e esse representado por termos por meio de propriedades.
                </p>
            </div>
        </div>
    </div>
</div>