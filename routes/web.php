<?php

\Illuminate\Support\Facades\Route::get('/', function (){
    return response()->json([
        'message' => 'up'
    ]);
});
