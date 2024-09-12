<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductionResource;
use App\Models\Production;
use Illuminate\Http\Request;

class ProductionController extends Controller
{
    public function report(){

        $prod = Production::where('status','!=','fatturato')->get();
//        dd($prod);

        return ProductionResource::collection($prod);

    }

}
