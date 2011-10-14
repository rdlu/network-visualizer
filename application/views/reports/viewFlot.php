<div id="tabs">
	<div id="metricsAccordion" class="accordion">
		<?php foreach ($metrics as $order => $metric): ?>
		<h3><a href="#"><?=$metric->desc?> (<?=$metric->name?>)</a></h3>
		<div id="<?=$metric->name?>-area" style="height: 370px;" >
			<div id="flot-<?=$metric->name?>" style="height: 320px; width: 80%; margin-right: 5px; float: left"></div>
			<div id="selection-<?=$metric->name?>" style="height: 300px; padding: 10px">
				<strong>> Filtros do gráfico</strong><br />
				<em style="font-size: 12px;">> Remover valores errôneos</em><br />
				<label for="filterType-<?=$metric->name?>-sd" style="line-height: 12px; font-size: 11px;">Upstream: </label>
				<select name="filterType[<?=$metric->name?>][sd]" id="filterType-<?=$metric->name?>-sd" style="line-height: 12px; font-size: 11px;">
					<option value=">=">Acima de</option>
					<option value="<=">Abaixo de</option>
				</select>
				<input type="text" id="filterValue-<?=$metric->name?>-sd" name="filterValue[<?=$metric->name?>][sd]"
				       maxlength="4" size="4" style="line-height: 12px; font-size: 11px;"/>
				<em id="filterUnit-<?=$metric->name?>-sd" class="filterUnit-<?=$metric->name?>" style="font-size: 11px;"></em>
				<br />

				<em style="font-size: 12px;">> Seleção das séries de valores</em>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
