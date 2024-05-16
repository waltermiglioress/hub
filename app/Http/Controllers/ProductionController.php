<?php

namespace App\Http\Controllers;

use App\Exports\ProductionExport;
use App\Models\Production;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ProductionController extends Controller
{

    public function export(Request $request)
    {

        $ids = $request->query('ids');

//        Log::info("IDs received for export: " . $request->query('ids'));

        if ($ids) {
            $idsArray = explode(',', $ids);// Trasforma la stringa di ID in un array
            $query = \App\Models\Production::whereIn('id',$idsArray);

        }else {
            $query = \App\Models\Production::query();  // Se non ci sono ID, esporta tutto
        }
//    dd($query);
        $export = new ProductionExport($query);
        $fileName = 'produzioni-' . now()->format('Y-m-d_His') . '.xlsx';
        return Excel::download($export, $fileName);
    }

//    /**
//     * Display a listing of the resource.
//     */
//    public function index()
//    {
//        //
//    }
//
//    /**
//     * Show the form for creating a new resource.
//     */
//    public function create()
//    {
//        //
//    }
//
//    /**
//     * Store a newly created resource in storage.
//     */
//    public function store(Request $request)
//    {
//        //
//    }
//
//    /**
//     * Display the specified resource.
//     */
//    public function show(Production $production)
//    {
//        //
//    }
//
//    /**
//     * Show the form for editing the specified resource.
//     */
//    public function edit(Production $production)
//    {
//        //
//    }
//
//    /**
//     * Update the specified resource in storage.
//     */
//    public function update(Request $request, Production $production)
//    {
//        //
//    }
//
//    /**
//     * Remove the specified resource from storage.
//     */
//    public function destroy(Production $production)
//    {
//        //
//    }
}
