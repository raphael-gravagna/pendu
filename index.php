<?php


session_start();

$letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
$WON = false;

// Toutes les partie du corps de pendu
$bodyParts = ["sanstete","tete","corp","maingauche","maindroite","jambes"];


// Mots aléatoires (les récupérer depuis le fichier mots.txt)
// $txt_file = 'mots.txt';

// $lines = file($txt_file);
// foreach ($lines as $num=>$line)
// {
//  echo 'Line '.$num.': '.$line.'<br/>';
// }
// $derniermot = (count($lines));

// rand(1,$derniermot);

// $num_mot = rand(1,$derniermot - 1);

// echo $lines[$num_mot];


// $words = $lines[$num_mot];

//renplissage des mots dans un  tavleau
$words = [];
$gestion = fopen("mots.txt", "r");
if ($gestion)
{
   while (!feof($gestion))
   {
        $charge = fgets($gestion, 4096);
        $mot = trim($charge);
        $words[] = $mot;
    }
     
}
fclose($gestion);



function getCurrentPicture($part){
    return "./images/pendu_". $part. ".png";
}


function startGame(){
   
}

// Restart du jeu: Nettoyage des sessions
function restartGame(){
    session_destroy();
    session_start();

}

// Récupère les parties de pendu 
function getParts(){
    global $bodyParts;
    return isset($_SESSION["parts"]) ? $_SESSION["parts"] : $bodyParts;
}

// Ajout des parties 
function addPart(){
    $parts = getParts();
    array_shift($parts);
    $_SESSION["parts"] = $parts;
}

// Récupération des partie actuelles du pendu
function getCurrentPart(){
    $parts = getParts();
    return $parts[0];
}

// recupération du mot actuel 
function getCurrentWord(){
    global $words;
    if(!isset($_SESSION["word"]) && empty($_SESSION["word"])){
        $key = array_rand($words);
        $_SESSION["word"] = $words[$key];
        //var_dump($words[$key]);
    }
    return $_SESSION["word"];
    
}


// recup reponses de l'utilisateur
function getCurrentResponses(){
    return isset($_SESSION["responses"]) ? $_SESSION["responses"] : [];
}

function addResponse($letter){
    $responses = getCurrentResponses();
    array_push($responses, $letter);
    $_SESSION["responses"] = $responses;
}

// Vérification si la lettre est juste 
function isLetterCorrect($letter){
    $word = getCurrentWord();
    $max = strlen($word) - 1;
    for($i=0; $i<= $max; $i++){
        if($letter == $word[$i]){
            return true;
        }
    }
    return false;
}

// verification si le mot a découvrir est correct en fonction des action joueur

function isWordCorrect(){
    $guess = getCurrentWord();
    $responses = getCurrentResponses();
    $max = strlen($guess) - 1;
    for($i=0; $i<= $max; $i++){
        if(!in_array($guess[$i],  $responses)){
            return false;
            var_dump($guess[$i]);
        }
    }
   
    return true;
}

// preparation affichage pendu

function isBodyComplete(){
    $parts = getParts();
    // iveryfication de quel partie du corps est affiché
    if(count($parts) <= 1){
        return true;
    }
    return false;
}


// si le jeu est finis
function gameComplete(){
    return isset($_SESSION["gamecomplete"]) ? $_SESSION["gamecomplete"] :false;
}


// set jeufinis
function markGameAsComplete(){
    $_SESSION["gamecomplete"] = true;
}

// lancement nouvelle partie
function markGameAsNew(){
    $_SESSION["gamecomplete"] = false;
}



// restart de la game par pression du btn restart
if(isset($_GET['start'])){
    restartGame();
}


/* détection quadn on clique sur une lettre*/
if(isset($_GET['kp'])){
    $currentPressedKey = isset($_GET['kp']) ? $_GET['kp'] : null;
    // si la lettre est juste
    if($currentPressedKey 
    && isLetterCorrect($currentPressedKey)
    && !isBodyComplete()
    && !gameComplete()){
        
        addResponse($currentPressedKey);
        if(isWordCorrect()){
            $WON = true; // jeu gagné
            markGameAsComplete();
        }
    }else{
        // sinon on commence a pendre
        if(!isBodyComplete()){
           addPart(); 
           if(isBodyComplete()){
               markGameAsComplete(); 
           }
        }else{
            markGameAsComplete(); 
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pendu officiel</title>
</head>
    <body style="background: deepskyblue">
        
        <div style="margin: 0 auto; background: #dddddd; width:900px; height:900px; padding:5px; border-radius:3px;">
            
            <div style="display:inline-block; width: 500px; background:#fff;">
                 <img style="width:80%; display:inline-block;" src="<?php echo getCurrentPicture(getCurrentPart());?>"/>
          
              
               <?Php if(gameComplete()):?>
                    <h1>Jeu terminé</h1>
                <?php endif;?>
                <?php if($WON  && gameComplete()):?>
                    <p style="color: darkgreen; font-size: 25px;">Vous avez gagné, Félicitation </p>
                <?php elseif(!$WON  && gameComplete()): ?>
                    <p style="color: darkred; font-size: 25px;">Vous avez Perdu, CHEH </p>
                <?php endif;?>
            </div>
            
            <div style="float:right; display:inline; vertical-align:top;">
                <h1>Pendu officiel</h1>
                <div style="display:inline-block;">
                    <form method="get">
                    <?php
                        $max = strlen($letters) - 1;
                        for($i=0; $i<= $max; $i++){
                            echo "<button type='submit' name='kp' value='". $letters[$i] . "'>".
                            $letters[$i] . "</button>";
                            if ($i % 7 == 0 && $i>0) {
                               echo '<br>';
                            }
                            
                        }
                    ?>
                    <br><br>
                    <!-- Restart game button -->
                    <button type="submit" name="start">Restart Game</button>
                    </form>
                </div>
            </div>
            
            <div style="margin-top:20px; padding:15px; background: lightseagreen; color: #fcf8e3">
                <!-- Display the current guesses -->
                <?php 
                 $guess = getCurrentWord();
                 $maxLetters = strlen($guess) - 1;
                for($j=0; $j<= $maxLetters; $j++): $l = getCurrentWord()[$j];?>
                    <?php if(in_array($l, getCurrentResponses())): ?>
                        <span style="font-size: 35px; border-bottom: 3px solid #000; margin-right: 5px;"><?php echo $l;?></span>
                    <?php  else: ?>
                        <span style="font-size: 35px; border-bottom: 3px solid #000; margin-right: 5px;">&nbsp;&nbsp;&nbsp;</span>
                    <?php endif;?>
                <?php endfor;?>
            </div>
            
        </div>
        
        
        
    </body>
    
    
</html>