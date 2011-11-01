<?php //var_dump($medicoes); ?>
<?php $state = null; ?>

<table id="floatbar" class="tablesorter">    
    <thead class="">
        <tr>
            <th style="width: 20px"></th>
            <th>Sonda</th>            
            <th>Localização</th>
            <th>Última atualização</th>
            <th>Throughput TCP <br />up / down (Mbps)</th>
            <th>Throughput <br />up / down (Mbps)</th>
            <th>Rtt (ms)</th>
            <th>Jitter <br />up / down (ms)</th>
            <th>Loss <br />up / down (%)</th>
            <th>Mos <br />up / down</th>
            <th>Owd <br />up / down (ms)</th>
            <th>Pom <br />up / down (%)</th>
        </tr>
    </thead>
    <tbody>       
        <?php foreach($medicoes as $medicao): ?>
        

        <tr>
            <?php if($medicao['destination']['status'] == 1) echo '<td title="A sonda está operando normalmente" style="background-color: #6DC23F; border: 1px"></td>';
                  elseif($medicao['destination']['status'] == 0) echo '<td title="A sonda está inativa" style="background-color: #BBB; border: 1px"></td>';
                  elseif($medicao['destination']['status'] == 2) echo '<td title="A sonda apresenta problemas" style="background-color: #F8DD6A; border: 1px"></td>';
                  elseif($medicao['destination']['status'] == 3) echo '<td title="A sonda está fora do ar" style="background-color: #D3564F; border: 1px"></td>';
            ?>
            

            <td><?php   echo
                (
                 '<h2>'.$medicao['destination']['name'].'</h2><i>'
                .'<p style="text-align: center; font-size: 80%">Versão: '.$medicao['destination']['system']['version']
                .' '.$medicao['destination']['system']['nmVersion']
                //.'<p style="text-align: left">ddnsVersion: '.$medicao['destination']['system']['ddnsVersion'].'</p>'
                //.'<p style="text-align: left">Versão Gparc: '.$medicao['destination']['system']['gparcVersion'].'</p>'
                .' '.( (strtolower($medicao['destination']['system']['modemInfo']) == 'none')? '' : $medicao['destination']['system']['modemInfo'] )
                .' '.strstr($medicao['destination']['system']['osVersion'], '-', true).'</p></i>'
                );
                ?>
            </td>            

            <td><?php echo $medicao['destination']['city'].' - '.$medicao['destination']['state']; ?></td>
            <td style="width: 80px"><?php echo (date('d/m/Y H:i:s', $medicao['destination']['updated'])); ?></td>
            <td><?php echo Convert::format($medicao['results']["full-throughput_tcp"]["DSAvg"], 'throughput').' / '.Convert::format($medicao['results']["full-throughput_tcp"]["SDAvg"], 'throughput'); ?></td>
            <td><?php echo Convert::format($medicao['results']["full-throughput"]["DSAvg"], 'throughput').' / '.Convert::format($medicao['results']["full-throughput"]["SDAvg"], 'throughput'); ?></td>
            <td><?php echo Convert::format($medicao['results']["full-rtt"]["DSAvg"], 'rtt'); ?></td>
            <td><?php echo Convert::format($medicao['results']["full-jitter"]["DSAvg"], 'jitter' ).' / '.Convert::format($medicao['results']["full-jitter"]["SDAvg"], 'jitter' ); ?></td>
            <td><?php echo Convert::format($medicao['results']["full-loss"]["DSAvg"], 'loss' ).' / '.Convert::format($medicao['results']["full-loss"]["SDAvg"], 'loss'); ?></td>
            <td><?php echo Convert::format($medicao['results']["full-mos"]["DSAvg"], 'mos' ).' / '.Convert::format($medicao['results']["full-mos"]["SDAvg"], 'mos'); ?></td>
            <td><?php echo Convert::format($medicao['results']["full-owd"]["DSAvg"], 'owd').' / '.Convert::format($medicao['results']["full-owd"]["SDAvg"], 'owd'); ?></td>
            <td><?php echo Convert::format($medicao['results']["full-pom"]["DSAvg"], 'pom').' / '.Convert::format($medicao['results']["full-pom"]["SDAvg"], 'pom'); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

