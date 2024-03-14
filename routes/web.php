<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});
