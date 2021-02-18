
/*for scaling*/
var border = [
	{x:51.21300063774803, y:-0.622736484755964},
	{x:51.212660814422556, y:-0.6227230737123409},
	{x:51.212635191054346, y:-0.6235089609292354},
	{x:51.212999797644585, y:-0.6235143253450431},
	].map(function(c){
		return {x:c.y * scale, y:c.x * scale};
	});

var outline = [
	{x:51.21291331627834, y:-0.6231142712691963},
	{x:51.21293935954447, y:-0.6230183823028284},
	{x:51.212778009649384, y:-0.6228645602331597},
	{x:51.21273395338237, y:-0.622981501927509},
	{x:51.21282258478283, y:-0.6230653209540543},
	{x:51.21283350617579, y:-0.6230364872089227},
	{x:51.21291331627834, y:-0.6231142712691963},
].map(function(c){
		return {x:c.y * scale, y:c.x * scale};
	});

var greenhouse = [
	{x:51.21280872293881, y:-0.6230552615576234},
	{x:51.21281838417413, y:-0.6230277689150846},
	{x:51.212796541378374, y:-0.6230110051086586},
	{x:51.21278856035427, y:-0.6230371566466831},
].map(function(c){
		return {x:c.y * scale, y:c.x * scale};
	});

$(function(){
	document.getElementById('map').src = document.getElementById('map').src
	$("#pagetitle").html("We maintain an allotment in Compton, Surrey");
	let m=minmax([outline,greenhouse,border]);
	normalize(greenhouse,m);
	normalize(outline,m);
	//normalize(border,m);
	plotol = createPath(outline);
	greenol = createPath(greenhouse);

	document.getElementById("allotgroup").appendChild(
		makeSVG("path",{id:"greenhouse"})
	);
	
	$("svg g path").attr("d",plotol);
	$("#greenhouse").attr("stroke","green")
		.attr("stroke-width",".2")
		.attr("fill","none")
		.attr("d",greenol);
});

