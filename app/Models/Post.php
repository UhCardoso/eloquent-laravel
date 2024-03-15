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

    /*
        Quando usar CASTS:
        quando precisar garantir que um atributo seja sempre tratado como um determinado tipo
        dentro da sua aplicação, como converter automaticamente strings JSON em arrays PHP ou formatar campos de data.
        São sobre como os dados são apresentados e tratados no seu código Laravel
    */
    protected $casts = [
        'date' => 'datetime:d/m/Y',
        'active' => 'boolean'
    ];

    // Local Scopes
    public function scopeLastWeek($query)
    {                                               //subdays: quantidade de dias atrás
        return $this->whereDate('date', '>=', now()->subDays(7))
            ->whereDate('date', '<=', now()->subDays(1)); // Recuperando posts entre 7 dias atrás e ontem(14/03/2024)
    }

    public function scopeToday()
    {
        return $this->whereDate('date', now());
    }

    public function scopeBetween($query, $firstDate, $lastDate)
    {
        $firstDate = Carbon::make($firstDate)->format('Y-m-d');
        $lastDate = Carbon::make($lastDate)->format('Y-m-d');

        return $this->whereDate('date', '>=', $firstDate)
            ->whereDate('date', '<=', $lastDate);
    }

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

    /*  
        Quando usar accessors: 
        Use accessors quando precisar formatar ou processar
        dados ao recuperá-los do banco de dados, mas não quer alterar a forma como eles são armazenados.
        Accessors são sobre como você vê os dados
    */
    // metodo ACESSOR
    // public function getTitleAttribute($value)
    // {
    //     return strtoupper($value);
    // }

    // metodo ACESSOR
    // public function getTitleAndBodyAttribute()
    // {
    //     return $this->title . ' - '. $this->body;
    // }

    // metodo ACESSOR
    // public function getDateAttribute($value)
    // {
    //     return Carbon::make($value)->format('d/m/Y');
    // }

    //metodo MUTATOR
    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::make($value)->format('Y-m-d');
    }
}
