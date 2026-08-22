<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataTableService
{
    public static function paginar(
        Builder $query,
        Request $request,
        array $searchColumns,
        array $orderColumns,
        int $defaultOrderColumn = 0,
        string $defaultOrderDir = 'asc'
    ): array {
        $totalRecords = $query->toBase()->count();

        if ($search = $request->get('search')['value']) {
            $query->where(function ($q) use ($search, $searchColumns) {
                foreach ($searchColumns as $column) {
                    $q->orWhere($column, 'ILIKE', "%{$search}%");
                }
            });
        }

        $filteredRecords = $query->toBase()->count();

        $orderColumn = $request->get('order')[0]['column'] ?? $defaultOrderColumn;
        $orderDir = $request->get('order')[0]['dir'] ?? $defaultOrderDir;

        if (isset($orderColumns[$orderColumn])) {
            $query->orderBy($orderColumns[$orderColumn], $orderDir);
        } else {
            $query->orderBy($orderColumns[$defaultOrderColumn] ?? array_key_first($orderColumns), $defaultOrderDir);
        }

        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $data = $query->skip($start)->take($length)->get();

        return [
            'draw' => intval($request->draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ];
    }
}
