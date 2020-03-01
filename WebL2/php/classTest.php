<?php

//Classes :

class FormationList {
    public function __construct($contents){ 
		$this->formations = [];
		
		foreach ($contents["records"] as $info) {
			array_push($this->formations, new Formation($info));
		}
	}

    public function printAll(){ 

		foreach ($this->formations as $form) {
		echo "<tr>";
		echo "<td>". $form->diplome_lib ."</td>";
		echo "<td>". $form->sect_disciplinaire_lib ."</td>";
		echo "<td>". $form->etablissement_lib ."</td>";
		echo "<td>". $form->reg_etab_lib ."</td>";
		echo "</tr>";
		}
	}
}


class Formation {

    public function __construct($fields){ 
		$this->diplome_lib = $fields["fields"]["diplome_lib"];
		$this->sect_disciplinaire_lib =  $fields["fields"]["sect_disciplinaire_lib"];
		$this->etablissement_lib = $fields["fields"]["etablissement_lib"];
		$this->reg_etab_lib = $fields["fields"]["reg_etab_lib"];
	}
	
	public $diplome_lib;
	public $sect_disciplinaire_lib;
	public $etablissement_lib;
	public $reg_etab_lib;
}





//Fonctions :


function prt($vars){
	$json =  @json_encode($vars);
	print "<script>console.log($json);</script>";
}



//Code :


$apiKey = "268989ff7daeb79e38a9d650b97a61c9bcf6cdbd5d74a378022416a5";
$conditions = "";
$getPage = false;
$contents = "";
$data = "";
$forms = "";
foreach ($_GET as $key) {
	if(!empty($key)){
		$getPage = true;
		break;
	}
}


if(isset($_GET["etab"]) && $_GET["etab"] != "")
	$conditions = $conditions . "&refine.etablissement_lib=".$_GET["etab"];

if(isset($_GET["secteur"]) && $_GET["secteur"] != "")
	$conditions = $conditions . "&refine.sect_disciplinaire_lib=".$_GET["secteur"];

try{
	$data = file_get_contents(		
		"https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&rows=50&sort=-rentree_lib&facet=etablissement_lib&facet=niveau_lib&facet=diplome_lib&facet=gd_disciscipline_lib&facet=sect_disciplinaire_lib&facet=reg_etab_lib&facet=dep_ins_lib&facet=com_etab_lib&X-API-KEY=".$apiKey."&facet=com_ins".$conditions);

	if($data === false) {
		print("Empty result");
	}else{
		$contents = json_decode($data, true);
		prt($contents );
		$forms = new FormationList($contents);
	}
} catch (Exception $e) {
	print("Erreur de connexion avec le support.");
}
?>




<!DOCTYPE html>
<html lang="en" >
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="./classTest.css">
</head>
<body>



<h2>Search :</h2>

<form action="" method="get">


<?php	

if(!$getPage){

print('<input list="etab_lst" name="etab" placeholder="Etablissement">
	<datalist id="etab_lst">');

	foreach ($contents["facet_groups"][7]["facets"] as $form)
		print('<option value="'.$form["name"].'"></option>');
	

			print('
			</datalist>
			<br>
			<br>
			<input list="secteur_lst" name="secteur" placeholder="Secteur">
			<datalist id="secteur_lst">');


	foreach ($contents["facet_groups"][2]["facets"] as $form)
		print('<option value="'.$form["name"].'"></option>');


print('
	</datalist>	
	<br>
	<br>	
	<input type="submit" value="Submit">');

	
}else{


  print('<input type="submit" value="retour">');
}
  ?>


<input type="button" value="Page principale" onclick="location.href='./../index.html'">


</form> 

<h2>HTML Table</h2>
<table>
  <tr>
    <th>Diplome</th>
    <th>Secteur disciplinaire</th>
    <th>Etablissement</th>
    <th>Region</th>
  </tr>


  <?php
  if(!empty($forms))
	$forms->printAll();

  ?>

</table>

</body>
</html>









