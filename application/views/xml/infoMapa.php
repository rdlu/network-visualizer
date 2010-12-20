<?="<?xml version='1.0' encoding='utf-8'?>" ?>
<xml>
    <?php foreach($entities as $k => $entity): ?>
    <entities>
        <sonda>
          <id><?=$entity->id?></id>
          <ip><?=$entity->ipaddress?></ip>
          <nome><?=$entity->name?></nome>
          <status>1</status>
          <latitude><?=$entity->latitude?></latitude>
          <longitude><?=$entity->longitude?></longitude>
          <agentes>
             <?php foreach($entity->destinations as $destination): ?>
             <med><?=$destination->id?></med>
             <?php endforeach; ?>
          </agentes>
        </sonda>
     </entities>
    <?php endforeach; ?>
</xml>