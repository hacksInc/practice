$(function(){

	var l_count = $('#l_count').text();
	var r_count = $('#r_count').text();

	var max = +l_count + +r_count;

	var blueGauge = Math.floor(+l_count/max*100);
	var redGauge = Math.floor(+r_count/max*100);

	$('#blueGauge').css('width', blueGauge + "%");
	$('#redGauge').css('width', redGauge + "%");
});