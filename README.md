
# Anotações de Estudo Eloquent Laravel

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

### Inserindo Dados Fake com o Tinker
***********
-- descrever como criar dados fake pelo tinker --
***************

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

Quando usar accessors: 
Use accessors quando precisar formatar ou processar
dados ao recuperá-los do banco de dados, mas não quer alterar a forma como eles são armazenados.
Accessors são sobre como você vê os dados

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

### MUTATOR
Usado para alterar dado antes de inseri-lo no banco de dados, colocando o prefixo do método do Model com a palavra "set". Ex:

```
public function setDateAttribute($value)
{
    $this->attributes['date'] = Carbon::make($value)->format('Y-m-d');
}
```
### CASTS

Quando usar CASTS:
quando precisar garantir que um atributo seja sempre tratado como um determinado tipo
dentro da sua aplicação, como converter automaticamente strings JSON em arrays PHP ou formatar campos de data.
São sobre como os dados são apresentados e tratados no seu código Laravel

```
protected $casts = [
    'date' => 'datetime:d/m/Y',
    'active' => 'boolean'
];
```

### LOCAL SCOPE

Local Scopes no Laravel são métodos definidos nos modelos Eloquent que permitem que você reutilize consultas SQL em várias partes do seu aplicativo. Eles são úteis para encapsular lógicas de consulta, mantendo seu código limpo e mais fácil de manter.

Quando usar LOCAL SCOPE:
1- Tiver consultas que você reutiliza frequentemente em diferentes partes do aplicativo.
2- Quiser manter suas consultas organizadas e fáceis de ler e manter.

O método no Model deverá começar com a palavra "``Scope``" seguido do nome da sua função.

```
public function scopeLastWeek($query)
{                                              
    return $this->whereDate('date', '>=', now()->subDays(4))
        ->whereDate('date', '<=', now()->subDays(0));
}
```

Chamando scope no arquivo rotas, por exemplo:

```
Route::get('/local-scope', function () {
    $posts = Post::lastWeek()->get();

    return $posts;
});
```
o ``lastWeek()`` irá reconhecer automaticamente que estamos chamando o método scopeLastWeek() no model, pois ele utiliza o prefixo "scope" no nome do método.
