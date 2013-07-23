Rabbit [![Build Status](https://travis-ci.org/rabbit-project/rabbit.png?branch=develop)](https://travis-ci.org/rabbit-project/rabbit)
======

Rabbit é um mini-framewok para auxiliar a criação do RabbitCMS

Framework implementa:

```
  - Controller
  - View
  - Dao (Doctrine 2)
  - Layout
  - LogManager
  - EventManager
  - Acl
  - Routing
  - ServiceLocator
```

Instalação
======

Antes de tudo é necessário a instalação do composer.

Instalação do composer:

Caso não venha com o composer.phar para instalar segue informação abaixo:

Acessar a pasta do projeto "rabbit"

Linux:
$ curl -s https://getcomposer.org/installer | php

Windows:
php -r "eval('?>'.file_get_contents('https://getcomposer.org/installer'));"

Depois executar o comando:

Atualizar o composer:
$ php composer.phar self-update

Instalando as dependencias do rabbit:
$ php composer.phar install
