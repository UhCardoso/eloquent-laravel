
# Eloquent Laravel

Curso para aprofundar os conhecimentos no Eloquent no treinamento oferecido pela [Especializa TI](https://especializati.com.br/).

No curso foram vistos:
- Personalização de Models
- Filtro de registros
- Paginação
- Ordenação
- Inserção, atualização e exclusão de registros
- Mass Assignment

### Fillable

Atributo adicionado na Model para deixar explicito quais os únicos campos enviados pela Request que serão
aceitos para serem salvos no banco de dados. Caso seja enviado um campo a mais que o esperado, esse campo será descartado.

```
class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'date'
    ];
}
```

### Adicionar campo na tabela em produção

```
$ composer require doctrine/dbal
```


criar migrate para realizar a alteração da tabela, colocando no final o nome  padrão descritivo do que a migrate irá alterar na tabela:

```
$ php artisan make:migration add_collumn_date_users
```

Copiar função schema da tabela migration original e alterar "``create``" por "``table``", e no corpo colocar a nova coluna informando opcionalmente a posição onde ela será inserida:

```
 Schema::table('posts', function (Blueprint $table) {
            ->after ou ->before é opcional
            $table->date('date')->after('body');
```

criar coluna no banco de dados:

```
$ php artisan migrate
```

Popular tabela com dados fake “```Factory```”:

```
$ comando: php artisan tinker
```

acessar o Model que será utilizado na aplicação passando as funções de criação de 100 dados fake de usuários nesse exempo:
```
\App\Models\User::factory()->count(100)->create();
```

### SoftDelete

Recurso para "fingir" que deletou o registro no banco de dados, apenas escondendo ele e não deletando de fato.

```
$ composer require doctrine/dbal
```

Caso já esteja usando a tabela em produção, crie uma migrate para realizar a alteração da tabela, colocando no final o nome  padrão descritivo do que a migrate irá alterar na tabela:

```
$ php artisan make:migration add_collumn_deleted_at_posts
```

Copiar função schema da tabela migration original e alterar "``create``" por "``table``", e no corpo colocar a nova coluna do tipo "``softDeletes()``"

```
 Schema::table('posts', function (Blueprint $table) {
            ->after ou ->before é opcional
            $table->date('date')->after('body');
```

Utilizar a Trait "``SoftDeletes`` no arquivo do Model":

```
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;
}
```

Agora toda vez que deletar registro, ele vai adicionar o valor na coluna deleted_at.

Criar a nova coluna:

```
$ php artisan migrate
```

### Acessor 
No arquivo Model podemos manipular o retorno de um campo criando o método contendo o nome dele com a palavra "Attribute" no final. Ex:

```
use HasFactory, SoftDeletes;

protected $fillable = [
    'user_id',
    'title',
    'body',
    'date'
];

public function getTitleAttribute($value)
{
    return strtoupper($value);
}
```

Pode também juntar o retorno de dois campos, seguindo o padrão da nomeação do método Nome_primeiro_campo "``And``" Nome_segundo_campo. Ex:

```
use HasFactory, SoftDeletes;

protected $fillable = [
    'user_id',
    'title',
    'body',
    'date'
];

public function getTitleAndBodyAttribute()
{
    return $this->title . ' - '. $this->body;
}
```

Pegando esse método em outros arquivos:

```
return $post->title_and_body
```

Criar arquivo de Trait para utilizar métodos acessores em vários Models ao mesmo tempo economizando código.
Recomendado criar uma pasta chamada "``Accessors``" no diretório "`App`" com o arquivo da Trait.

Exemplo do código na trait:

```
<?php

namespace App\Accessors;

trait DefaultAccessors
{
    public function getTitleAttribute($value)
    {
        return strtoupper($value);
    }

    public function getTitleAndBodyAttribute()
    {
        return $this->title . ' - '. $this->body;
    }
}
```

Depois basta importa-la no arquivo do Model para utiliza-la dentro da classe Model

```
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Accessors\DefaultAccessors; <<<<

class Post extends Model
{
    use HasFactory, SoftDeletes, DefaultAccessors; <<<<
```

-- descrever como criar dados fake pelo tinker --