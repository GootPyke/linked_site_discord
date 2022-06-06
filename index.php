<?php 
    require __DIR__ . '/vendor/autoload.php';
    require_once 'src\controller\connexion.php';

    function accueil(){

        $title = "Accueil";
        ob_start();
?>
test
<?php 
    $content = ob_get_clean();
    require_once('src/vue/template.php');
    }

    if(isset($_GET["action"])){
        $getAction = htmlspecialchars($_GET["action"]); 
        $action = preg_replace('#&[a-z]*;#', '', $getAction);
        switch ($action) {
            case 'accueil':
                accueil();
                break;
            
            case 'connexion':
                connexion();
                break;
        }
    } else {
        header('Location: index.php?action=accueil');
    }
?>

