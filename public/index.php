<?php
    require_once '../inc/config.php';
    require_once $config['inc'].'header.php';


    if (isset($_SESSION['logedIn']) && $_SESSION['logedIn'] == true)
        require_once $config['inc'].'navbar_logedin.php';
    else
        require_once $config['inc'].'navbar_default.php';


    if (isset($_SESSION['flashMsg'])) {
        echo flashMsg($_SESSION['flashMsg'], $_SESSION['alertType']);
        unset($_SESSION['flashMsg']);
        // unset($_SESSION['alertType']);
    }


    if (isset($_GET['load']) && $_GET['load'] != '' && file_exists($config['inc'].'main_'.strtolower($_GET['load']).'.php'))
        require_once $config['inc'].'main_'.strtolower($_GET['load']).'.php';
    else
        require_once $config['inc'].'main_default.php';
    
    require_once $config['inc'].'footer.php';
