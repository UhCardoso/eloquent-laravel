<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('softdelete', function() {
    Post::destroy(5);

    $posts = Post::get();

    return $posts;
});

Route::get('/delete', function() {
    // Post::destroy(1, 2, 4); // Dá mais possibilidade de deletar vários registros pelo id

    $post = Post::where('id', 3)->first();

    if(!$post)
        return "Post not found";

    $post->delete();
});

Route::get('/update', function(Request $request) {
    if(!$post = Post::find(1))
        return 'Post not found';

    //$post->title = "Titulo atualizado";
    //$post->save();

    $post->update($request->all());

    dd($post);

    return Post::find(1);
});

Route::get('/insert-dinamico', function(Request $request) {
    Post::create($request->all());// dados da request ?title=fsdfsfsadf&user_id=2&body=body&date=2024-11-21

    $posts = Post::get();

    return $posts;
});

Route::get('insert', function(Post $post, Request $request) {
    $post->user_id = 1;
    $post->title = $request->name;
    $post->body = Str::random(30).'descrição postagem teste ';
    $post->date = date('Y-m-d');
    $post->save(); // Salvar dados acima no banco de dados

    $posts = Post::get();

    return $posts;
});

Route::get('/select', function() {
    //$users = User::all() // pegar todos os dados
    //$users = User::where('id', '>=',10)->get(); //pode forçar query com o get
    //$user = User::where('id', 10)->first(); // trazer o user direto sem ser array
    //$user = User::first(); // trazer primeiro dado da tabela
    //$user = User::find(10); // retornar usuário com id igual a 10
    //$user = User::findOrFail(request('id')); // se não encontrar, retorna erro 404 (usado em API)
    //$user = User::where('name', request('name'))->firstOrFail(); // retorna nome ou retorna 404 se não encontrar
    $user = User::firstWhere('name', request('name')); // faz a mesma coisa da linha acima

    dd($user->name);
});

Route::get('/where', function(User $user) {
    //$user = $user->where('email', '=', 'goldner.alf@example.net')->first(); //com segundo parametro de comparação
    //$user = $user->where('email', 'goldner.alf@example.net')->first(); //quando usa comparação, não precisa informar o simbolo no segundo parametro
    $filter = 'a';
    //$users = $user->where('name', 'LIKE', "%{$filter}%")->get(); //retorna valor que contenha letras passadas no parametro filter
    //$users = $user->where('name', 'LIKE', "%{$filter}%")
    //                ->orWhereNot('name', 'Cardoso') //whereNot | whereName('Cardoso') | whereIn('email', [array, com, dados]) | orWhereIn('email', [array, com, dados])
    //                ->get(); // Fazer multiplas querys

    $users  = $user->where('name', 'LIKE', "%{$filter}")
                    ->orWhere(function($query) use ($filter) {
                        $query->where('name', '!=', 'Cardoso');
                        $query->where('name', '!=', "%{$filter}");
                    })
                    ->get();// Multiplas querys passando function como parametro do or Where possibilitanso até filtrar outras tabelas

    dd($users);
});

Route::get('pagination', function(User $user) {
    //$users = $user->paginate(30); // 30 resultados por página >> parametro para acessar outras paginas /pagination?page=2
    //$users = $user->where('name', 'LIKE', "%a%")->paginate(); // filtrar registros retornando os dados no formato paginate -- passe na rota o /?page=2&filter=a para o total de registros se respeitado no filtro
    
    $filter = request('filter');
    $totalPage = request('paginate', 10); // retornar 10 itens por pagina caso não passe o parametro
    $users = $user->where('name', 'LIKE', "%{$filter}%")->paginate($totalPage); // retornar o numero de registros por pagina dinamicamente por parametro

    return $users;
});

Route::get('/orderby', function(User $user) {
    $users = $user->orderBy('name', 'DESC')->get(); // retornar registros de forma decrescente considerando a tabela escolhida
    //o orderBy pode ser usando mais de uma vez e pode ser combinado com o ->where()

    return $users;
});

Route::get('/', function () {
    return view('welcome');
});
