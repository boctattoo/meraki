<?php
$senha = 'admin123';
$hash = '$2y$10$iqh0Cf35qOp8kQnIGRUle.1Ru6OLyRuwkMLZpSr1gIqXCuIYwrsz2';

if (password_verify($senha, $hash)) {
    echo "Senha OK!";
} else {
    echo "Senha inválida!";
}
?>
