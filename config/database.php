<?php
require_once __DIR__.'/config.php';
try {
  $pdo=new PDO(
    'mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.';charset=utf8mb4',
    DB_USER, DB_PASS,
    [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]
  );
}
catch(PDOException $e){
  http_response_code(500);
  die('<h2>No se pudo conectar a MySQL</h2><p>Importa <b>encuentralo_db.sql</b> y revisa config/config.php.</p>');
}