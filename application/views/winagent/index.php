<table id="entityList" class="tablesorter">
        <thead>
            <tr>
                <th>Usuário</th>
                <th>Data</th>
                <th>Cell Id</th>
                <th>Loss down / up (%)</th>
                <th>Jitter down / up (ms)</th>
                <th>POM down / up (%)</th>
                <th>TP down / up (Mbps)</th>
                <th>Rtt (ms)</th>
                <th>TP TCP down / up (Mbps)</th>
            </tr>
        </thead>
        <tbody>            
            <?php foreach($medicoes as $medicao): ?>
                <tr>
                    <td><?php echo $medicao->username; ?></td>
                    <td><?php echo date('d/m/Y - H:i:s', $medicao->timestamp); ?></td>
                    <td><?php echo $medicao->cellid; ?></td>
                    <td><?php echo $medicao->loss_down.'%'. ' / '.$medicao->loss_up.'%'; ?></td>
                    <td><?php printf('%.2f / %.2f', $medicao->jitter_down*1000, $medicao->jitter_up*1000); ?></td>
                    <td><?php echo $medicao->pom_down.'%'.' / '.$medicao->pom_up.'%'; ?></td>
                    <td><?php printf('%.2f / %.2f', $medicao->throughput_down/1000000, $medicao->throughput_up/1000000); ?></td>
                    <td><?php printf('%.2f', $medicao->rtt*1000); ?></td>
                    <td><?php printf('%.2f / %.2f', $medicao->throughputtcp_down/1000000, $medicao->throughputtcp_up/1000000); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<div id="pagination" style="text-align: center">
    <span>
        <a href="<?php if($page > 1){echo url::site('winagent', 'http')."?page=".($page -1);} ?>"> << &nbsp;</a>
        <?php echo $page."/".ceil($total_medicoes/$results_per_page); ?>
        <a href="
            <?php
                    if( $total_medicoes > ($results_per_page * $page)){echo url::site('winagent', 'http')."?page=".($page +1);}else {echo "";} ?>"> &nbsp; >> &nbsp;</a>
    </span>    
</div>

<div>
    <?php //echo "<p>n medicoes: ".$total_medicoes."</p>"; ?>
    <?php //echo "<p>results_per_page: ".$results_per_page."</p>"; ?>
    <?php //echo "<p>page: ".$page."</p>"; ?>
</div>
