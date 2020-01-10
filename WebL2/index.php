


<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Rechercher ma formation </title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
  <link rel="stylesheet" href="./style.css">

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
  integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
  crossorigin=""/>
  <!-- Make sure you put this AFTER Leaflet's CSS -->
  <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"
  integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew=="
  crossorigin=""></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script><script  src="./script.js"></script>



</head>


<body>
  <div class="background">
    <img src="images/amphi.jpg" alt="Amphi de rentrée pour les élèves de l'Ecole polytechnique">
  </div>


  <div class="mainContent">

    <header>

      <ul>
       <li> <img src="images/book_icon.png" id="logo"  alt="Le logo de la boite">
       </li>
       <li><a class="active" href="#home">Home</a></li>
       <li><a href="#news">News</a></li>
       <li><a href="#contact">Contact</a></li>
       <li class="joinButton"><a href="#about" class="blueButton">Login</a></li>
     </ul>
   </header>

   <article>
    <header>
      <h1 class="pageTitle">Mon avenir ?</h1>
      <h2 class="pageSubTitle">C'est plus facile avec LOGO !</h2>
    </header>
    <section>

      <p>Proin laoreet molestie mi ac mattis. Curabitur velit lorem, euismod eget leo sed, imperdiet ullamcorper mauris. Fusce posuere dapibus eros. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed ullamcorper 
      </p>

      <div class="buttonBar">
        <a href="" class="blueButton">Commencer mes recherches !</a>
      </div>


    </section>
  </article>




  <article>
    <section>


      <p>Rechercher les ecoles :</p><br>


      <form method="post">

       <div class="formline">


        <input list="search_niv" placeholder="Niveau">

        <datalist id="search_niv">

        </datalist>


        <input list="search_sect" placeholder="Secteur">

        <datalist id="search_sect">
        </datalist>


      </div>
      <br>


      <div class="formline">

        <input list="search_reg" placeholder="Region">

        <datalist id="search_reg">

        </datalist>

        <input list="search_dip" placeholder="Diplome">

        <datalist id="search_dip">

        </datalist> 

      </div>
    </form>

    <div class="buttonBar" id="searchButton">
      <a class="blueButton">Rechercher</a>
    </div>



    <div id="mapid">

    </div>




  </section>
</article>
</div>


<script type="text/javascript">



  function AddOptionElement(optionTag, facetToAdd){
    theDiv = document.getElementById(optionTag);
    var option = document.createElement("option");
    option.value = facetToAdd["name"];
    theDiv.appendChild( option);
  }




  var mymap = L.map('mapid').setView([48.835, 2.348], 8);
  L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
    maxZoom: 18,
    id: 'mapbox.streets',
    accessToken: 'pk.eyJ1IjoianVsaWVuNDIwIiwiYSI6ImNrMzV5M21vejBuaW4zbnBlbjY4b205a2wifQ.HlGy-0kBMA6qU3tu-b2tTA'
  }).addTo(mymap);


  mymap.on('click', function(e) {
   AddMapMarker(e.latlng.lat, e.latlng.lng);
 });
  var markerGroup = L.layerGroup().addTo(mymap);


  function AddMapMarker(lat, long){
    L.marker([lat, long], {}).addTo(markerGroup);
  }


  function ClearMinimap(){
 markerGroup.clearLayers();
 }




 $("#searchButton").click(SendRequest);

 SendRequest();


 function SendRequest(){

  var secteur = document.getElementById("search_sect").previousSibling.previousSibling.value;
  var diplome = document.getElementById("search_dip").previousSibling.previousSibling.value;
  var region = document.getElementById("search_reg").previousSibling.previousSibling.value;
  var niveau = document.getElementById("search_niv").previousSibling.previousSibling.value;

  var conditions  = (secteur=="")?"":"&refine.sect_disciplinaire_lib="+secteur;
  conditions += (diplome=="")?"":"&refine.diplome_lib="+diplome;
  conditions += (region =="")?"":"&refine.reg_etab_lib="+region;
  conditions += (niveau =="")?"":"&refine.niveau_lib="+niveau;


  $.getJSON("https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&rows=5&sort=-rentree_lib&facet=etablissement_lib&facet=niveau_lib&facet=diplome_lib&facet=gd_disciscipline_lib&facet=sect_disciplinaire_lib&facet=reg_etab_lib&facet=dep_ins_lib&facet=com_etab_lib&facet=etablissement"+conditions, function(data) {

    ClearMinimap();


    if(data == undefined)
      return;

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
//"etablissement" == 8


var records = data["records"]; 



let searchById = (records.length > 0)?"&q="+records[0]:"";

for (var i=1 ; i <  records.length ; i++)
{
  searchById += " OR " + records[i]["fields"]["etablissement"];
  }


console.log(data);
console.log("https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-etablissements-enseignement-superieur&q=0062143X+OR+0171463Y");


  $.getJSON("https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-etablissements-enseignement-superieur"+searchById, function(etablissements) {

console.log(etablissements);
console.log(etablissements);


});







});

}




</script>

</body>

</html>