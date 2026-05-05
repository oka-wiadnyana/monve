<?php

namespace App\Http\Controllers;

use App\Http\Resources\BackUpTableResource;
use App\Models\BackupTable;
use Illuminate\Http\Request;

class BackupTableController extends Controller
{
    public function index()
    {
        $backups = \App\Models\BackupTable::where('SCHEMA_NAME', 'like', 'sipp_pn_%')->get();

        return BackUpTableResource::collection($backups);
    }

    public function show($id)
    {
        // Cari database berdasarkan primary key (SCHEMA_NAME)
        $backup = BackupTable::where('SCHEMA_NAME', $id)
            ->where('SCHEMA_NAME', 'like', 'sipp_pn_%') // Pastikan tetap terfilter
            ->firstOrFail();
        $backup->load(['tables' => function ($query) {
            $query->where('TABLE_ROWS', '>', 0)
                ->whereIn('TABLE_NAME', ['perkara', 'perkara_putusan', 'perkara_banding', 'perkara_banding_detil', 'perkara_jadwal_sidang', 'ecourt_banding', 'berpadu_upaya_hukum']); // Opsional
        }]);

        return new BackupTableResource($backup);
    }
}