</div>
<script type="text/javascript">
	var graphReport = {
		response: <?=$results?>,
		objects: {},
		draw: function(metric, metricObj) {
			var datasets = graphReport.datasetWithKeys(metric, metricObj);

			//desenha opcoes e seleciona somente o Avg
			var choiceContainer = jQuery("#selection-" + metric);
			var data = [], checked = '';
			jQuery.each(datasets, function(idx, dataset) {
				var type = idx.substr(0,3);
				if(type=='Avg') {
					data.push(dataset);
					var checked = 'checked';
				}
				choiceContainer.append('<br/><input type="checkbox" name="'+ idx +
						'" '+checked+' id="checkbox'+ metric + idx + '">' +
						'<label for="checkbox'+ metric + idx + '" style="font-size:10px;">'
						+ dataset.label2 + '</label>');
			});

			//desenha grafico
			graphReport.objects[metric] = jQuery.plot(
					$("#flot-" + metric),
					data,
					{
						xaxis: { mode: "time" },
						yaxis: {
							tickFormatter: function(val, axis) {
								return conversion.stringFromMetric(metric, val, axis);
							}
						},
						series: { lines: {show: true} },
						crosshair: { mode: "x" },
						grid: { hoverable:true, autoHighlight:false },
						selection: { mode: "x" }
					}
			);
			choiceContainer.find("input").click(function(evt) {
				graphReport.objects[metric].clearSelection();
				graphReport.redraw(metric, metricObj);
			});

			//graphReport.redraw(metric,datasets);

			//linha horizontal
			$("#flot-" + metric).bind("plothover", function (event, pos, item) {
				var xValueFormatted = null;
				if (item) {
					xValueFormatted = item.series.xaxis.tickFormatter(item.datapoint[0], item.series.xaxis);

					if (this.previousPoint != item.dataIndex) {
						this.previousPoint = item.dataIndex;

						$("#graphTooltip").remove();
						var x = item.datapoint[0].toFixed(2),
								y = item.datapoint[1].toFixed(2);

						graphReport.showTooltip(pos.pageX+5, pos.pageY, xValueFormatted);
					}
				} else {
					$("#graphTooltip").remove();
					this.previousPoint = null;
				}
				graphReport.latestPosition = pos;
				if (!graphReport.updateLegendTimeout)
					graphReport.updateLegendTimeout = setTimeout(function() {
						graphReport.updateLegend(graphReport.objects[metric], event.target, metric, xValueFormatted)
					}, 50);
			});

			//controle de selecao
			$("#flot-" + metric).bind("plotselected", function(event, ranges) {
				var data2 = {};
				var choiceContainer = jQuery("#selection-" + metric);
				choiceContainer.find("input:checked").each(function () {
					var key = $(this).attr("name");
					if (key && datasets[key])
						data2[key] = datasets[key];
				});
				//console.log(event,ranges,metric,metricObj,data2);

				if (data.length > 0) {

				}
			});

		},
		redraw: function(metric, metricObj) {
			var data = [];
			var choiceContainer = jQuery("#selection-" + metric);
			var datasets = graphReport.datasetWithKeys(metric, metricObj);
			choiceContainer.find("input:checked").each(function () {
				var key = $(this).attr("name");
				if (key && datasets[key])
					data.push(datasets[key]);
			});

			var rawFilterValueSD = jQuery("#filterValue-"+metric+"-sd").val();
			var rawFilterValueDS = jQuery("#filterValue-"+metric+"-ds").val();
			if(rawFilterValueSD || rawFilterValueDS) {
				var filterValue = parseFloat($u(rawFilterValue,conversion.metrics[metric].target).as(conversion.metrics[metric].original).val());
				console.log("Valores do filtro",rawFilterValue,filterValue);
				var isFilterHigherThan = jQuery("#filterType-"+metric).val() == '>=';
				for(var seriesIdx in data) {
					var newData=[];
					console.log(data[seriesIdx]);
					for(var dataIdx in data[seriesIdx].data) {
						var testedValue = parseFloat(data[seriesIdx].data[dataIdx][1]);
						//console.log(data[seriesIdx].data[dataIdx]);
						if(isFilterHigherThan) {
							//console.log("Incluir? (<=)", testedValue <= filterValue, testedValue);
							if(testedValue <= filterValue)
								newData.push(data[seriesIdx].data[dataIdx]);
						} else {
							//console.log("Incluir? (>=)",testedValue >= filterValue,testedValue);
							if(testedValue >= filterValue)
								newData.push(data[seriesIdx].data[dataIdx]);
						}
					}
					console.log("newData",newData.length);
					data[seriesIdx].data = jQuery(newData);
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
		drawAll: function() {
			//console.log(this.results);
			jQuery.each(this.response, this.draw);
		},
		results: function(metric, metricObj, type, path) {
			var retorno = [];
			if (metric == 'rtt' && (path == 'sd')) return retorno;
			jQuery.each(metricObj[type].values[path], function(idx, el) {
				retorno.push([idx,el]);
			});
			return retorno;
		},
		resultsWithLabels: function (metric, metricObj, type, path) {
			var results = graphReport.results(metric, metricObj, type, path);
			var caminho = (path == "ds") ? "Up" : "Down";
			if (metric == 'rtt') caminho = "";
			return { data: results, label: titleCaps(metric) + " " + caminho + " (" + type + ") = 0.0", label2: caminho + "stream (" + type + ")" };

		},
		datasetWithKeys: function(metric, metricObj) {
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
		dataset: function(metric, metricObj) {
			var retorno = [];
			for (var type in metricObj) {
				for (var path in metricObj[type].values) {
					retorno.push(graphReport.resultsWithLabels(metric, metricObj, type, path));
				}
			}
			return retorno;
		},
		updateLegendTimeout: null,
		latestPosition: null,
		updateLegend: function(plotObj, target, metric, xValueFormatted) {
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
					y = p1result + (p2result - p1result) * (pos.x - p1time) / (p2time - p1time);

				var legends = jQuery(target).find(".legendLabel");

				var valueForLegend = conversion.stringFromMetric(metric, y);

				legends.eq(i).text(series.label.replace(/=.*/, "= " + valueForLegend));
			}
		},
		showTooltip: function(x, y, contents) {
			$('<div id="graphTooltip">' + contents + '</div>').css({
				position: 'absolute',
				display: 'none',
				top: y + 5,
				left: x + 5,
				border: '1px solid #fdd',
				padding: '2px',
				'background-color': '#fee',
				opacity: 0.80
			}).appendTo("body").fadeIn(200);
		}
	};

	$(function() {

		graphReport.drawAll();

		jQuery.each(graphReport.response, function(idx,el) {
			jQuery("#filterUnit-"+idx).append(conversion.metrics[idx].target);
		});


		$("#metricsAccordion").accordion({
			collapsible: true,
			active: <?=count($results) - 1 ?>,
			autoHeight: false,
			clearStyle: false
		});
	});
</script>
