<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Welcome', [
            'posts' => Inertia::defer(fn () => Post::latest()->get()),
        ]);
    }
}
