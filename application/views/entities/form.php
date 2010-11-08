<h2><a href="<?=url::base().Request::instance()->controller?>">Voltar</a></h2>
<?php if($errors): ?>
<ul>
    <?php foreach($errors as $error): ?>
    <li><?=$error?></li>
    <?php endforeach; ?>
</ul>
<?php endif ?>

<?=Form::open(Request::instance()->controller.'/'.Request::instance()->action.'/'.Request::instance()->param('id',0),array('id'=>'newEntity'))?>
<dl>
<?php foreach ($entity->inputs() as $label => $input): ?>
    <dt><?php echo $label ?></dt>
    <dd><?php echo $input ?></dd>
<?php endforeach ?>
    <dd>&nbsp;</dd>
    <dt><?=Form::submit('submit_entity','OK')?></dt>
</dl>
<?=Form::close()?>
<script type="text/javascript">
    function checkIp() {
        $.ajax({
           type: "POST",
           url: "<?=url::base().'tools/check/'?>",
           data: "ip="+$('input[name$="ipaddress"]').val(),
           dataType: 'json',
           success: function(data) {
               console.warn(data.data.version);
               $('span#ipCheck').remove();
               if(data.data.version)
                    $('input[name$="ipaddress"]').after('<span id="ipCheck" class="input sucess">Host contactado com sucesso. Versão: '+data.data.version+'</span>');
               else
                    $('input[name$="ipaddress"]').after('<span id="ipCheck" class="input error">Não houve resposta do host no IP indicado. Cheque se a instalação foi feita corretamente.</span>');
           },
           error: function(status,msg,error) {
               console.warn('CheckIP Failed '+msg);
           }
         });
    }

    $('input[name$="ipaddress"]').blur(function() {
        console.warn('WEEEE!');
        checkIp();
    });
</script>