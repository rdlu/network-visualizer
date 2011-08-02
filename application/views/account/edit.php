<?php echo form::open('account/savechanges', array('class' => 'form_center', 'method' => 'post')) ?>
    <?php echo form::hidden('id', $user->id) ?>
  <table>
    <tr>
      <td><?php echo form::label('email','E-mail') ?></td>
      <td>:</td>
      <td><?php echo form::input('email', $user->email,array('id' => 'email')) ?>
    </tr>
    <tr>
      <td><?php echo form::label('username','Username') ?></td>
      <td>:</td>
      <td><?php echo form::input('username', $user->username,array('id' => 'username')) ?>
    </tr>
    <tr>
      <td><?php echo form::label('password','Nova Senha') ?></td>
      <td>:</td>
      <td><?php echo form::password('password','',array('id' => 'password')) ?>
    </tr>
    <tr>
      <td><?php echo form::label('password_confirm','Digite novamente') ?></td>
      <td>:</td>
      <td><?php echo form::password('password_confirm','',array('id' => 'password_confirm')) ?>
    </tr>
  </table>
  <div style="text-align:center">
    <?php echo form::submit('submit','Salvar') ?>
  </div>
<?php echo form::close() ?>