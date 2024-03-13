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
