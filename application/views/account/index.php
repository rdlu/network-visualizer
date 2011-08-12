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
                <th>Último Login</th>
                <th>&nbsp;</th>              
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $key => $user): ?>
                <tr>
                    <td><?php echo $user->username; ?></td>
                    <td><?php echo $user->email; ?></td>
                    <td><?php echo $user->logins; ?></td>
                    <td><?php echo date("(d/m/y) H:i:s", $user->last_login); ?></td>
                    <td><a href='<?php echo URL::site('account/edit?id='.$user->id, 'http');?>'><img src="" alt="editar"/></a>
                        <a href='<?php echo URL::site('account/delete?id='.$user->id, 'http'); ?>'><img src="" alt="excluir"/></a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>