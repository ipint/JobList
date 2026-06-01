<?php

use App\Http\Controllers\PublicXmlFeedController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/feeds/jobs/{xmlFeed:slug}.xml', [PublicXmlFeedController::class, 'show'])
    ->name('public.xml-feeds.show');
