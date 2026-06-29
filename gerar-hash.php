<?php
// script que uso pra gerar hash de senha rapido quando preciso atualizar
// um usuario direto no banco (tipo o admin). roda com php gerar-hash.php
echo password_hash('123456', PASSWORD_DEFAULT);
