<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Tudo aqui é servido com prefixo /api (ex.: /api/health)
*/

Route::get('/health', function () {
    return response()->json(['ok' => true]);
});

// Lista fake só para testar
Route::get('/events', function () {
    return response()->json([
        ['id' => 1, 'title' => 'Abertura', 'date' => '2025-09-10', 'location' => 'Auditório'],
        ['id' => 2, 'title' => 'Workshop Laravel', 'date' => '2025-09-15', 'location' => 'Lab 1'],
    ]);
});

// Criação fake só para testar POST do formulário
Route::post('/events', function (Request $request) {
    return response()->json([
        'id'       => rand(1000, 9999),
        'title'    => $request->input('title'),
        'date'     => $request->input('date'),
        'location' => $request->input('location'),
        'status'   => 'created',
    ], 201);
});