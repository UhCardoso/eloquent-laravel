
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

### Inserindo dados fake com Factory
Criar arquivo de factory na pasta "``database\factories``"

```
$ php artisan make:factory PostFactory --model=Post
```

Gerar dados fake no banco de dados com o tinker

```
$ php artisan tinker
```

Inserir o seguinte comando para criar a factory com 10 usuários por exemplo:

```
>>> \App\Models\Post::factory()->count(10)->create();
```
No comando acima, passar a partir de qual Model sera gerada a factory

Ou Também pode ser usado o comando Seeder:

```
$ php artisan make:seeder PostSeeder
```
No arquivo criado, deixar a seguinte estrutura para gerar 10 posts por exemplo:

```
public function run()
{
    Post::factory()->count(10)->create();
}
```

Caso tenha mais de um seeder, pode chama-los no arquivo "``DatabaseSeeder.php``".

```
public function run()
{
    $this->call([
        PostsSeeder::class
    ]);
}
```

Rodar seeder defenido:

```
$ php artisan db:seed
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
Usado para alterar dado antes de inseri-lo no banco de dados, colocando o sufixo do método do Model com a palavra "set". Ex:

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
o ``lastWeek()`` irá reconhecer automaticamente que estamos chamando o método scopeLastWeek() no model, pois ele utiliza o sufixo "scope" no nome do método.


### Observer

No Laravel, um Observer serve para centralizar a lógica relacionada a eventos de ciclo de vida dos modelos Eloquent. Cada modelo em Laravel pode disparar uma série de eventos durante seu ciclo de vida, como creating, created, updating, updated, saving, saved, deleting, deleted, retrieved.

Quando usar o OBSERVER:

Se há ações que você precisa realizar sempre que um modelo é criado, atualizado, excluído, etc., como limpar cache, atualizar outras partes do banco de dados, enviar notificações, entre outros.

o primerio passo do processo para utilizar um Observer no laravel e usar o comando de criação do "``Observer``":

```
$ php artisan make:observer UserObserver --model=User
```

Será criado um arquivo de "``PostObserver.php``" no caminho "``App\Observes``".

Exemplo:

```
namespace App\Observers;

use App\Models\Post;

class PostObserver
{
    /**
     * Handle the Post "created" event.
     *
     * @param  \App\Models\Post  $post
     * @return void
     */
    public function created(Post $post)
    {
        //
    }

```

Os métodos desse arquivo serão chamados após executar a ação no model.
Para fazer esses métodos serem chamados após antes de executar a ação no model basta alterar o nome do método deixando "ing" no final do nome, no caso acima o "``created`` será "``creating``".

No arquivo "`App\Providers\EventServiceProvider.php`", relacione o arquivo "``PostObserser.php``" ao Model "``Post``".

Exemplo:

```
 public function boot()
{
    Post::observe(PostObserver::class);
}
```

### Eventos

Adicionar eventos no laravel para enviar um email por exemplo sempre que um método for executado.

Criar evento:

```
$ php artisan make:event PostCreated
```

No arquivo Model usa o "PostCreated" por exemplo para chamar os eventos.

```
 protected $dispatchesEvents = [
    'created' => PostCreated::class,
];
```

Criar listener:
```
protected $dispatchesEvents = [
    'created' => PostCreated::class,
];

```

Ligar o evento criado ao listener no arquivo "```EventServiceProvider```" informando qual listener será rodado quando o evento for disparado:

```
protected $listen = [
    Registered::class => [
        SendEmailVerificationNotification::class,
    ],
    PostCreated::class => [
        NotifyNewPostCreated::class
    ]
];
```

criar Model email:

php artisan make:mail MailNewPostCreated