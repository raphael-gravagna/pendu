<?php

if(empty($_GET["mot"])) {
$mot = $_GET["mot"];
$file = fopen("mots.txt", "a");
fwrite($file,$mot);
fclose($file);
}

else {
$mot = $_GET["mot"]. PHP_EOL;
$file = fopen("mots.txt", "a");
fwrite($file,$mot);
fclose($file);
}

$file = 'mots.txt';

$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $num=>$line)
{
 echo 'Line '.$num.': '.$line.' <a href="./admin.php?suppr='.$line.'">Supprimer</a> </br>';
 
}


if(isset($_GET["suppr"])) {
    $file = 'mots.txt';
    $DELETE = $_GET["suppr"];;
    $data = file("mots.txt");

    $out = array();

    foreach($data as $line) {
        if(trim($line) != $DELETE) {
            $out[] = $line;
        }
    }

    $fp = fopen("mots.txt", "w+");
    flock($fp, LOCK_EX);
    foreach($out as $line) {
        fwrite($fp, $line);
    }
    flock($fp, LOCK_UN);
    fclose($fp);  

}


?>
<form action="admin.php?mot=<?=$mot?>" method="get">
<textarea id="msg" name="mot"></textarea>
<button type="submit">Ajouter le mot</button>
</form>
            <a href="index.php">Retourner dans le jeu</a>

