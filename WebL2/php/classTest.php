<?php

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


function sysout ($data){
	$inf = json_encode($data);
	print("<script>console.log(" . $inf . ");</script>");
}
	


	$data = file_get_contents("https://data.enseignementsup-recherche.gouv.fr/api/records/1.0/search/?dataset=fr-esr-principaux-diplomes-et-formations-prepares-etablissements-publics&rows=15&sort=-rentree_lib&facet=etablissement_lib&facet=niveau_lib&facet=diplome_lib&facet=gd_disciscipline_lib&facet=sect_disciplinaire_lib&facet=reg_etab_lib&facet=dep_ins_lib&facet=com_etab_lib&facet=com_ins");
	
	if($data === false) {
		print("Empty result");
	}

	$contents = json_decode($data, true);

?>

<!DOCTYPE html>
<html lang="en" >
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="./classTest.css">
</head>
<body>

<h2>HTML Table</h2>

<table>
  <tr>
    <th>Diplome</th>
    <th>Secteur disciplinaire</th>
    <th>Etablissement</th>
    <th>Region</th>
  </tr>

  <?php

         foreach ($contents["records"] as $info) {


			$form = new Formation($info);

		sysout($info);

echo "<tr>";
echo "<td>". $form->diplome_lib ."</td>";
echo "<td>". $form->sect_disciplinaire_lib ."</td>";
echo "<td>". $form->etablissement_lib ."</td>";
echo "<td>". $form->reg_etab_lib ."</td>";
echo "</tr>";
			}
  ?>

</table>

</body>
</html>









