<?php echo form::open('auth/register') ?>
  <table>
    <tr>
      <td><?php echo form::label('username','Usuário') ?></td>
      <td>:</td>
      <td><?php echo form::input('username','',array('id' => 'username')) ?>
    </tr>
    <tr>
      <td><?php echo form::label('password','Senha') ?></td>
      <td>:</td>
      <td><?php echo form::password('password','',array('id' => 'password')) ?>
    </tr>
  </table>
  <div style="text-align:center">
    <?php echo form::submit('submit','Entrar') ?>
  </div>
<?php echo form::close() ?>