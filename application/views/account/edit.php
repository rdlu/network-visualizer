<?php $current_user = Auth::instance()->get_user(); ?>

<?php if($current_user->has('roles', ORM::factory('role', array('name' => 'admin')))) : ?>
    <?php echo form::open('account/edit', array('id' => 'validate', 'class' => 'form_center')) ?>
        <?php echo form::hidden('id', $user->id) ?>
    <input type="hidden" name="action" value="save" />
      <table>
        <tr>
          <td><?php echo form::label('email','E-mail') ?></td>
          <td>:</td>
          <td><?php echo form::input('email', $user->email,array('id' => 'email', 'class' => 'required email')) ?>
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
          <td><?php echo form::password('password_confirm','',array('id' => 'password_confirm')) ?></td>
        </tr>
        <tr>
            <td>
                <?php echo Form::radio('privilege', 'administrador', ($user->has('roles', ORM::factory('role', array('name' => 'admin'))))); ?>
                <span>Administrador</span>
                <?php echo Form::radio('privilege', 'configurador', ($user->has('roles', ORM::factory('role', array('name' => 'config')))) && !($user->has('roles', ORM::factory('role', array('name' => 'admin'))))); ?>
                <span>Configurador</span>
                <?php echo Form::radio('privilege', 'visualizador', ($user->has('roles', ORM::factory('role', array('name' => 'login')))) && !($user->has('roles', ORM::factory('role', array('name' => 'config')))) && !($user->has('roles', ORM::factory('role', array('name' => 'admin'))))); ?>
                <span>Visualizador</span>
            </td>
        </tr>

      </table>

      <div style="text-align:center">
        <?php echo form::submit('submit','Salvar', array('id' => 'submit', 'class' => 'submit')) ?>
      </div>
    <?php echo form::close() ?>

    <?php if (isset($errors)): ?>
            <ul class="errors">
                    <?php foreach($errors as $error): ?>
                    <li class="<?=$error['class']?>"><?=$error['message']?></li>
                    <?php endforeach; ?>
            </ul><br />
    <?php endif; ?>
<?php endif; ?>

<?php if(!$current_user->has('roles', ORM::factory('role', array('name' => 'admin')))) : ?>
    <?php echo form::open('account/edit', array('id' => 'validate', 'class' => 'form_center')) ?>
        <?php echo form::hidden('id', $user->id) ?>
    <input type="hidden" name="action" value="save" />
      <table>
        <tr>
          <td><?php echo form::label('email','E-mail') ?></td>
          <td>:</td>
          <td><?php echo form::input('email', $user->email,array('id' => 'email', 'class' => 'required email')) ?>
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
          <td><?php echo form::password('password_confirm','',array('id' => 'password_confirm')) ?></td>
        </tr>
      </table>

      <div style="text-align:center">
        <?php echo form::submit('submit','Salvar', array('id' => 'submit', 'class' => 'submit')) ?>
      </div>
    <?php echo form::close() ?>

    <?php if (isset($errors)): ?>
            <ul class="errors">
                    <?php foreach($errors as $error): ?>
                    <li class="<?=$error['class']?>"><?=$error['message']?></li>
                    <?php endforeach; ?>
            </ul><br />
    <?php endif; ?>
<?php endif; ?>