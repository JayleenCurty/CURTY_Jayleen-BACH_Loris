<?php
require_once __DIR__ . '/../src/utils/autoloader.php';

Auth::deconnecter();

header('Location: index.php');
exit;
