		
	</main>
<!--            Footer -->
	<footer>
		<div class="container-fluid bg bg-dark text-white mt-4 rounded">
			<div class="row">
				<div class="col-3 pt-3 pl-2 pb-5"></div>
				<div class="col-6 pt-3 pl-2 pb-5 text-center"><a href="./accueil.php">KipTrack <span style= "vertical-align: text-top; font-size: 0.5rem">&copy;<?=date("Y");?></span></a></div>
				<div class="col-3 pt-3 pl-2 pb-5"></div>
			</div>
		</div>
	</footer>
</div>
</div>
</div>
<!--        CanvasJS-->
<?php if($page=="Main" && $user_nbr_of_account["COUNT(*)"]!=0): ?>
<script>
window.onload = function () {

	var chart = new CanvasJS.Chart("chartContainer", {
		animationEnabled: true,
		title:{
			text: "Versements Vs Dépenses"
		},	
		axisY: {
			title: "Versements (€)",
			titleFontColor: "#4F81BC",
			lineColor: "#4F81BC",
			labelFontColor: "#4F81BC",
			tickColor: "#4F81BC"
		},
		axisY2: {
			title: "Dépenses (€)",
			titleFontColor: "#C0504E",
			lineColor: "#C0504E",
			labelFontColor: "#C0504E",
			tickColor: "#C0504E"
		},	
		toolTip: {
			shared: true
		},
		legend: {
			cursor:"pointer",
			itemclick: toggleDataSeries
		},
		data: [{
			type: "column",
			name: "Versements (€)",
			legendText: "Versements",
			showInLegend: true, 
			dataPoints:<?php echo json_encode($dataPoints1, JSON_NUMERIC_CHECK); ?>
		},
		{
			type: "column",	
			name: "Dépenses (€)",
			legendText: "Dépenses",
			axisYType: "secondary",
			showInLegend: true,
			dataPoints:<?php echo json_encode($dataPoints2, JSON_NUMERIC_CHECK); ?>
		}]
	});
	chart.render();
	
	function toggleDataSeries(e) {
		if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
			e.dataSeries.visible = false;
		}
		else {
			e.dataSeries.visible = true;
		}
		chart.render();
	}

}
</script>


<?php endif ?>
<!--        Boostrap-->
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>

<!--        page JS-->
<script type="application/javascript" src="./js/plugins.js"></script>
<script type="application/javascript" src="./pagetransitions/page-transitions.js"></script>
<?php if($page=="Main"): ?>
<script type="application/javascript" src="./js/canvasjs.min.js"></script>
<?php endif ?>
<script type="application/javascript" src="./js/script.js"></script>
</body>

</html>