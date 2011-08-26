<?php echo form::open('account/register') ?>
  <table>
    <tr>
      <td><?php echo form::label('email','E-mail') ?></td>
      <td>:</td>
      <td><?php echo form::input('email','',array('id' => 'email')) ?>
    </tr>
    <tr>
      <td><?php echo form::label('username','Username') ?></td>
      <td>:</td>
      <td><?php echo form::input('username','',array('id' => 'username')) ?>
    </tr>
    <tr>
      <td><?php echo form::label('password','Password') ?></td>
      <td>:</td>
      <td><?php echo form::password('password','',array('id' => 'password')) ?>
    </tr>
    <tr>
      <td><?php echo form::label('password_confirm','Password Confirm') ?></td>
      <td>:</td>
      <td><?php echo form::password('password_confirm','',array('id' => 'password_confirm')) ?>
    </tr>
    <tr>
        <td>
            <?php echo Form::radio('privilege', 'administrador', TRUE); ?>
            <span>Administrador</span>
            <?php echo Form::radio('privilege', 'configurador', FALSE); ?>
            <span>Configurador</span>
            <?php echo Form::radio('privilege', 'visualizador', FALSE); ?>
            <span>Visualizador</span>
        </td>
    </tr>
  </table>
  <div style="text-align:center">
    <?php echo form::submit('submit','Register') ?>
  </div>
<?php echo form::close() ?>