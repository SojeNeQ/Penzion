<?php
    $idStranky = "";
    
    //zde jsme odstranili $seznam stranek a presunuli do data.php
    require_once "./data.php";


    //http://localhost/david/2023-02-07/primapenzion/index.php?id=galerie
    //podivame se do url a zjistime zda tam neni parametr "id" 
    if (array_key_exists("id", $_GET)) {
        $idStranky = $_GET["id"];

        //pokud id stranky neexistuje v seznamuStranek, tak nastabime id jako 404
        if (!array_key_exists($idStranky, $seznamStranek)) {
            $idStranky = "404";
        }

    }else{//pokud tam parametr id neni tak automaticky zvolime stranku ktera je na prvnim miste

        //funkce array_keys vrati pole vsech klicu
        //my vebereme tne prvni
        $idStranky = array_keys($seznamStranek)[0];
    }
?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $seznamStranek[$idStranky]->getTitulek(); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">

</head>

<body>

    <header>
        <div class="container">

            <div class="headerTop">
                <a class="tel" href="tel:+420606123456">+420 / 606 123 456</a>
                <div class="socIkony">
                    <a href="#" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" target="_blank"><i class="fa-brands fa-twitter"></i></a>
                </div>
            </div>
            <a class="logo" href="domu">Prima<br>Penzion</a>

            <div class="menu">
                <ul>
                    <?php
                    foreach ($seznamStranek AS $id => $instance) {
                        if ($instance->getMenu() != "") {
                            echo "<li><a href='$id'>{$instance->getMenu()}</a></li>";
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>

        <img src="img/<?php echo $seznamStranek[$idStranky]->getObrazek(); ?>" alt="Prima Penzion">
    </header>

    <?php
        //novy zpusob
        echo $seznamStranek[$idStranky]->getObsah();

        //stary zpusob
        //sem na toto misto pak bueme pripojovat soubory html
        //require_once "./$idStranky.html";
    ?>
    
    <footer>
        <div class="pata">

            <div class="menu">
                <ul>
                    <?php
                    foreach ($seznamStranek AS $id => $instance) {
                        if ($instance->getMenu() != "") {
                            echo "<li><a href='$id'>{$instance->getMenu()}</a></li>";
                        }
                    }
                    ?>
                </ul>
            </div>

            <a class="logo" href="domu">Prima<br>Penzion</a>

            <div class="pataInfo">
                <p>
                    <i class="fa-solid fa-map-pin"></i>
                    <a href="https://goo.gl/maps/Uiw7tu1bhMrvjrn66" target="_blank"> <b>PrimaPenzion</b>, Jablonského 2,
                        Praha 7</a>
                </p>
                <p>
                    <i class="fa-solid fa-phone fa-rotate-180"></i>
                    <a class="tel" href="tel:+420606123456">+420 / 606 123 456</a>
                </p>
                <p>
                    <i class="fa-regular fa-envelope"></i>
                    <strong> info@primapenzion.cz</strong>
                </p>
            </div>

            <div class="socIkony">
                <a href="#" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" target="_blank"><i class="fa-brands fa-twitter"></i></a>
            </div>


        </div>

        <div class="copy">
            &copy;<b>PrimaPenzion</b> 2023
        </div>

    </footer>

</body>

</html>