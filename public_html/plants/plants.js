	var ori;
	var Markers;
	var mymap;
	$( function() {
		$("#pagetitle").html("Plants collected on my phone");
		$("#DateFromSelect").on("click",function(){
			$("#fromdate").toggle("fade",500);
		});
		$("#DateToSelect").on("click",function(){
			$("#todate").toggle("fade",500);
		});
		$("#frompicker").datepicker({
			dateFormat:"yy-mm-dd",
				autoSize:true,
				showButtonPanel:true,
				onSelect:function(dateText){
					updateThumbs(null);
				}
		});
		$("#topicker").datepicker({
			dateFormat:"yy-mm-dd",
				autoSize:true,
				onSelect:function(dateText){
					updateThumbs(null);
				}
		});
		$("#place").submit(function(event){
			event.preventDefault();
			place=$("#i_place").val();
			$.get("https://nominatim.openstreetmap.org/search",{q:place,format:"json"},function(data){
				boxcoords = data[0].boundingbox
				console.log(boxcoords);
				p1 =[boxcoords[0],boxcoords[2]];
				p2 =[boxcoords[1],boxcoords[3]];
				mymap.fitBounds([p1,p2]);
			});
		});
		setupFilters();
		//console.log = function(){}
		var lat = 51.476852;
		var lon = 0.00;
		mymap = L.map('mapid' , {
			//dragging: !L.Browser.mobile,
			//tap: !L.Browser.mobile
		});
//		var marker;
		mymap.on('moveend', function() { 
			updateThumbs(null);
		})

		mymap.on('load', function() { 
			updateThumbs(null);
		});

		L.tileLayer('https://b.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.openstreetmap.org">OpenStreetMap</a>',
			maxZoom: 18,
			id: '"openstreetmap".',
		}).addTo(mymap);
		mymap.setView([lat, lon], 13);
		//updateThumbs();

	});

	function locate(src){
		var pic = document.getElementById("mainImage");
		var imginfo = document.getElementById("imageinfo");
		var path=src.currentTarget.dataset.path;
		pic.src=path;
		try{
		var plantData = JSON.parse(atob(src.currentTarget.dataset.plantType));
			imginfo.innerText=plantData.family;
			imginfo.innerText+="\n"+plantData.genus;
			imginfo.innerText+="\n"+plantData.species;
			imginfo.innerText+="\n"+plantData.common;
		}catch{};
		var loc = src.currentTarget.dataset.location.split(',');
			if(! "marker" in window){
				marker.remove();
			}
		mymap.setView([loc[0],loc[1]], 16);
		marker = L.marker([loc[0],loc[1]]).addTo(mymap);
	}
	function touchend(ev){
		mapframe.style.width=(content.clientWidth-w)+"px";
	}
	function vmmove(e){
		var ori=window.matchMedia("(orientation: landscape)");
		var aspect = window.matchMedia("max-aspect-ratio: 3/2");
		var content = document.getElementById("content");
		var mapframe = document.getElementById("mapframe");
		//var piccontainer = document.getElementById("piccontainer");
		var imageview = document.getElementById("imageview");
		if(ori.matches){
			var w = e.clientX;//-piccontainer.clientWidth;
			imageview.style.width=w+"px";
			mapframe.style.width=(content.clientWidth-w)+"px";
		}else{
			var x = e.pageX - $('#imageview').offset().left;
			var y = e.pageY - $('#imageview').offset().top;
			var w = e.clientY;//-piccontainer.clientWidth;
			imageview.style.height=y+"px";
			mapframe.style.height=(content.clientHeight-y)+"px";
		}
	}
	function vmove(e){
		var touch=e.touches[0];
		var content = document.getElementById("content");
		var mapframe = document.getElementById("mapframe");
		var piccontainer = document.getElementById("piccontainer");
		var imageview = document.getElementById("imageview");
		///var w = e.clientX-piccontainer.clientWidth;
		var w = touch.pageX;//-piccontainer.clientWidth;
		imageview.style.width=w+"px";
		mapframe.style.width=(content.clientWidth-w)+"px";
	}

	function tglsubMenu(e){
		submenu=document.getElementById("submenu");
		compStyle = window.getComputedStyle(submenu);
		mainarea=document.getElementById("mainarea");
		subopen=document.getElementById("open");
		subclosed=document.getElementById("closed");
		if(compStyle.visibility=="collapse"){
			submenu.style.height="5vh";
			submenu.style.visibility="visible";
			mainarea.style.height="70vh";
			subopen.attributes['visibility'].value="hidden";
			subclosed.attributes['visibility'].value="visible";
		}else{
			submenu.style.height="0vh";
			submenu.style.visibility="collapse";
			mainarea.style.height="75vh";
			subopen.attributes['visibility'].value="visible";
			subclosed.attributes['visibility'].value="hidden";
		}
	}
	function addToMap(loc,image){
				marker = L.marker(loc).addTo(Markers);
				marker.id=image;
				marker.bindPopup( "<img src=" + image.image + " style=\"width:100%\" /> <p>" + image.species +  "</p>" );
	}
	function updateThumbs(event){
		//event.preventDefault();
		var mapbounds = mymap.getBounds();
		var fdata = new FormData($('#filter')[0]);
		var date = $('#frompicker').datepicker("getDate");
		fdata.append('from',$.datepicker.formatDate("yy-mm-dd",date));
		date = $('#topicker').datepicker("getDate");
		fdata.append('to',$.datepicker.formatDate("yy-mm-dd",date));
		if($('#onmap').is(":checked")){
			fdata.append('map',JSON.stringify(mapbounds));
		}
		$.ajax({ url:"/plants/plantapi.php?images",
				method:"POST",
				data:fdata,
				processData: false,
				contentType:false,
				success:function(x,y,z)
				{
					$('#piccontainer').empty();
					var res=JSON.parse(x);
					var div = document.getElementById('piccontainer');
					if(Markers!=null){
						Markers.clearLayers();
					}
					Markers = L.layerGroup();
					for (var i in res.pics){
						var picdata = res.pics[i];
						loc=picdata.loc;
						loc = loc.split(',');
						marker = L.marker(loc).addTo(Markers);
						marker.id=i;
						var x = atob(picdata.pic);
						var img = $("<img></img>");
						var tdiv = $("<div class=\"thumbHolder\"></div>").append(img).append("<p>"+picdata.date+"</p>");
						
						img.attr("src", "data:image/jpeg;base64,"+picdata.pic);
						img.attr("data-path",picdata.name);
						img.attr("data-plant-type",picdata.cats);
						img.attr("data-location",picdata.loc);
						$('#piccontainer').append(tdiv);
						img.attr("style", "width:100%");
						var ip = img.clone().add($("<p>" + picdata.species +  "</p>"));
						var divip = $("<div></div>").append(ip);
						marker.bindPopup(divip.prop('outerHTML') );
				}
				Markers.addTo(mymap);
				$image=$("#piccontainer img");
				$image.addClass("thumb");
				$image.click(locate);
				}
		});
	}

	let getFamilies = new Promise((resolve,reject) =>
	{
		$.ajax({ url:"/plants/plantapi.php?family",
			success:function(x,y,z)
			{
				var res=JSON.parse(x);
				var select = $('#familySelected');

				for (var i in res){
					var option = document.createElement("option");
					option.value=res[i].family;
					option.innerText=res[i].family;
					option.selected=false;
					select.append(option);
				}
				resolve("it is done");
			}
		});

	});

	let getFirstDate = new Promise((resolve,reject) =>
	{
		$.ajax({ url:"/plants/plantapi.php?firstdate",
			success:function(x,y,z)
			{
				var date= JSON.parse(x);
				$( "#frompicker" ).datepicker( "setDate", date );
				//var d = $("#fromdate").datepicker("getDate");
				//var dd = $.datepicker.formatDate("yy-mm-dd",d);
				//$("#DateFromSelect").text(dd);

				d = $("#topicker").datepicker("setDate",new Date());
				resolve("it is done");
			}
		});
	});

	Promise.all([getFirstDate,getFamilies]).then(()=>{
		updateThumbs();
	});
	function setupFilters()
	{

		$("#familySelected").change(function(){
			$("#genusSelected").find('option').prop('selected',false);
			$("#speciesSelected").find('option').prop('selected',false);
			var fdata = new FormData($('#filter')[0]);
			$.ajax({ url:"/plants/plantapi.php?genus",
					method:"POST",
					data:fdata,
					processData: false,
					contentType:false,
					success:function(x,y,z)
			{
				var genuses = JSON.parse(x);
				$('#speciesSelected').empty();
				$('#genusSelected').empty();
				$.each(genuses,function(i,genus){
					$('#genusSelected').append("<option>"+genus['genus']+"</option>");
				});
			}});
			updateThumbs();
		});
		$("#genusSelected").change(function(){
			$("#speciesSelected").find('option').prop('selected',false);
			var fdata = new FormData($('#filter')[0]);
			$.ajax({ url:"/plants/plantapi.php?species",
					method:"POST",
					data:fdata,
					processData: false,
					contentType:false,
					success:function(x,y,z)
					{
						var specs = JSON.parse(x);
						$('#speciesSelected').empty();
						$.each(specs,function(i,species){
							$('#speciesSelected').append("<option>"+species['species']+"</option>");
						});
					},
					error:function(x,y,z){
						$('#speciesSelected').empty();
						$('#speciesSelected').append("<option>Error</option>");
					}
			});
			updateThumbs();
		});
		$("#speciesSelected").change(function(){
			updateThumbs();
		});

		$('#onmap').click( updateThumbs);
		$( window ).on( "orientationchange", function( event ) {
	  		$( "#orientation" ).text( "This device is in " + event.orientation + " mode!" );
				ori=event.orientation;
		});
	}

