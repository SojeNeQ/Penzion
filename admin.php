<?php
    session_start();
    require_once "./data.php";

    //toto je zpracovani formulare pro prihlaseni
    if (array_key_exists("login-submit", $_POST)) {
        $zadaneJmeno = $_POST["jmeno"];
        $zadaneHeslo = $_POST["heslo"];

        if ($zadaneJmeno == "admin" && $zadaneHeslo == "kocka123") {
            //oke, zadal udaje spravne prihlasime ho, tak ze mu v sessne udelame nejakou hodnotu podle ktere s ebudeme rozhodovat jestli prihlaseny je nebo ne
            $_SESSION["jePrihlasen"] = true;
        }
    }

    //toto je zpracovani formualre pro odhlaseni
    if (array_key_exists("logout-submit", $_GET)) {
        unset($_SESSION["jePrihlasen"]);
        header("Location: ?");
    }

    //toto je kontorola zda je uzivatel prihlasen a ma pravo stranky editovat, pridavat a mazat
    if (array_key_exists("jePrihlasen", $_SESSION)) {
        //uzivatel chce zacit editovat stranku
        if (array_key_exists("edit", $_GET)) {
            //vytahneme si z url id stranky
            $idStranky = $_GET["edit"];

            //sahneme si do promenne $đeznam stranek pro nasi konkretni instanci
            $aktivniInstance = $seznamStranek[$idStranky];
            //var_dump($aktivniInstance);
        }

        //uzvivatel chce ulozit stranku
        if (array_key_exists("aktualizovat-submit", $_POST)) {
            $idStranky = trim($_POST["id-stranky"]); //odstranime mezery pred a za stringem
            $titulekStranky = $_POST["titulek-stranky"];
            $menuStranky = $_POST["menu-stranky"];
            $obrazekStranky = $_POST["obrazek-stranky"];

            if ($idStranky == "") {
                //zjsitili jsme ze id je prazdne
                //nechceme tedy nic ukladat do databaze
                header("Location: ?add"); //presmeurjeme ho zpet na editovani nove stranky
                exit; //rika, ze se uz nema dal generovat zbytek php
            }

            //pokud aktivni instance neexistuje tak to zanemna ze se jedna o uplne novou stranku
            if (!isset($aktivniInstance)) {
                $aktivniInstance = new Stranka ("", "", "", "");
            } 

            //nastavime tyto data do vlastnoti instance
            $aktivniInstance->setId($idStranky);
            $aktivniInstance->setTitulek($titulekStranky);
            $aktivniInstance->setMenu($menuStranky);
            $aktivniInstance->setObrazek($obrazekStranky);
            //po tom co nastavime vsechyn vlastnosti  tak zapiseme cleou instanci do DB
            $aktivniInstance->zapisDoDb();

            //zde zapiseme obsah do DB
            $obsahWysiwygu = $_POST["obsah-stranky"];
            $aktivniInstance->setObsah($obsahWysiwygu);

            //presmerujeme uzivatele na spravnou url
            //pokud se zmenilo id tak v url je furt to stare ?edit=stare-id
            header("Location: ?edit=$idStranky");
        }

        //uzivatel chce pridat novou stranku
        if (array_key_exists("add", $_GET)) {
            $aktivniInstance = new Stranka ("", "", "", "");
        }

        //uzivatel chce smazat stranku
        if (array_key_exists("delete", $_GET)) {
            $idStranky = $_GET["delete"];
            $instanceKterouChcemeSmazat = $seznamStranek[$idStranky];
            $instanceKterouChcemeSmazat->smazSe();
            header("Location: ?");
            exit;
        }


    }
    

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin sekce</title>
</head>
<body>
    <h1>Admin sekce</h1>

    <?php

        if (array_key_exists("jePrihlasen", $_SESSION)) {
            echo "Uspesne jste se prihlasili";
            ?>
            <!-- pokud udelame ohlasovni formulare pomoci metody get, tak je mozne toto nasimulovat i pomoci odkazu-->
            <a href="?logout-submit">Odkaz pro odhlasovani</a>
            
            <form action="" method="get">
                <input type="submit" name="logout-submit" value="Odhlasit se">
            </form>

            <!-- kdyz uzviatel na tento odkaz kline tak je to stejne jako by odeslal GET formular s klicem add -->
            <a href="?add">Pridat novou stranku</a>

            <?php
            //vypis seznamu stranek
            echo "<ul>";
            foreach ($seznamStranek AS $id => $instance) {
                echo "<li><a href='?edit={$instance->getId()}'>{$instance->getId()}</a> <a href='?delete={$instance->getId()}'>SMAZAT</a></li> ";
                echo "<hr>";
            }
            echo "</ul>";

            if (isset($aktivniInstance)) {
                ?>
                    <form action="" method="post">
                        <label for="kocka">Id</label>
                        <input type="text" name="id-stranky" id="kocka" value="<?php echo $aktivniInstance->getId(); ?>">
                        <hr>
                        <label for="slon">Titulek</label>
                        <input type="text" name="titulek-stranky" id="slon" value="<?php echo $aktivniInstance->getTitulek(); ?>">
                        <hr>
                        <label for="panda">Menu</label>
                        <input type="text" name="menu-stranky" id="panda" value="<?php echo $aktivniInstance->getMenu(); ?>">
                        <hr>
                        <label for="pstros">Obrazek</label>
                        <input type="text" name="obrazek-stranky" id="pstros" value="<?php echo $aktivniInstance->getObrazek(); ?>">
                        <hr>
                        <label for="klokan">Obsah stranky</label>
                        <textarea name="obsah-stranky" id="klokan" cols="30" rows="10"><?php echo htmlspecialchars($aktivniInstance->getObsah());?></textarea>
                        <input type="submit" name="aktualizovat-submit" value="Aktualizovat web">
                    </form>
                    <!-- pripojime knihovnu tinymce -->
                    <script src="./vendor/tinymce/tinymce/tinymce.min.js"></script>
                    <!-- aktivovat knihovnu -->
                    <script>
                        tinymce.init({
                            selector: "#klokan",
                            language: "cs",
                            language_url: "<?php echo dirname($_SERVER["PHP_SELF"]); ?>/vendor",
                            entity_encoding: "raw",
                            verify_html: false,
                            content_css: ["<?php echo dirname($_SERVER["PHP_SELF"]); ?>/css/style.css", "<?php echo dirname($_SERVER["PHP_SELF"]); ?>/css/all.min.css"],
                            body_id: "obsah",
                            plugins:["code", "responsivefilemanager", "image", "anchor", "autolink", "autoresize", "link", "media", "lists"],
                            toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat',
                            toolbar2: "| responsivefilemanager | link unlink anchor | image media | forecolor backcolor  | print preview code ",
                            external_plugins: {
                                'responsivefilemanager': '<?php echo dirname($_SERVER['PHP_SELF']); ?>/vendor/primakurzy/responsivefilemanager/tinymce/plugins/responsivefilemanager/plugin.min.js',
                            },
                            external_filemanager_path: "<?php echo dirname($_SERVER['PHP_SELF']); ?>/vendor/primakurzy/responsivefilemanager/filemanager/",
                            filemanager_title: "File manager",
                        });
                    </script>

                <?php
            }
        }else{
            ?>
            <form action="" method="post">
                <label for="koala">Prihlasovaci jmeno</label>
                <input type="text" name="jmeno" id="koala">
                <hr>
                <label for="mravenec">Prihlasovaci heslo</label>
                <input type="password" name="heslo" id="mravenec">
                <hr>
                <input type="submit" name="login-submit" value="Prihlasit se">
            </form>
            <?php
        }

    ?>
</body>
</html>