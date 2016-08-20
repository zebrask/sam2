<?php
/*
Cette page r�cupere les informations du signal radio recu par le raspberry PI et effectue une action
en fonction de ces derni�res.

NB : Cette page est appell�e en parametre du programme C 'radioReception', vous pouvez tout � fait
appeller une autre page en renseignant le parametre lors de l'execution du programme C.

@author : Valentin CARRUESCO (idleman@idleman.fr)
@licence : CC by sa (http://creativecommons.org/licenses/by-sa/3.0/fr/)
RadioPi de Valentin CARRUESCO (Idleman) est mis � disposition selon les termes de la 
licence Creative Commons Attribution - Partage dans les M�mes Conditions 3.0 France.
Les autorisations au-del� du champ de cette licence peuvent �tre obtenues � idleman@idleman.fr.
@modif : ONFRAY Matthieu http://onfray.info
*/
require_once("fonctions.php");

//charge la conf de l'utilisateur
$conf_mamaison = charger_conf();
//R�cuperation des parametres du signal sous forme de variables
list($file,$sender,$group,$state,$interruptor) = $_SERVER['argv'];

//mon capteur cr�pusculaire DIO a ces codes...
if ($sender == "9841358" && $interruptor == 9)
{
	$modules = null;
	//$state vaut "off" le soir, "on" le matin
	if ($state == "on") $moment = "l'aube";
	else $moment = "le crepuscule";
	echo "Ici a " . $conf_mamaison["ville_utilisateur"] . ", c'est " . $moment . " le " . date("d/m/Y") . " a " . date("H:m") . "\n";
	echo "Activation des modules suivants : ";
	//parcours des items connus
	foreach($conf_mamaison as $var => $val)
	{
		//recherche le motif "itemX" : si on le trouve pas on passe au motif suivant
		if (! item_valide($var)) continue;
		//sortie si inexistant
		if (! isset($conf_mamaison[$var])) break;
		else $item_cur = $conf_mamaison[$var];
		//si l'item se d�clenche au capteur
		//ouverture � l'aube 
		if (item_on($item_cur) == "capteur-aube" && $state == "on") 
		{
			system("php " . CHEMIN . "activer.php on " . $var);
			$modules .= item_desc($item_cur) . " ";
		}
		//ouverture au cr�puscule
		if (item_on($item_cur) == "capteur-crepuscule" && $state == "off") 
		{
			system("php " . CHEMIN . "activer.php on " . $var);
			$modules .= item_desc($item_cur) . " ";
		}
		//fermeture � l'aube
		if (item_off($item_cur) == "capteur-aube" && $state == "on") 
		{
			system("php " . CHEMIN . "activer.php on " . $var);
			$modules .= item_desc($item_cur) . " ";
		}
		//fermeture au cr�puscule
		if (item_off($item_cur) == "capteur-crepuscule" && $state == "off") 
		{
			system("php " . CHEMIN . "activer.php off " . $var);
			$modules .= item_desc($item_cur) . " ";
		}
	} 
	if (is_null($modules)) echo "aucun\n";
	else echo $modules . "\n";
	echo "debug: php " . CHEMIN . "activer.php on " . $var;
}
?>
