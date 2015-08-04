# Babita Framework 1

BF1 é um *nano* framework PHP inspirado no [Silex](http://silex.sensiolabs.org/), [Slim](http://www.slimframework.com/) e [Simple MVC Framework](http://simplemvcframework.com/php-framework) (Utiliza alguns componentes destes). Foi desenvolvido / implementado para atender algumas necessidades do SGAMA - Sistema de Gestão Acadêmica do Maranhão. BF1 é pequeno, rápido e simples de usar. O toolkit possibilita o desenvolvimento de projetos flexiveis sem burocracia. Você pode fazer um clone / download e começar a trabalhar imediatamente.

### Exemplos

```PHP
Router::get('/', function() {
  echo 'Bem vindo ao BF1 <3!';
});

Router::run();
```

#### A principal característica do BF1 é simplicidade e a forma desenvolvida para acessar os recursos do sistema.

Veja a simplicidade do `index.php`
Com apenas isso você já pode desenvolver seu seus controllers e executa-los através da url

Você pode passar diversos parâmetros pela URL
````
Ex. 1: www.dominio.com/controller/method/parameter
Ex. 2: www.dominio.com/controller/method/parameter1/parameter2
````

```PHP

require_once "app/start.php";

use \Core\Router;
Router::autoRun();
```

BF1 também suporta lambda URIs:

```PHP
Router::get('/nome/(:any)', function($nome) {
  echo 'Meu nome é: ' . $nome;
});

Router::run();
```

Você também pode fazer requests com os verbos HTTP:

```PHP
Router::get('/', function() {
  echo 'GET <3';
});

Router::post('/', function() {
  echo'POST <3';
});

Router::put('/', function() {
  echo 'PUT <3';
});

Router::delete('/', function() {
  echo'DELETE <3';
});
Router::run();
```

Se não houver uma rota definida para um determinado local, você pode executar um callback personalizado:

```PHP
Router::error(function() {
  echo '404 :: Página não encontrada';
});
```

Se você não especificar um callback de erro, o BF1 executa o controller padrão para este fim.

## Instalação

1. Faça o download
2. Descompacte o pacote
4. Edite o arquivo index.php e configure suas rotas
5. Edite o aquivo app/Core/Config.php e defina suas configurações de banco de dados e constantes do sistema.
6. Faça o upload dos arquivos para o seu servidor e seja feliz :)
