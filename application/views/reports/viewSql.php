<span class="nice little info" style="padding: 2px 5px 2px 30px !important; display: inline-block;">Período selecionado para os gráficos: <?=$startDate . ' ' . $startHour?>
    até <?=$endDate . ' ' . $endHour?></span><br/><br/>
<div id="tabs">
    <div id="metricsAccordion" class="accordion">
        <?php foreach ($metrics as $order => $metric): ?>
        <h3><a href="#"><?=$metric->desc?> (<?=$metric->name?>)</a></h3>
        <div id="<?=$metric->name?>-area" style="height: 370px;">
            <div id="sql-<?=$metric->name?>" style="height: 320px; width: 80%; margin-right: 5px; float: left"></div>
            <div id="selection-<?=$metric->name?>" style="height: 300px; padding: 10px">
                <strong>> Exportar para CSV</strong><br/>
                <span style="margin-left: 15px">
                   <a href="#" class="xportButton" rel="<?=$metric->name?>" id="button-<?=$metric->name?>"><img
                       src="<?=url::base()?>/images/actions/table_save.png"
                       alt="Salvar"> Salvar</a>
                </span>

                <form action="<?=url::site('reports/xport')?>/" method="post" id="form-<?=$metric->name?>"
                      class="xportForm">
                    <input type="hidden" name="source" value="<?=$source['id']?>"/>
                    <input type="hidden" name="destination" value="<?=$destination['id']?>"/>
                    <input type="hidden" name="metric" value="<?=$metric->id?>"/>
                    <input type="hidden" name="startDate" value=""/>
                    <input type="hidden" name="startHour" value=""/>
                    <input type="hidden" name="endDate" value=""/>
                    <input type="hidden" name="endHour" value=""/>
                    <input type="submit" style="display: none;"/>

                    <strong>> Filtros do gráfico</strong><br/>
                    <em style="font-size: 12px;">> Remover valores:</em><br/>

                    <label for="filterType-<?=$metric->name?>-ds"
                           style="line-height: 12px; font-size: 11px;">Down: </label>
                    <select name="filterType[<?=$metric->name?>][ds]" id="filterType-<?=$metric->name?>-ds"
                            style="line-height: 12px; font-size: 11px;">
                        <option value=">=">Acima de</option>
                        <option value="<=">Abaixo de</option>
                    </select>
                    <input type="text" id="filterValue-<?=$metric->name?>-ds" name="filterValue[<?=$metric->name?>][ds]"
                           maxlength="4" size="4" style="line-height: 12px; font-size: 11px;"/>
                    <em id="filterUnit-<?=$metric->name?>-ds" class="filterUnit-<?=$metric->name?>"
                        style="font-size: 11px;"></em>
                    <br/>
                    <?php if ($metric->name != 'rtt'): ?>
                    <label for="filterType-<?=$metric->name?>-sd"
                           style="line-height: 12px; font-size: 11px;">Up: </label>
                    <select name="filterType[<?=$metric->name?>][sd]" id="filterType-<?=$metric->name?>-sd"
                            style="line-height: 12px; font-size: 11px;">
                        <option value=">=">Acima de</option>
                        <option value="<=">Abaixo de</option>
                    </select>
                    <input type="text" id="filterValue-<?=$metric->name?>-sd" name="filterValue[<?=$metric->name?>][sd]"
                           maxlength="4" size="4" style="line-height: 12px; font-size: 11px;"/>
                    <em id="filterUnit-<?=$metric->name?>-sd" class="filterUnit-<?=$metric->name?>"
                        style="font-size: 11px;"></em>
                    <br/>
                    <?php endif; ?>

                    <em style="font-size: 12px;">> Seleção das séries de valores</em>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div><br/>
<table class="filterMenu">
    <tr>
        <td>
            <strong style="text-shadow: none">Exportação dos valores</strong><br/>
            <em style="font-size: 13px; text-shadow: none">
                1. Após consultar, selecione o intervalo no gráfico com o mouse (clicar e arrastar)<br/>
                2. Aperte CTRL + C no seu teclado<br/>
                3. Cole no Excel (CTRL + V)<br/>
            </em>
        </td>
    </tr>
