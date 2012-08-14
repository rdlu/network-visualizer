<?php foreach (Kohana::$config->load('menus.main.admin.submenu') as $key2 => $level2): ?>
<a href="<?=url::site($level2['href'])?>" class="boardMenu" id="boardMenu.<?=$key2?>"><img
    src="<?=url::site('images/boardMenu/' . $key2 . '.png')?>"
    alt="Administrar <?=$level2['title']?>"/><?=$level2['title']?></a>
<? endforeach ?>