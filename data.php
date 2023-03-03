<?php
$instanceDatabaze = new PDO(
    "mysql:host=localhost;dbname=penzion;charset=utf8",
    "root",
    "root",
    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
);

class Stranka {
    private $id;
    private $titulek;
    private $menu;
    private $obrazek;
    private $oldId = "";

    function __construct($argId, $argTitulek, $argMenu, $argObrazek)
    {
        $this->id = $argId;
        $this->titulek = $argTitulek;
        $this->menu = $argMenu;
        $this->obrazek = $argObrazek;
    }

    //toto je getter a pouziva se k tomu abychom mohli vypsat privatni nebo protected vlastnost
    function getTitulek () {
        return $this->titulek;
    }

    function getMenu () {
        return $this->menu;
    }

    function getObrazek() {
        return $this->obrazek;
    }

    function getId() {
        return $this->id;
    }

    function getObsah() {
        $idStranky = $this->id;
        //protoze jsme uvnitr classy, tak nemame moznost použí promenou $instanceDatabaze, protože ona je nadefinovana venku, jediny zpusob jak pouzit promenou nadefinovanou mimo classu je pouzi pole $GLOBALS
        $query = $GLOBALS["instanceDatabaze"]->prepare("SELECT * FROM stranka WHERE id=?");
        $query->execute([$idStranky]);

        //pokud nas prikaz nenasel zadnou stranku v DB, tak to vrati NULL
        $dataStranky = $query->fetch(); //pokud chcemz datbaze vytahnout pouze 1 vysledek, pouzijeme fetch misto fetchAll
        
        //titmo ifem zkontorlujeme zda v databazi stranka existuje
        if ($dataStranky != NULL) {
            //pokud stranka existuje tak vraitme obsah z DB
            return $dataStranky["obsah"];
        }else{
            //pokud stranka neexistuje vratime prazdny string
            return "";
        }
        


        /* toto byl stary zpusob kdy jsme stranky meli ulozeny v souborech
        $idStranky = $this->id;
        $obsahSouboru = file_get_contents("./$idStranky.html");
        return $obsahSouboru;
        */
    }

    function setObsah ($argNovyObsah) {
        $idStranky = $this->id;
        $query = $GLOBALS["instanceDatabaze"]->prepare("UPDATE stranka SET obsah=? WHERE id=?");
        $query->execute([$argNovyObsah, $idStranky]);
        //pri updatu nemusime nic fetchovat


        /* stary zpusob s html soubory
        $idStranky = $this->id;
        file_put_contents("./$idStranky.html", $argNovyObsah);
        */ 
    }

    function setId($argNoveId) {
        //prorotze id je primarni klic, tak nez ho prepiseme za novy, tak je treba si to stare ulozit nekam bokem
        $this->oldId = $this->id;
        $this->id = $argNoveId;
    }

    function setTitulek ($argNovyTitulek) {
        $this->titulek = $argNovyTitulek;
    }

    function setMenu ($argNoveMenu) {
        $this->menu = $argNoveMenu;
    }

    function setObrazek ($argNovyObrazek) {
        $this->obrazek = $argNovyObrazek;
    }

    function zapisDoDb () {
        //zde udelame update do DB
        if ($this->oldId == "") {
            //nejprve zjistime jaka hodnota poradi je v DB nejvyssi
            $query = $GLOBALS["instanceDatabaze"]->prepare("SELECT * FROM stranka ORDER BY poradi DESC LIMIT 1");
            //titmo zpusobem taky dokazeme zjistit poradi
            //$query = $GLOBALS["instanceDatabaze"]->prepare("SELECT MAX(poradi) AS max_hodnota FROM stranka");
            $query->execute();
            $strankaSNejvyssimPoradi = $query->fetch();
            
            //pokud zadan astranka v DB jeste neni, tak nastavime prvni strance poradi 1
            if ($strankaSNejvyssimPoradi == NULL) {
                $nejvyssiPoradi = 1;
            }else{
                //pokud stranka existuje 
                //tak vezmeme jeji poradi a pro novu stranku to zvedneme o 1 nahoru
                $nejvyssiPoradi = $strankaSNejvyssimPoradi["poradi"];
                $nejvyssiPoradi++;
            }
            


            //prikaz pro vlozeni noveho zazanmu do DB
            $query = $GLOBALS["instanceDatabaze"]->prepare("INSERT INTO stranka SET id=?, titulek=?, menu=?, obrazek=?, poradi=?");
            $query->execute([$this->id, $this->titulek, $this->menu, $this->obrazek, $nejvyssiPoradi]);
        }else{
            $query = $GLOBALS["instanceDatabaze"]->prepare("UPDATE stranka SET id=?, titulek=?, menu=?, obrazek=? WHERE id=?");
            $query->execute([$this->id, $this->titulek, $this->menu, $this->obrazek, $this->oldId]);
        }
    }

    function smazSe() {
        $query = $GLOBALS["instanceDatabaze"]->prepare("DELETE FROM stranka WHERE id=?");
        $query->execute([$this->id]);
    }

}

//nyni vytvorime seznam stranke podle toho co je ulozene v DB
$seznamStranek = [];
$query = $instanceDatabaze->prepare("SELECT * FROM stranka ORDER BY poradi");
$query->execute();
$poleStranek = $query->fetchAll();

foreach ($poleStranek AS $stranka) {
    $seznamStranek[$stranka["id"]] = new Stranka($stranka["id"], $stranka["titulek"], $stranka["menu"], $stranka["obrazek"]);
}

//pole instanci objektu
/*
$seznamStranek = [
    "domu" => new Stranka("domu", "PrimaPenzion", "Domů", "primapenzion-main.jpg"),
    "galerie" => new Stranka("galerie", "Fotogalerie", "Foto", "primapenzion-pool-min.jpg"),
    "rezervace" => new Stranka("rezervace", "Rezervace", "Chci pokoj", "primapenzion-room.jpg"),
    "kontakt" => new Stranka("kontakt", "Kontakt", "Kontakt", "primapenzion-room2.jpg")
];
*/

//toto je pole poli
/*
$seznamStranek = [
    "domu" => [
        "id" => "domu",
        "titulek" => "PrimaPenzion",
        "menu" => "Domů",
        "obrazek" => "primapenzion-main.jpg"
    ],
    "galerie" => [
        "id" => "galerie",
        "titulek" => "Fotogalerie",
        "menu" => "Foto",
        "obrazek" => "primapenzion-pool-min.jpg"
    ],
    "rezervace" => [
        "id" => "rezervace",
        "titulek" => "Rezervace",
        "menu" => "Chci pokoj",
        "obrazek" => "primapenzion-room.jpg"
    ],
    "kontakt" => [
        "id" => "kontakt",
        "titulek" => "Kontakt",
        "menu" => "Kontakt",
        "obrazek" => "primapenzion-room2.jpg"
    ]
];
*/