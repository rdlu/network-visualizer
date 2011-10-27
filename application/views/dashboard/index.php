<?php var_dump($medicoes); ?>
<table id="floatbar" class="tablesorter">    
    <thead class="">
        <tr>
            <th>Sonda</th>
            <th>Localização</th>
            <th>Última atualização</th>
            <th>Throughput TCP</th>
            <th>Throughput</th>
            <th>Rtt</th>
            <th>Jitter</th>
            <th>Loss</th>
            <th>Mos</th>
            <th>Owd</th>
            <th>Pom</th>
        </tr>
    </thead>
    <tbody>       
        <?php foreach($medicoes as $medicao): ?>
        <tr>
            <td><?php echo $medicao['destination']['name']; ?></td>
            <td><?php echo $medicao['destination']['city']; ?></td>
            <td><?php echo (date('d/m/Y - H:i:s', $medicao['destination']['updated'])); ?></td>
            <td><?php echo $medicao['results']["full-throughput_tcp"]["DSAvg"].' - '.$medicao['results']["full-throughput_tcp"]["SDAvg"]; ?></td>
            <td><?php echo $medicao['results']["full-throughput"]["DSAvg"].' - '.$medicao['results']["full-throughput"]["SDAvg"]; ?></td>
            <td><?php echo $medicao['results']["full-rtt"]["DSAvg"]; ?></td>
            <td><?php echo $medicao['results']["full-jitter"]["DSAvg"].' - '.$medicao['results']["full-jitter"]["SDAvg"]; ?></td>
            <td><?php echo $medicao['results']["full-loss"]["DSAvg"].' - '.$medicao['results']["full-loss"]["SDAvg"]; ?></td>
            <td><?php echo $medicao['results']["full-mos"]["DSAvg"].' - '.$medicao['results']["full-mos"]["SDAvg"]; ?></td>
            <td><?php echo $medicao['results']["full-owd"]["DSAvg"].' - '.$medicao['results']["full-owd"]["SDAvg"]; ?></td>
            <td><?php echo $medicao['results']["full-pom"]["DSAvg"].' - '.$medicao['results']["full-pom"]["SDAvg"]; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

