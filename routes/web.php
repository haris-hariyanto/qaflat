<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [Controllers\HomeController::class, 'index'])->name('index');
Route::get('/jawaban/{content}', [Controllers\ContentController::class, 'index'])->name('content');
Route::get('/subject/{subject}', [Controllers\SubjectController::class, 'index'])->name('subject');
Route::get('/grade/{grade}', [Controllers\GradeController::class, 'index'])->name('grade');
Route::get('/p/{page}', [Controllers\PageController::class, 'page'])->name('page');
Route::get('/sitemaps-index.xml', [Controllers\SitemapController::class, 'index']);
Route::get('/sitemap-questions-{index}.xml', [Controllers\SitemapController::class, 'sitemapContents'])->name('sitemap.questions');
