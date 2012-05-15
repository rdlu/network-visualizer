<? foreach($messages as $sourceID => $msgList): ?>
    <? foreach($msgList as $message): ?>
        <?=$message?><br />
    <? endforeach; ?>
    <?=(count($msgList))?"":"Nenhum erro encontrado nos arquivos RRD para a origem ".$sources[$sourceID]->name?>
<? endforeach; ?>
