<?php if($current_user->has('roles', ORM::factory('role', array('name' => 'admin')))) : ?>
    <div id="account_options">
        <a href="<?php echo URL::site('account/register', 'http'); ?>">           
            <span class="button">
                <img src="<?php echo URL::base('http'); ?>images/actions/add.png" alt="" />
                Criar novo
            </span>
        </a>
    </div>
    <table id="entityList" class="tablesorter">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Logins</th>                
                <th>Nível de acesso</th>
                <th>Último Login</th>
                <th>Status</th>
                <th>&nbsp;</th>              
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $key => $user): ?>
                <tr>
                    <td><?php echo $user->username; ?></td>
                    <td><?php echo $user->email; ?></td>
                    <td><?php echo $user->logins; ?></td>
                    <td><?php
                            if($user->has('roles', ORM::factory('role', array('name' => 'admin')))){
                                echo 'administrador';
                            }
                            elseif ($user->has('roles', ORM::factory('role', array('name' => 'config')))){
                                echo 'configurador';
                            }
                            else echo 'visualizador';
                        ?>
                    </td>
                    <td><?php 
                            if($user->last_login != 0){echo date("(d/m/Y) H:i:s", $user->last_login);}
                            else echo "não logou";
                        ?>
                    </td>
                    <td><?php echo ($user->active == 1)? 'ativo' : 'inativo'; ?></td>
                    <td>
                        <a href='<?php echo URL::site('account/edit?id='.$user->id, 'http');?>'>Editar</a>
                        <?php if($user->username != 'admin') : ?>
                            <a href='<?php echo URL::site('account/delete?id='.$user->id, 'http'); ?>'>Excluir</a>
                            <a href='<?php echo URL::site('account/toogle?id='.$user->id, 'http');?>'><?php echo ($user->active == 1)? 'Desativar' : 'Ativar'; ?></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php endif ?>

<?php if(!$current_user->has('roles', ORM::factory('role', array('name' => 'admin')))) : ?>
     <table id="entityList" class="tablesorter">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Logins</th>
                <th>Nível de acesso</th>
                <th>Último Login</th>
                <th>Status</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
                <tr>
                    <td><?php echo $current_user->username; ?></td>
                    <td><?php echo $current_user->email; ?></td>
                    <td><?php echo $current_user->logins; ?></td>
                    <td><?php                            
                           if ($current_user->has('roles', ORM::factory('role', array('name' => 'config')))){
                                echo 'configurador';
                            }
                            else echo 'visualizador';
                        ?>
                    </td>
                    <td><?php
                            if($current_user->last_login != 0){echo date("(d/m/Y) H:i:s", $current_user->last_login);}
                            else echo "não logou";
                        ?>
                    </td>
                    <td><?php echo ($current_user->active == 1)? 'ativo' : 'inativo'; ?></td>
                    <td>
                        <a href='<?php echo URL::site('account/edit?id='.$current_user->id, 'http');?>'>Editar</a>
                    </td>
                </tr>
        </tbody>
    </table>
<?php endif ?>