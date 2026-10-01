<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReleaseRequest;
use App\Models\Release;
use Illuminate\Http\Request;

class ReleasesController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Release::with('user:id,name')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReleaseRequest $request)
    {
        $release = $request->user()->releases()->create($request->validated());

        return response($release, 201);
    }

    /**
     * Display the specified resource.
     * On met le type string devant $id parce que, par défaut, Laravel extrait les paramètres de l'URL sous forme de strings, même s'il s'agit d'entiers
     */
    public function show(string $id, Request $request)
    {
        $release = Release::with('user:id,name')->where('user_id', $request->user()->id)->find($id);

        if (!$release) {
            return response()->json(['message' => "Cette sortie n'existe pas"], 404);
        }

        return $release;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReleaseRequest $request, Release $release)
    {
        if ($release->user_id !== $request->user()->id) {
            return response(['message' => 'Vous n\'êtes pas autorisé à modifier cette sortie'], 403);
        }

        $release->update($request->validated());

        return $release;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Release $release)
    {
        if ($release->user_id !== $request->user()->id) {
            return response(['message' => 'Vous n\'êtes pas autorisé à supprimer cette sortie'], 403);
        }

        $release->delete();

        return response(['message' => 'Sortie supprimée']);
    }
}
