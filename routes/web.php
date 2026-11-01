<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::livewire('/', 'pages::beranda')->name('beranda');
    Route::livewire('/instansi', 'pages::instansi')->name('instansi');
    Route::livewire('/divisi', 'pages::divisi')->name('divisi');
    Route::livewire('/pegawai', 'pages::pegawai.index')->name('pegawai.index');
    Route::livewire('/pegawai/create', 'pages::pegawai.form')->name('pegawai.create');
    Route::livewire('/pegawai/{pegawai}/edit', 'pages::pegawai.form')->name('pegawai.edit');
});
Route::livewire('/masuk', 'pages::account.login')->name('login')->middleware('guest');



Route::post('/keluar', function () {
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/masuk');
})->name('logout');
