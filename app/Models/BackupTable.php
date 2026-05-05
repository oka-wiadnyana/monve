<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupTable extends Model
{
    protected $connection = 'mysql_backup';
    protected $table = 'schemata'; // Merujuk ke hasil tinker tadi

    // SCHEMA_NAME adalah kolom unik yang kita jadikan ID
    protected $primaryKey = 'SCHEMA_NAME';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    public function tables()
    {
        return $this->hasMany(BackupTableDetail::class, 'TABLE_SCHEMA', 'SCHEMA_NAME');
    }
}
