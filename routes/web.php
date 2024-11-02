<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::view('/', 'welcome');

// Route::view('/dashboard',function(){
//     $users = User::all();
//     return $users;
// })->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/dashboard',function(){
    $users = User::all();
    return view('dashboard',[
        'users'=>$users
    ]);
})->middleware(['auth','verified'])->name('dashboard');


Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');
Route::get('/chat/{id}',function($id){
    return view('chat',[
        'id'=>$id
    ]);
})->middleware(['auth','verified'])->name('chat');
require __DIR__.'/auth.php';
