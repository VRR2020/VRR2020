<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Customer extends Model
{
     /**
     * The storage format of the model's date columns.
     * Foi necessário inserir esse parâmetro, por que o SQL Server estava em português
     * @var string
     */
    protected $dateFormat = 'd/m/yy H:i:s';

    protected $fillable = [
        'first_name',
        'last_name',
        'address',
        'email',
        'phone',
    ];

}
