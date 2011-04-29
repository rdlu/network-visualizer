<!--HTML DA SINTETIZAÇÃO-->

<!--incluir:  1) as tags para escolher a cidade origem -->
<!--          2)Template para medição e prováveis campos ocultos para dados ocultos: previnir default do form -->

<div id="synth_select">
    <form class="synth_form" action=""> <!-- action feita via javascript. Vide SYNTH.newSection -->
        <div>
            <span>Sonda de Origem</span>
            <select id="synth_dropdown" name="origem">
                <!-- povoado no oontroller synthesizing.php -->
                
                <?php 
                    if(Arr::is_array($resp)) //só
                        foreach($resp as $entity): ?>
                        <option id="<?="synth_opt_".$entity['id']; ?>" value="<?=$entity['id'];?>">
                           <?=$entity['name'] ." (".$entity['ipaddress'] .")"; ?>
                        </option>
                <?php endforeach; ?>
            </select>
            <span class="synth_menu">
                <a href="#" id="synth_select_add"> <!-- ADD -->
                    <img alt="Adiciona uma sonda para visualização" src="images/actions/add.png" />
                </a>
                <a href="#"> <!-- POP UP -->
                    <img alt="Abre as relações em outra janela" src="images/actions/application_cascade.png" />
                </a>
            </span>
        </div>
    </form>
</div>



<!-- template para cada seção -->


<div id="synth_template_secao" class="template"> <!-- id = secao_id  da sonda origem -->
    <h2 class="synth_title">
        <span class="nome"></span>
         <span id="" class="synth_menu2">
             <a href="#" class="synth_delete">
                 <img alt="Exclui a visualização dos resultados da sonda de origem" src="images/actions/delete.png" />
             </a>
                <a href="#" class="synth_popup"> <!-- POP UP -->
                    <img alt="Abre as relações em outra janela" src="images/actions/application_cascade.png" />
                </a>
         </span>
    </h2>
    <div class="synth_sondas_dest"><!-- AQUI SÃO INCLUÍDAS AS NOVAS BOXES --></div>
</div>

<!-- Esse é o template -->
 <div id="synth_template_box" class="template">
        <div class="synth_destino">
            <span class="nome"></span>
        </div>

        <div id="separator"></div>

        <div class="synth_bar_bg">
            <div class="synth_bar_inner rtt_bar">
                <span class="rtt">RTT</span>
            </div>
        </div>
        <div class="synth_bar_bg">
            <div class="synth_bar_inner loss_bar">
                <span class="loss">LOSS</span>
            </div>
        </div>

        <div class="synth_bar_bg">
            <div class="synth_bar_inner tpTCP_bar">
                <span class="tpTCP">tpTCP</span>
            </div>
        </div>
        <div class="synth_bar_bg">
            <div class="synth_bar_inner tpUDP_bar">
                <span class="tpUDP">tpUDP</span>
            </div>
        </div>
        
        <span>&nbsp;</span>
 </div>