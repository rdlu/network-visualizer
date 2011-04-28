<script type="text/javascript">
    $(document).ready(function(){
        $('#header').remove();
        $('#footer').remove();
        $('#synth_select').remove();
        SYNTH.newSection( <?=$sondaOrigemId?>);
        SYNTH.newBoxes(<?=$sondaOrigemId?>);
        SYNTH.hack(<?=$sondaOrigemId?>); //hack com borda (requeijão? pode ser?)
    });
</script>

<!-- template para cada seção -->


<div id="synth_template_secao" class="template"> <!-- id = secao_id  da sonda origem -->
    <h2 class="synth_title">
        <span class="nome"></span>
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