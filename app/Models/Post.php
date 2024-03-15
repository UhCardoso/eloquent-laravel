<?php

namespace App\Models;

use App\Accessors\DefaultAccessors;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes, DefaultAccessors;
    // filtra a inserção de dados no DB aceitando somente os dados abaixo vendo da requisição
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'date'
    ];

    // retorna dados no tipo e padrão especifico que voçê precisa
    protected $casts = [
        'date' => 'date',
        'active' => 'boolean'
    ];

    // personalizando informações da tabela caso necessário
    // protected $table = 'postagens'; //mudar nome da tabela
    // protected $primaryKey = 'id'; // mudar nome da chave primária
    // protected $keyType = 'string'; // muda tipo da chave primária
    // protected $incrementing = false; // desabilitar incrementação da coluna primária
    // protected $timestamps = false; // desabilitar coluna data criação
    // const CREATED_AT = 'data_criacao'; // alterar nome coluna data criação
    // const UPDATED_AT = 'data_atualizacao'; // alterar nome coluna data atualização
    // protected $dateFormat = 'Y/m/d'; // personalizar formato da data
    // protected $connection = 'pgsql'; // alternar conexão com outro banco de dados
    // protected $attributes = [ //definir valor padrão para colunas caso não tenha a configuração no banco
    //     'active' => true
    // ]; 

    // public function getTitleAttribute($value)
    // {
    //     return strtoupper($value);
    // }

    // public function getTitleAndBodyAttribute()
    // {
    //     return $this->title . ' - '. $this->body;
    // }

    // public function getDateAttribute($value)
    // {
    //     return Carbon::make($value)->format('d/m/Y');
    // }
}
