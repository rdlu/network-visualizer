<div id="tabs">
    <div id="metricsAccordion" class="accordion">
        <?php foreach($metrics as $order => $metric): ?>
        <h3><a href="#"><?=$metric->desc?> (<?=$metric->name?>)</a></h3>
        <div id="<?=$metric->name?>-area"">
            <form action="<?=url::site('reports/xport')?>/" method="post" id="form-<?=$metric->name?>" class="xportForm">
                <input type="hidden" name="source" value="<?=$source['id']?>" />
                <input type="hidden" name="destination" value="<?=$destination['id']?>" />
                <input type="hidden" name="metric" value="<?=$metric->id?>" />
                <input type="hidden" name="startDate" value="" />
                <input type="hidden" name="startHour" value="" />
                <input type="hidden" name="endDate" value="" />
                <input type="hidden" name="endHour" value="" />
                <input type="submit" style="display: none;"/>


                <span style="margin-left: 15px">
                   <a href="#" class="xportButton" rel="<?=$metric->name?>" id="button-<?=$metric->name?>">Download do arquivo CSV</a>
                </span>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<script>
    $(function() {
        $("#metricsAccordion").accordion({
            collapsible: true,
            active: 0,
            fillSpace: true,
            clearStyle: true
        });

        $(".xportButton").click(function(event) {
            event.preventDefault();
            var metric = jQuery(event.target).attr("rel");
            $("#form-"+metric).children('[name="startDate"]').val($("#inicio").val());
            $("#form-"+metric).children('[name="startHour"]').val($("#horaini").val());
            $("#form-"+metric).children('[name="endDate"]').val($("#fim").val());
            $("#form-"+metric).children('[name="endHour"]').val($("#horafim").val());

            $("#form-"+metric).submit();
        });

    });
</script>
