<?php

namespace App\Http\Controllers;

use App\Models\Medecin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MedecinController extends Controller
{
    /**
     * Liste paginée avec recherche multi-champs (keyword, spécialité)
     */
    public function index(Request $request)
    {
        $query = Medecin::query();

        // Recherche multi-champs (keyword OU spécialité)
        if ($request->has('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('nom', 'like', "%{$keyword}%")
                  ->orWhere('prenom', 'like', "%{$keyword}%")
                  ->orWhere('specialite', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('matricule', 'like', "%{$keyword}%");
            });
        }

        // Filtrage par disponibilité (optionnel)
        if ($request->has('disponible')) {
            $query->where('disponible', $request->boolean('disponible'));
        }

        $medecins = $query->paginate($request->get('size', 10));
        return response()->json($medecins);
    }

    /**
     * Détail d'un médecin
     */
    public function show($id)
    {
        $medecin = Medecin::with('rendezVous')->findOrFail($id);
        return response()->json($medecin);
    }

    /**
     * Créer un médecin
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom'        => 'required|string|max:255',
            'prenom'     => 'required|string|max:255',
            'specialite' => 'required|string|max:255',
            'email'      => 'required|email|unique:medecins',
            'telephone'  => 'required|string',
            'matricule'  => 'required|string|unique:medecins',
            'disponible' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $medecin = Medecin::create($request->all());
        return response()->json($medecin, 201);
    }

    /**
     * Mettre à jour un médecin
     */
    public function update(Request $request, $id)
    {
        $medecin = Medecin::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom'        => 'sometimes|string|max:255',
            'prenom'     => 'sometimes|string|max:255',
            'specialite' => 'sometimes|string|max:255',
            'email'      => 'sometimes|email|unique:medecins,email,' . $id,
            'telephone'  => 'sometimes|string',
            'matricule'  => 'sometimes|string|unique:medecins,matricule,' . $id,
            'disponible' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $medecin->update($request->all());
        return response()->json($medecin);
    }

    /**
     * Supprimer un médecin
     */
    public function destroy($id)
    {
        $medecin = Medecin::findOrFail($id);
        $medecin->delete();
        return response()->json(null, 204);
    }
}
