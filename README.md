# laravel-starter

## Ferramentas utilizadas neste projeto

- [Visual Studio Code](https://code.visualstudio.com/) # Instalar
- [Docker](https://www.docker.com/) # Instalar
- [Dbeaver](https://dbeaver.io/) # Instalar
- [Postman](https://www.postman.com/) # Instalar

## Bancos de dados utilizados neste projeto

- [MySQL](https://www.mysql.com/)

## Linguagens de programação e frameworks utilizados neste projeto

- [PHP](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [Laravel](https://laravel.com/)

## Vamos começar

### Pasta do projeto

_Você deve estar na pasta onde o projeto será criado._

```sh
mkdir laravel-starter && cd laravel-starter
```

### Scripts de banco de dados

_Utilizaremos o MySQL como banco de dados da nossa aplicação._

**Criando a pasta de scripts:**

Voce deve estar na pasta laravel-starter

```sh
mkdir scripts
mkdir scripts/db
```

**Criando o arquivo com o script para a criação das tabelas:**

_Crie o arquivo ```01_create_tables.sql``` dentro da pasta scripts/db._

**Copie o script abaixo para dentro deste arquivo:**

```sql
-- MYSQL: Create user table
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
);
```

**Criando o arquivo com o script para inserir os dados de exemplo:**

_Crie o arquivo ```02_insert_data.sql``` dentro da pasta scripts/db._

**Copie o script abaixo para dentro deste arquivo:**

```sql
-- MYSQL: Insert data into user table
INSERT INTO `user` (`id`, `username`, `password`, `email`, `created_at`, `updated_at`) VALUES (1, 'admin', 'admin', 'admin@admin.com', '2020-01-01 00:00:00', '2020-01-01 00:00:00');
```

### Docker para o banco de dados MySQL

_Vamos criar o arquivo Dockerfile.mysql na raiz do projeto laravel-starter com o nome de ```Dockerfile.mysql```._

**Copie o conteúdo abaixo para dentro deste arquivo:**

```dockerfile
FROM mysql:8.0

RUN useradd -u 1000 laravel

ENV MYSQL_DATABASE=laravel_db
ENV MYSQL_USER=laravel_user
ENV MYSQL_PASSWORD=laravel_pass
ENV MYSQL_ROOT_PASSWORD=laravel_pass
ENV MYSQL_ALLOW_EMPTY_PASSWORD=yes
ENV MYSQL_RANDOM_ROOT_PASSWORD=yes
ENV MYSQL_ROOT_HOST=%
ENV TZ=America/Sao_Paulo

COPY ./scripts/db/01_create_tables.sql /docker-entrypoint-initdb.d/
COPY ./scripts/db/02_insert_data.sql /docker-entrypoint-initdb.d/

EXPOSE 3306

```

**Execute o container com base neste arquivo Dockerfile.mysql:**

```sh
# Opcional: remover o container caso exista
docker rm -f laravel-starter-db

# Opcional: remover a imagem caso exista
docker rmi -f laravel-starter-db

# fazer o build do container com force para garantir que a imagem será criada
docker build -t laravel-starter-db -f Dockerfile.mysql .

# executar o container, o comando -p adiciona as portas, onde HOST:EXPOSTA
docker run --name laravel-starter-db -p 3306:3306 -d laravel-starter-db
```
### Criando o container docker para a aplicação Laravel

Vamos criar o container com o nome laravel-starter-app, com a imagem do composer, com o volume ./ (sua pasta laravel-starter)
para o diretório de trabalho /app dentro do container

> O Composer é um gerenciador de dependências para o PHP. Ele permite que você declare as bibliotecas dependentes que seu
> projeto precisa e as gerencia (instala / atualiza) para você.

```bash
# Estamos usando o sh (shell) para entrar no container
docker run -it --rm --name laravel-starter-app -p 8000:8000 -v ./:/app -w /app --link laravel-starter-db composer /bin/sh

# Garantir a vitoria com o driver do mysql no container docker
docker-php-ext-install pdo pdo_mysql
```

**Criando o projeto com o Laravel:**

Vamos executar o comando abaixo dentro do container laravel-starter-app para criarmos o projeto laravel utilizando o MySQL
como banco de dados.

```bash
composer create-project --prefer-dist laravel/laravel src
```

**Vamos testar se a aplicação está funcionando:**

Execute o comando abaixo para iniciar o servidor web do Laravel.

> O Artisan é a interface de linha de comando (CLI) incluída no Laravel, um framework PHP popular para o desenvolvimento de
> aplicativos web. O Artisan permite que você execute várias tarefas, como a criação de componentes do Laravel,
> gerenciamento do banco de dados, execução de comandos personalizados, e muito mais.

```bash
php src/artisan serve --host=0.0.0.0 --port=8000 
```

### Configurando o banco de dados no Laravel

**Vamos configurar o banco de dados no Laravel:**

Vamos editar o arquivo ```src/.env``` e alterar as seguintes variáveis:

```dotenv
DB_CONNECTION=mysql
DB_HOST=laravel-starter-db
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

**Criar model de usuário:**

```
php src/artisan make:model User

# Entre no arquivo src/app/Models/User.php e cole o codigo abaixo

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $table = 'user';

    // Campos preenchíveis em massa
    protected $fillable = [
        'id',
        'username',
        'password',
        'email',
        'created_at',
        'updated_at',
    ];
}

```

**Criar controlador que vai buscar os dados clientes:**
```sh
php src/artisan make:controller UserController

# Entre no arquivo src/app/Http/Controllers/UserController.php e cole o codigo abaixo

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }
}

```

**Alterar o arquivo de rotas:**

Entre na pasta src/routes/api.php e cole o codigo abaixo:

```sh
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/users', [UserController::class, 'index']);

```

**Teste a sua aplicacao:**

```sh
php src/artisan serve --host=0.0.0.0 --port=8000 
```

http://localhost:8000/api/users