</table>
<script type="text/javascript">
var graphReport = {
    response: <?=$results?>,
    source: <?=Zend_Json::encode($source)?>,
    destination: <?=Zend_Json::encode($destination)?>,
    range:{ start:"<?=$startDate . " " . $startHour?>", end:"<?=$endDate . " " . $endHour?>"  },
    objects:{},
    draw:function (metric, metricObj) {
        var datasets = graphReport.datasetWithKeys(metric, metricObj);

        //desenha opcoes e seleciona somente o Avg
        var choiceContainer = jQuery("#selection-" + metric);
        var data = [], checked = '';
        jQuery.each(datasets, function (idx, dataset) {
            var type = idx.substr(0, 3);
            if (type == 'Avg') {
                data.push(dataset);
                var checked = 'checked';
            }
            choiceContainer.append('<br/><input type="checkbox" name="' + idx +
                '" ' + checked + ' id="checkbox' + metric + idx + '">' +
                '<label for="checkbox' + metric + idx + '" style="font-size:10px;">'
                + dataset.label2 + '</label>');
        });

        //desenha grafico
        graphReport.objects[metric] = jQuery.plot(
            $("#sql-" + metric),
            data,
            {
                xaxis:{ mode:"time" },
                yaxis:{
                    tickFormatter:function (val, axis) {
                        return conversion.stringFromMetric(metric, val, axis);
                    }
                },
                series:{ lines:{show:true} },
                crosshair:{ mode:"x" },
                grid:{ hoverable:true, autoHighlight:false },
                selection:{ mode:"x" }
            }
        );
        choiceContainer.find("input").bind('change keyup', function (evt) {
            //console.log(evt);
            graphReport.objects[metric].clearSelection();
            graphReport.redraw(metric, metricObj);
        });

        //graphReport.redraw(metric,datasets);

        //linha horizontal
        $("#sql-" + metric).bind("plothover", function (event, pos, item) {
            var xValueFormatted = null;
            if (item) {
                xValueFormatted = item.series.xaxis.tickFormatter(item.datapoint[0], item.series.xaxis);

                if (this.previousPoint != item.dataIndex) {
                    this.previousPoint = item.dataIndex;

                    $("#graphTooltip").remove();
                    var x = item.datapoint[0].toFixed(2),
                        y = item.datapoint[1].toFixed(2);

                    graphReport.showTooltip(pos.pageX + 5, pos.pageY, xValueFormatted);
                }
            } else {
                $("#graphTooltip").remove();
                this.previousPoint = null;
            }
            graphReport.latestPosition = pos;
            if (!graphReport.updateLegendTimeout)
                graphReport.updateLegendTimeout = setTimeout(function () {
                    graphReport.updateLegend(graphReport.objects[metric], event.target, metric, xValueFormatted)
                }, 50);
        });

        //controle de selecao
        $("#sql-" + metric).bind("plotselected", function (event, ranges) {
            graphReport.exportTable(metric, ranges.xaxis, graphReport.objects[metric].getData());
        });

    },
    redraw:function (metric, metricObj) {
        var data = [];
        var choiceContainer = jQuery("#selection-" + metric);
        var datasets = graphReport.datasetWithKeys(metric, metricObj);
        choiceContainer.find("input:checked").each(function () {
            var key = $(this).attr("name");
            if (key && datasets[key])
                data.push(datasets[key]);
        });

        var rawFilterValueDS = jQuery("#filterValue-" + metric + "-ds").val();
        if (metric != 'rtt') {
            var rawFilterValueSD = jQuery("#filterValue-" + metric + "-sd").val();
            var filterValueSD = parseFloat($u(rawFilterValueSD, conversion.metrics[metric].target).as(conversion.metrics[metric].original).val());
        }
        if (rawFilterValueSD || rawFilterValueDS) {
            var filterValueDS = parseFloat($u(rawFilterValueDS, conversion.metrics[metric].target).as(conversion.metrics[metric].original).val());
            //console.log("Valores do filtro SD,DS",rawFilterValueSD,filterValueSD,rawFilterValueDS,filterValueDS);
            var isSDFilterHigherThan = jQuery("#filterType-" + metric + "-sd").val() == '>=';
            var isDSFilterHigherThan = jQuery("#filterType-" + metric + "-ds").val() == '>=';
            for (var seriesIdx in data) {
                var newData = [];
                //console.log(data[seriesIdx]);
                for (var dataIdx in data[seriesIdx].data) {
                    var testedValue = parseFloat(data[seriesIdx].data[dataIdx][1]);
                    if (data[seriesIdx].path == 'sd')
                        if (isSDFilterHigherThan) {
                            //console.log("Incluir? (<=)", testedValue <= filterValue, testedValue);
                            if (testedValue <= filterValueSD)
                                newData.push(data[seriesIdx].data[dataIdx]);
                            else
                                newData.push([data[seriesIdx].data[dataIdx][0], null]);
                        } else {
                            //console.log("Incluir? (>=)",testedValue >= filterValue,testedValue);
                            if (testedValue >= filterValueSD)
                                newData.push(data[seriesIdx].data[dataIdx]);
                            else
                                newData.push([data[seriesIdx].data[dataIdx][0], null]);
                        }
                    else if (isDSFilterHigherThan) {
                        //console.log("Incluir? (<=)", testedValue <= filterValue, testedValue);
                        if (testedValue <= filterValueDS)
                            newData.push(data[seriesIdx].data[dataIdx]);
                        else
                            newData.push([data[seriesIdx].data[dataIdx][0], null]);
                    } else {
                        //console.log("Incluir? (>=)",testedValue >= filterValue,testedValue);
                        if (testedValue >= filterValueDS)
                            newData.push(data[seriesIdx].data[dataIdx]);
                        else
                            newData.push([data[seriesIdx].data[dataIdx][0], null]);
                    }
                }
                //console.log("newData",newData.length);
                if ((rawFilterValueSD && data[seriesIdx].path == 'sd') || (rawFilterValueDS && data[seriesIdx].path == 'ds')) data[seriesIdx].data = jQuery(newData);
            }

        }

        //console.log(metric,dataset,choiceContainer,data);

        if (data.length > 0) {
            var plotObj = graphReport.objects[metric];
            plotObj.setData(data);
            plotObj.setupGrid();
            plotObj.draw();
        }
    },
    drawAll:function () {
        //console.log(this.results);
        jQuery.each(this.response, this.draw);
    },
    results:function (metric, metricObj, type, path) {
        var retorno = [];
        if (metric == 'rtt' && (path == 'sd')) return retorno;
        jQuery.each(metricObj[type].values[path], function (idx, el) {
            retorno.push([idx, el]);
        });
        return retorno;
    },
    resultsWithLabels:function (metric, metricObj, type, path) {
        var results = graphReport.results(metric, metricObj, type, path);
        var caminho = (path == "ds") ? "Down" : "Up";
        if (metric == 'rtt') caminho = "Roundtrip ";
        return {
            data:results, label:titleCaps(metric) + " " + caminho + " (" + type + ") = 0.0",
            label2:caminho + "stream (" + type + ")",
            type:type,
            path:path
        };

    },
    datasetWithKeys:function (metric, metricObj) {
        var retorno = {};
        var i = 0;
        for (var type in metricObj) {
            for (var path in metricObj[type].values) {
                retorno[type + "" + path] = graphReport.resultsWithLabels(metric, metricObj, type, path);
                retorno[type + "" + path].color = i;
                i++;
            }
        }
        return retorno;
    },
    dataset:function (metric, metricObj) {
        var retorno = [];
        for (var type in metricObj) {
            for (var path in metricObj[type].values) {
                retorno.push(graphReport.resultsWithLabels(metric, metricObj, type, path));
            }
        }
        return retorno;
    },
    updateLegendTimeout:null,
    latestPosition:null,
    updateLegend:function (plotObj, target, metric, xValueFormatted) {
        this.updateLegendTimeout = null;

        var pos = this.latestPosition;

        var axes = plotObj.getAxes();
        if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max ||
            pos.y < axes.yaxis.min || pos.y > axes.yaxis.max)
            return;

        var i, j, dataset = plotObj.getData();
        for (i = 0; i < dataset.length; ++i) {
            var series = dataset[i];

            // find the nearest points, x-wise
            for (j = 0; j < series.data.length; ++j)
                if (series.data[j][0] > pos.x)
                    break;

            // now interpolate
            var y, p1 = series.data[j - 1], p2 = series.data[j];
            //console.debug("P1 e P2 sem parseFloat", series.data[j - 1], series.data[j]);
            var p1time = parseInt(p1[0]), p2time = parseInt(p2[0]);
            var p1result = parseFloat(p1[1]), p2result = parseFloat(p2[1]);
            //console.debug("P1 e P2", p1time, p1result, p2time, p2result);
            if (p1 == null)
                y = p2result;
            else if (p2 == null)
                y = p1result;
            else
                y = p1result; //+ (p2result - p1result) * (pos.x - p1time) / (p2time - p1time);

            var legends = jQuery(target).find(".legendLabel");

            var valueForLegend = conversion.stringFromMetric(metric, y);

            legends.eq(i).text(series.label.replace(/=.*/, "= " + valueForLegend));
        }
    },
    showTooltip:function (x, y, contents) {
        $('<div id="graphTooltip">' + contents + '</div>').css({
            position:'absolute',
            display:'none',
            top:y + 5,
            left:x + 5,
            border:'1px solid #fdd',
            padding:'2px',
            'background-color':'#fee',
            opacity:0.80
        }).appendTo("body").fadeIn(200);
    },
    exportTable:function (metric, range, data) {
        //console.log(metric,range,data);
        var source = graphReport.source.name + " (" + graphReport.source.ipaddress + ")";
        var destination = graphReport.destination.name + " (" + graphReport.destination.ipaddress + ")";
        var from = new Date(parseInt(range.from) + 7200000).toLocaleString();
        var to = new Date(parseInt(range.to) + 7200000).toLocaleString();

        var table = "<table>\r\n\t<tr><th>Resultados de medições da métrica " + metric + " em " + conversion.metrics[metric].original + "</th></tr>";
        table += "\r\n\t<tr><th>Sonda Origem: " + source + "</th>\r\n\t<th>Sonda Destino " + destination + "</th></tr>";
        table += "\r\n\t<tr><th>Data Inicio: " + from + "</th>\r\n\t<th>Data Fim: " + to + "</th></tr>";
        table += "\r\n\t<tr><th>Timestamp</th> ";
        var line, col;
        for (col = 0; col < data.length; ++col) {
            table += "<th>" + data[col].label2 + "</th>";
        }
        table += "</tr>";
        for (line = 0; line < data[0].data.length; ++line) {
            var timestamp = parseInt(data[0].data[line][0]);
            if (range.from > timestamp || range.to < timestamp) continue;
            var d = new Date(timestamp + 7200000);
            var formatedDate = d.getFullYear() + "-" + (d.getMonth() + 1).padZero() + "-" + d.getDate().padZero() + " " + d.getHours().padZero() + ":" + d.getMinutes().padZero() + ":" + d.getSeconds().padZero();
            table += "\r\n\t<tr><td>" + formatedDate + "</td>";
            for (col = 0; col < data.length; ++col) {
                var value = (data[col].data[line][1] == null) ? "" : conversion._value(metric, data[col].data[line][1]);
                table += "<td>" + value + "</td>"
            }
            table += "</tr>";
        }
        table += "\r\n</table>";

        jQuery("#tempArea").val(table);
        jQuery("#clipboardArea").dialog({
            modal:true,
            title:"Dados a serem copiados para o Excel",
            minWidth:550,
            buttons:{
                Ok:function () {
                    $(this).dialog("close");
                }
            }
        });
        jQuery("#tempArea").select().focus();
    }
};

$(function () {

    graphReport.drawAll();

    jQuery.each(graphReport.response, function (idx, el) {
        jQuery("#filterUnit-" + idx + "-sd").append(conversion.metrics[idx].target);
        if (idx != 'rtt') jQuery("#filterUnit-" + idx + "-ds").append(conversion.metrics[idx].target);
    });


    $("#metricsAccordion").accordion({
        collapsible:true,
        active: <?=count($results) - 1 ?>,
        autoHeight:false,
        clearStyle:false
    });

    $(".xportButton").click(function (event) {
        event.preventDefault();
        var metric = jQuery(event.target).attr("rel");
        $("#form-" + metric).children('[name="startDate"]').val($("#inicio").val());
        $("#form-" + metric).children('[name="startHour"]').val($("#horaini").val());
        $("#form-" + metric).children('[name="endDate"]').val($("#fim").val());
        $("#form-" + metric).children('[name="endHour"]').val($("#horafim").val());

        $("#form-" + metric).submit();
    });
});
</script>
