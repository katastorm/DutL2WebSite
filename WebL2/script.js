

let apiKey = "268989ff7daeb79e38a9d650b97a61c9bcf6cdbd5d74a378022416a5";


$(window).scroll(function(e){
	parallax();
});

function parallax(){
	var scrolled = $(window).scrollTop();
	$('.background').css('top',-(scrolled*0.15)+'px');
}



var appInit = false;

$(document).ready(function(){

/*
	mymap.on('click', function(e) {
	 AddMapMarker(e.latlng.lat, e.latlng.lng);
	});*/

	//Partie carte :
	var mymap = L.map('mapid').setView([48.835, 2.348], 8);
	L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
		attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
		maxZoom: 18,
		id: 'mapbox.streets',
		accessToken: 'pk.eyJ1IjoianVsaWVuNDIwIiwiYSI6ImNrMzV5M21vejBuaW4zbnBlbjY4b205a2wifQ.HlGy-0kBMA6qU3tu-b2tTA'
	}).addTo(mymap);

	var markerGroup = L.layerGroup().addTo(mymap);

//Utilitaires pour la carte
function AddMapMarker(lat, long, fields){
	var marker =	L.marker([lat, long], {}).addTo(markerGroup);


	var space = "<br><br>";
	var name = fields["uo_lib"] + "<br>" +
		"<a target=\"_blank\" href="+fields["url"]+">Acceder au site web</a>"  + space;	
	var adresse = (fields["adresse_uai"] == undefined)?"":"Adresse:"+fields["adresse_uai"] + space;
	var tel = (fields["numero_telephone_uai"] == undefined)?"":"Téléphone:"+fields["numero_telephone_uai"] + space;
	var wiki = "Page wikidata<br>" +
				"<a target=\"_blank\" href="+fields["element_wikidata"]+">"+fields["element_wikidata"] + space;


	marker.bindPopup(name + adresse + tel + wiki);	
	marker.on('click', onClickMarker);
}

function ClearMinimap(){
	markerGroup.clearLayers();
}

function onClickMarker(e) {
	var popup = e.target.getPopup();
	var content = popup.getContent();
	//console.log(content);
}





	//Transition du boutton "Commencer mes recherches"
	$("#searchButton").click(() => SendRequest(50) );
	$('#searchZone').fadeOut(0);
	$('#openSearchPannel').click(function(e){    
		$('#beforeSearch').fadeOut('fast', function(){
       // $('.searchZone').replace('<div id="beforeSearch"></div>').fadeIn('fast');
       $('#searchZone').fadeIn('fast');
   });
	});

	//Remplissage des choix des champs
	function AddOptionElement(optionTag, facetToAdd){
		theDiv = document.getElementById(optionTag);
		var option = document.createElement("option");
		option.value = facetToAdd["name"];
		theDiv.appendChild( option);
}

let resultTextCnt = document.getElementById("result_cnt");
let moreResultText = document.getElementById("more_results");
$("#more_results").click(() => SendRequest(250) );




//Automatique, au démarage
SendRequest(50);


function SendRequest(maxResultCnt){
//Recuperation des critères de recherche
var secteur = document.getElementById("search_sect").previousSibling.previousSibling.value;
var diplome = document.getElementById("search_dip").previousSibling.previousSibling.value;
var region = document.getElementById("search_reg").previousSibling.previousSibling.value;
var niveau = document.getElementById("search_niv").previousSibling.previousSibling.value;

var conditions  = (secteur=="")?"":"&refine.sect_disciplinaire_lib="+secteur;
conditions += (diplome=="")?"":"&refine.diplome_lib="+diplome;
conditions += (region =="")?"":"&refine.reg_etab_lib="+region;
conditions += (niveau =="")?"":"&refine.niveau_lib="+niveau;

//Envoie de la première requète
$.getJSON("https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&X-API-KEY="+apiKey+"&rows="+maxResultCnt+"&sort=-rentree_lib&facet=etablissement_lib&facet=niveau_lib&facet=diplome_lib&facet=gd_disciscipline_lib&facet=sect_disciplinaire_lib&facet=reg_etab_lib&facet=dep_ins_lib&facet=com_etab_lib&facet=com_etab"+conditions, function(data) {

	ClearMinimap();

		//En cas d'erreur
		if(data == undefined)
			return;

		//Aucun résultats		
		if(data["nhits"] == 0){
			alert("Aucun résultat trouvé");
			return;
		}



//Remplissage des champs automatiques (à faire seulement 1 fois au démarage)
if(!appInit){
	appInit = true;

//Trier les résultats
for (var i = 0; i < data["facet_groups"].length; i++) {
	data["facet_groups"][i]["facets"].sort(function(a,b){
		return a["name"].localeCompare(b["name"]);
	});	
}

//"niveau_lib"
jQuery.each(data["facet_groups"][6]["facets"], function() {
	AddOptionElement("search_niv", this);
});
//"reg_etab_lib"
jQuery.each(data["facet_groups"][5]["facets"], function() {
	AddOptionElement("search_reg", this);
});
//"diplome_lib"
jQuery.each(data["facet_groups"][7]["facets"], function() {
	AddOptionElement("search_dip", this);
});
//"sect_disciplinaire_lib"
jQuery.each(data["facet_groups"][2]["facets"], function() {
	AddOptionElement("search_sect", this);
});
//"com_etab" == 8
}



//creation de la requete correllé pour avoir la géolocalisation de l'ecole
var records = data["records"]; 
let searchById = (records.length > 0)?"&q="+records[0]["fields"]["com_etab"]:"";
for (var i=1 ; i <  records.length ; i++){
	searchById += " OR " + records[i]["fields"]["com_etab"];
}


moreResultText.hidden =  records.length < maxResultCnt;
//console.log( records.length + " vs " + maxResultCnt);

//Envoie de la seconde requête et placement des marqueurs
$.getJSON("https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-etablissements-enseignement-superieur&X-API-KEY="+apiKey+"&rows="+maxResultCnt+searchById, function(etablissements) {
	records = etablissements["records"]; 
	for (var i=0 ; i <  records.length ; i++)
	{		
		let pos = records[i]["geometry"]["coordinates"];
		//console.log(records[i]);
		AddMapMarker(pos[1], pos[0], records[i]["fields"]);
	}
	resultTextCnt.innerHTML = records.length;
            
});
});
}
});

