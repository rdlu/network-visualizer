<!-- MainMenu Template View -->
<ul id="mainMenu">
    <li id="titleMainMenu">NetMetric MoM</li>
<?php foreach(Kohana::config('menus.main') as $key1=>$level1): ?>
    <li id="mainMenu.<?=$key1?>"><?php if(isset($level1['href'])): ?><a href="<?=url::site($level1['href'])?>"><?=$level1['title']?></a><?php else: ?>
            <?=$level1['title']?>
        <?php endif ?></li>
<?php endforeach?>
</ul>
<!--End MainMenu -->