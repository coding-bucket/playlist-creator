<?php

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@Symfony' => true,
    // 'strict_param' => true,
    'array_syntax' => ['syntax' => 'short'],
]);
