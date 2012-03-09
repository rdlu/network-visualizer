<div id="opt">
    <a id="filtro_btn" href="#">
        <span class="button2">
            Filtros
       </span>
    </a>
    <a href="<?php echo url::site('winagent', 'http') ?>">
        <span class="button2">
            Mostrar todas medições
       </span>
    </a>
</div>

<table id="entityList" class="tablesorter" style="text-align: center">
        <thead>
            <tr>
                <th>Usuário</th>
                <th>Perfil</th>
                <th>Cell Id / LAC</th>
                <th>Data</th>
                <th>Rtt (ms)</th>
                <th>TP TCP <br />down / up (Mbps)</th>
                <th>TP <br />down / up (Mbps)</th>                
                <th>Loss <br />down / up (%)</th>
                <th>Jitter <br />down / up (ms)</th>
                <th>POM <br />down / up (%)</th>                               
                <th>Modem</th>
                <th>Força do sinal</th>
                <th>Modo de seleção <br />da rede</th>
                <th>Tecnologia de Conexão</th>
                <th>Taxa de erros (%)</th>
                <th>Mtu (bytes)</th>
                <th>Atraso DNS (ms)</th>
                <th>Rota</th>
            </tr>
        </thead>
        <tbody>            
            <?php foreach($medicoes as $medicao): ?>
                <tr >
                    <td><?php echo $medicao->username; ?></td>
                    <td><?php echo $medicao->perfil; ?></td>
                    <td><?php
                            if(!empty($medicao->cellid) && $medicao->cellid !== null && $medicao->cellid != 0)
                                echo $medicao->cellid;
                            else
                                echo ("-");
                        ?>
                        <?php
                            //puxadinho na tabela para incluir condicionalmente o lac
                            if(!empty($medicao->lac) && $medicao->lac !== null && $medicao->lac != 0){
                                echo " / $medicao->lac";
                            }
                            else { echo " / -";}
                        ?>
                    </td>
                    <td><?php echo date('d/m/Y <br/> H:i:s', $medicao->timestamp); ?></td>
                    <td><?php printf('%.2f', $medicao->rtt*1000); ?></td>
                    <td><?php printf('%.2f / %.2f', $medicao->throughputtcp_down/1000000, $medicao->throughputtcp_up/1000000); ?></td>
                    <td><?php printf('%.2f / %.2f', $medicao->throughput_down/1000000, $medicao->throughput_up/1000000); ?></td>                    
                    <td><?php echo $medicao->loss_down.'%'. ' / '.$medicao->loss_up.'%'; ?></td>
                    <td><?php printf('%.2f / %.2f', $medicao->jitter_down*1000, $medicao->jitter_up*1000); ?></td>
                    <td><?php echo $medicao->pom_down.'%'.' / '.$medicao->pom_up.'%'; ?></td>
                    <td style="white-space: nowrap"><?php echo ($medicao->modem? $medicao->modem : "-"); ?></td>
                    <td><?php echo ($medicao->forcaSinal? $medicao->forcaSinal : "-"); ?></td>
                    <td><?php echo ($medicao->modoSelecaoRede? $medicao->modoSelecaoRede : "-"); ?></td>
                    <td><?php echo ($medicao->tecnoConexao? $medicao->tecnoConexao : "-"); ?></td>                    
                    <td><?php echo ($medicao->taxaErros? $medicao->taxaErros : "-"); ?></td>
                    <td><?php echo ($medicao->mtu? $medicao->mtu : "-"); ?></td>
                    <td><?php echo ($medicao->atrasoDNS? $medicao->atrasoDNS : "-"); ?></td>
                    <td id="<?php echo $medicao->id ?>" class="rota" title="Clique para ver a rota completa"><?php if(strlen($medicao->rota) > 10)echo(substr($medicao->rota, 0, 22))." ..."; else echo $medicao->rota; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


<div>
    <?php //echo "<p>n medicoes: ".$total_medicoes."</p>"; ?>
    <?php //echo "<p>results_per_page: ".$results_per_page."</p>"; ?>
    <?php //echo "<p>page: ".$page."</p>"; ?>
</div>

<?php foreach($medicoes as $medicao): ?>
    <?php if($medicao->rota != '-' && (!empty($medicao->rota))) : ?>
        <div id="<?php echo "rota_".$medicao->id ?>" class="dialog" title="Rota" >
            <?php echo $medicao->rota; ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<div id="dialog" title="Filtros">
    <div id="tabs">
            <ul>
                    <li><a href="#usuario">Usuário</a></li>
                    <li><a href="#data">Data</a></li>
                    <li><a href="#cell_id">Cell Id</a></li>
            </ul>
            <div id="usuario">
                <form action="<?php echo url::site('winagent', 'http');  ?>" method="get" enctype="application/x-www-form-urlencoded">
                    <h2>Nome do usuário</h2><br />
                    <input type="hidden" name="filter" value="username" />
                    <input type="text" id="username" name="q"/><br />
                    <input type="submit" value="Enviar" class="button" />
                </form>
            </div>
            <div id="data">
                <form action="<?php echo url::site('winagent', 'http');  ?>" method="get" enctype="application/x-www-form-urlencoded">                <h2>Data da medição</h2><br />
                    <label style="width: 150px; display: inline-block" for="inicio">Data de início</label><input class="datepicker" type="text" style="width: 100px" id="inicio" name="inicio" /><br />
                    <label style="width: 150px; display: inline-block" for="fim">Data de término</label><input class="datepicker" type="text" style="width: 100px" id="fim" name="fim" /><br />
                    <input type="hidden" name="filter" value="timestamp" />
                    <input type="submit" value="Enviar" class="button" />
                </form>
            </div>
            <div id="cell_id">
                <form action="<?php echo url::site('winagent', 'http');  ?>" method="get" enctype="application/x-www-form-urlencoded">
                    <h2>Cell Id</h2><br />
                    <input type="hidden" name="filter" value="cellid" />
                    <input type="text" id="cellid" name="q"/><br />
                    <input type="submit" value="Enviar" class="button" />
                </form>
            </div>
    </div>
</div>