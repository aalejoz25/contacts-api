<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});


//Contact routes
Route::get('/contacts',[ContactController::class,'index']);
Route::post('/contacts',[ContactController::class,'store']);
Route::get('/contacts/{contact}',[ContactController::class,'show']);
Route::put('/contacts/{contact}',[ContactController::class,'update']);
Route::delete('/contacts/{contact}',[ContactController::class,'destroy']);

//Email management routes
Route::post('/contacts/{contact}/emails', [ContactController::class, 'addEmail']);
Route::put('/contacts/{contact}/emails/{email}', [ContactController::class, 'updateEmail']);
Route::delete('/contacts/{contact}/emails/{email}', [ContactController::class, 'deleteEmail']);


//Phone management routes
Route::post('/contacts/{contact}/phones', [ContactController::class, 'addPhone']);
Route::put('/contacts/{contact}/phones/{phone}', [ContactController::class, 'updatePhone']);
Route::delete('/contacts/{contact}/phones/{phone}', [ContactController::class, 'deletePhone']);

