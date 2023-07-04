<?php

// decrypt cookie
if(isset($_COOKIE['guessIt'])){
    $decryptedGuessIt = openssl_decrypt($_COOKIE['guessIt'],'aes-128-cbc','gogetit',0,'go12345678910111'); // just a simple encryption
    // $decryptedTimes = openssl_decrypt($_COOKIE['times'],'aes-128-cbc','gogetit',0,'go12345678910111'); // it is inactive yet

// check if form have value
if(isset($_POST['input']))
    $input = (int)$_POST['input']; //alter of htmlentities()
$ans = array('it is bigger, lower it','it is smaller, increase it','What!! You Cheated!','You Lost');

// if both number to guess and input are valid
if(!empty($input) && isset($_COOKIE['guessIt']))
    switch ($input){
        case $input > $decryptedGuessIt:
            $turn = 0; // it is bigger
            setcookie('times',--$_COOKIE['times']);
            break;
        case $input < $decryptedGuessIt:
            $turn = 1; // it is smaller
            setcookie('times',--$_COOKIE['times']);
            break;
        case $input == $decryptedGuessIt:
            $turn = 2; // you won
            $_COOKIE['times'] = 0;
            break;
    }

// check losing condition : do if left turns is set AND result != you won AND left times != 0
if(isset($turn) && $turn != 2 && $_COOKIE['times'] == 0)
    $turn = 3; // you lost

// check new game condition : do if left times is not set OR guessIt is not set OR left times == 0
if(!isset($_COOKIE['times']) || !isset($_COOKIE['guessIt']) || 0 == $_COOKIE['times']){
    $guessIt = rand(1,100); // between 1 to 100

    // initial cookie encryption
    $guessIt = openssl_encrypt($guessIt,'aes-128-cbc','gogetit',0,'go12345678910111');

    setcookie("guessIt", $guessIt , time() + 3600, '/'); // 1 hour
    setcookie('times', $_COOKIE['times'] = 10 , time() + 3600, '/');
    $display = 'disabled';
    $message = 'New Game';
}else{
    $display = '';
    $message = 'Guess';}
?>
<!doctype html>
<html>
<head>
    <title>GuessIt</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @font-face {
            font-family: cr;
            src: local(./fonts/Caprasimo-Regular.tff);
        }

        #showTurn {
            font-family: cr;
        }
    </style>
</head>
<body class="flex justify-center bg-blue-400" style="position: fixed;bottom: 0;top: 0;right: 0;left: 0;">
<div class="flex justify-center flex-col h-full">
    <form method="POST" action="" class="flex justify-center flex-col items-center">
        <input class="bg-transparent border-b-2" style="width:300px" type="text" name="input" placeholder="" <?php echo $display ?>>
        <input type="submit" class="font-sarif text-white" value="<?php echo $message // show $msg = you won/you lost/etc ?>">
    </form>
    <p class="flex text-white font-mono justify-center mt-5" <?php if(!isset($turn)) echo 'style="visibility:hidden"' ?>>
        <?php if(isset($turn)) echo  $ans[$turn]; else echo "0" // default value to keep fixed space between guess btn and left turns in association with visibility:hidden  line 65 ?>
    </p>
    <p id="showTurn" class="text-white font-bold text-9xl flex justify-center mt-10">
        <?php if(isset($_COOKIE['times'])) echo  $_COOKIE['times'] // show left times to try ?>
    </p>
</div>
</body>
</html>
