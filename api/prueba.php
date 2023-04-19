<?php

$numero_documento = "071220220117175484300012001003000000001123456781";
$numero_documento = trim($numero_documento);
$numero_documento = str_replace(array("-", " "), "", $numero_documento);
print_r($numero_documento);
$suma = 0;
$factor = 2;

$caracteres = str_split($numero_documento);

$long = count($caracteres) - 1;
    echo"numero de caracteres en long :";
    echo"<p>$long</p>";
    echo"<p>'inicia variable suma en '.$suma</p>";
for ($i = $long; $i >= 0; $i--) {
    
    $suma += intval($caracteres[$i]) * $factor;
    
    echo "<p> $i '-> '.$caracteres[$i] </p> ";
    echo "<p>'valor de la suma= ' .$suma</p>";
    if ($factor == 7) {
        $factor = 2;
    } else {
        $factor++;
        
    }
    // echo "<p>$factor</p> ";
}




$modulo = 11 - ($suma % 11);

echo ("valor de modulo ");
echo"<p>$modulo</p>";
if ($modulo == 10) {
    echo"modulo es igual a 1";
    return (string)"1";
}

if ($modulo < 10) {
    echo"inicio <10";
    echo"<p>$modulo</p>";
    return (string)$modulo;
}

if ($modulo == 11) {
    echo"<p>retorna 0</p> ";
    
    return (string) "0";
}
echo "sale por k";
return "K";
?>
