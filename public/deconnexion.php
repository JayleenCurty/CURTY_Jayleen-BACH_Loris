<?php
require_once __DIR__ . '/../src/autoload.php';

Auth::deconnecter();

header('Location: index.php');
exit;
