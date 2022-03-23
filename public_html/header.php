<?php
//	include "session.php";
//	$_SESSION['started']=true;
?>
<header>
	<div id="me" class="headerpic">
		<img class="headerpic" src="/staticimg/me.jpg"/>
	</div>
	<div id="garden" class="headerpic">
		<img class="headerpic" src="/staticimg/garden.jpg" />
	</div>
	<div id="title">
			<img id="titleimg" src="/staticimg/guildford.jpg"/>
			<h1 id="intro">Kens Pub</h1>
			<h2 id="pagetitle"></h2>
			<a id="login-button" ms-hide-element="true" href="/login" class="button logout login w-button">
<?php
	if(isset($_SESSION['name'])){
		echo $_SESSION['name'];
	}else{
		echo "Login";
	}
?>
			</a>
	</div>
	<script>
		$(function() {
			$("nav#menu ul").menu({
				select:function(event, ui){
					var div = ui.item.find("div");
					var id = $(div[0]).attr("id");
					var hrf = "/";
					switch(id){
						case "m_home":
							hrf="/index";
							break;
						case "m_blog":
							hrf="/wp";
							break;
						case "m_plants":
							hrf="/plants/index";
							break;
						case "m_allotment":
							hrf="/allotment";
							break;
						case "m_moles":
							hrf="/allotment/moles";
							break;
						case "m_cv":
							hrf="/cvs/cv.html";
							break;
						case "m_projects":
							hrf="/projects";
							break;
						case "m_aws_iot":
							hrf="/projects/aws";
							break;
						case "m_overpass":
							hrf="/projects/overpass";
							break;
					}
					window.location.href=hrf;
				}
			});
		});
	</script>
	<nav id="menu" class="navbar navbar-expand-lg navbar-light bg-light">
		<ul>
			<li><div id="m_home">Home<span class="ui-icon ui-icon-home"></span></div></li>
			<li><div id="m_blog" >Blog</div></li>
			<li><div id="m_plants">Plants</div></li>
			<li>
				<div id="m_allotment">Allotment</div>
				<ul>
					<li class="ui-state-disabled"><div>Sections</div></li>
					<li><div id="m_moles">Moles</div></li>
				</ul>
			</li>
			<li>
				<div id="m_projects">Projects</div>
				<ul>
					<li class="ui-state-disabled"><div>Sections</div></li>
					<li><div id="m_aws_iot">AWS IOT</div></li>
					<li><div id="m_overpass">SVG Mapping</div></li>
				</ul>
			</li>
			<li><div id="m_cv">CV</div></li>
		</ul>
	</nav>

</header>

