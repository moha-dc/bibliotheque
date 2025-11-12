<?php
session_start();
//détruire toutes les variables de session
session_destroy();
//une destruction de session nécessite une redirection
header('Location:index.php?logout=success');
exit();