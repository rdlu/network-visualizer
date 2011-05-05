<!-- MainMenu Template View -->
<ul id="mainMenu">
    <li id="titleMainMenu"><img src="<?=url::base()?>images/icon.png" alt="Logo" style="vertical-align:text-top;margin:0 5px;">NetMetric MoM</li>
<?php foreach($menus as $key1=>$level1): ?>
    <li id="mainMenu.<?=$key1?>"><?php if(isset($level1['href'])): ?><a href="<?=url::site($level1['href'])?>"><?=$level1['title']?></a><?php else: ?>
            <?=$level1['title']?>
        <?php endif ?></li>
<?php endforeach?>
</ul>
<!--End MainMenu -->