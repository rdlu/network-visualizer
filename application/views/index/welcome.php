<!-- Index Template View -->
   <!-- Esse é o conteúdo da aba início do MoM -->
   <div id="rightBar">

       <div id="rightBarMenu" class="contentBar">
         <a href="#">Criar</a>
         <a href="#">Deletar</a>
         <a href="#">Pesquisar</a>
       </div>
       <div id="sondaDestaque"></div>

       <div id="entities"></div>
       <div id="androidBanner" class="sondaDestaqueBox sondaDestaque" style="background-color: #ccc; padding: 5px; cursor: pointer;">
           <img src="<?=url::site('images/android.png')?>" style="margin: 5px;vertical-align: middle;" />
           Agentes Android
       </div>
   </div>
   <div id="leftBar">
       <div id="leftBarMenu" class="contentBar">
           <!--
           <span class="leftBarBtn"><a href="#">Diagrama</a></span>
           <span class="leftBarBtn"><a href="#">Mapa</a></span>
           -->
       </div>
       <div id="leftBarContent">
           <!-- TAB COM A VISÃO por GOOGLE/MAPS -->

           <div id="mapa">
               <!--conteúdo iniciado por AJAX/JS -->
           </div>
           <!-- TAB COM A VISÃO POR DIAGRAMA -->
           <div id="diagrama">
                <!--conteúdo iniciado por AJAX/JS -->
           </div>
       </div>
   </div>

       <div id="androidResponse" style="display: none;"></div>

<!-- End Index -->
<script type="text/javascript">
    jQuery("#androidBanner").click(function(evt) {
        jQuery.get("<?=url::site('entities/androidList')?>", function(data) {
            console.log([data]);
            jQuery("#androidResponse").html(data);
            jQuery("#androidResponse").dialog({minWidth:780,title:"Status dos Agentes Android"});
        });
    });
</script>