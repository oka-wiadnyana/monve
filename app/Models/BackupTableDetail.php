<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class BackupTableDetail extends Model
{
    protected $connection = 'mysql_backup';
    protected $table = 'tables'; // information_schema.tables
    protected $primaryKey = 'TABLE_NAME';
    public $incrementing = false;
    public $timestamps = false;

    protected static function booted()
    {
        // PENTING: Jangan panggil parent::booted() jika induknya punya scope SCHEMA_NAME

        static::addGlobalScope('only_base_tables', function (Builder $builder) {
            $builder->where('TABLE_TYPE', '=', 'BASE TABLE');
            // JANGAN masukkan SCHEMA_NAME di sini!
        });
    }

    public function schema()
    {
        return $this->belongsTo(BackupTable::class, 'TABLE_SCHEMA', 'SCHEMA_NAME');
    }
}
