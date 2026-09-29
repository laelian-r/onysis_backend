<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticlesController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Article::with('user:id,name')->latest()->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ArticleRequest $request)
    {
        $article = $request->user()->articles()->create($request->validated());

        return response($article, 201);
    }

    /**
     * Display the specified resource.
     * On met le type string devant $id parce que, par défaut, Laravel extrait les paramètres de l'URL sous forme de strings, même s'il s'agit d'entiers
     */
    public function show(string $id)
    {
        $article = Article::with('user:id,name')->find($id);

        if (!$article) {
            return response()->json(['message' => "Cet article n'existe pas"], 404);
        }

        return $article;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ArticleRequest $request, Article $article)
    {
        if ($article->user_id !== $request->user()->id) {
            return response(['message' => 'Vous n\'êtes pas autorisé à modifier cet article'], 403);
        }

        $article->update($request->validated());

        return $article;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Article $article)
    {
        if ($article->user_id !== $request->user()->id) {
            return response(['message' => 'Vous n\'êtes pas autorisé à supprimer cet article'], 403);
        }

        $article->delete();

        return response(['message' => 'Article supprimé']);
    }
}
