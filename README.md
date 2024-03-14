
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

> $ composer require doctrine/dbal


criar migrate para realizar a alteração da tabela, colocando no final o nome   padrão descritivo do que a migrate irá alterar na tabela:

> $ php artisan make:migration add_collumn_date_users

Copiar função schema da tabela migration original e alterar "``create``" por "``table``", e no corpo colocar a nova coluna informando opcionalmente a posição onde ela será inserida:

```
 Schema::table('posts', function (Blueprint $table) {
            ->after ou ->before é opcional
            $table->date('date')->after('body');
```

criar coluna no banco de dados:

> $ php artisan migrate


Popular tabela com dados fake “```Factory```”:

> $ comando: php artisan tinker

acessar o Model que será utilizado na aplicação passando as funções de criação de 100 dados fake de usuários nesse exempo:
```
\App\Models\User::factory()->count(100)->create();
```