<?php

namespace App\Http\Controllers;

use App\Models\RendezVous;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RendezVousController extends Controller
{
    /**
     * Liste paginée avec filtrage multi-critères
     */
    public function index(Request $request)
    {
        $query = RendezVous::with(['patient', 'medecin']);

        // Filtrage par statut (EN_ATTENTE, CONFIRME, ANNULE, TERMINE)
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtrage par médecin
        if ($request->has('medecinId')) {
            $query->where('medecin_id', $request->medecinId);
        }

        // Filtrage par patient
        if ($request->has('patientId')) {
            $query->where('patient_id', $request->patientId);
        }

        // Filtrage par date (format YYYY-MM-DD)
        if ($request->has('date')) {
            $query->whereDate('date_heure', $request->date);
        }

        // Tri par date_heure décroissant (plus récents en premier)
        $query->orderBy('date_heure', 'desc');

        $rdvs = $query->paginate($request->get('size', 10));
        return response()->json($rdvs);
    }

    /**
     * Détail d'un rendez-vous (avec infos patient et médecin)
     */
    public function show($id)
    {
        $rdv = RendezVous::with(['patient', 'medecin'])->findOrFail($id);
        return response()->json($rdv);
    }

    /**
     * Créer un rendez-vous
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date_heure'  => 'required|date',
            'statut'      => 'sometimes|in:EN_ATTENTE,CONFIRME,ANNULE,TERMINE',
            'motif'       => 'required|string|max:500',
            'notes'       => 'nullable|string',
            'patient_id'  => 'required|exists:patients,id',
            'medecin_id'  => 'required|exists:medecins,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $rdv = RendezVous::create($request->all());
        $rdv->load(['patient', 'medecin']);
        return response()->json($rdv, 201);
    }

    /**
     * Modifier un rendez-vous (ou changer son statut)
     */
    public function update(Request $request, $id)
    {
        $rdv = RendezVous::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'date_heure'  => 'sometimes|date',
            'statut'      => 'sometimes|in:EN_ATTENTE,CONFIRME,ANNULE,TERMINE',
            'motif'       => 'sometimes|string|max:500',
            'notes'       => 'nullable|string',
            'patient_id'  => 'sometimes|exists:patients,id',
            'medecin_id'  => 'sometimes|exists:medecins,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $rdv->update($request->all());
        $rdv->load(['patient', 'medecin']);
        return response()->json($rdv);
    }

    /**
     * Supprimer un rendez-vous
     */
    public function destroy($id)
    {
        $rdv = RendezVous::findOrFail($id);
        $rdv->delete();
        return response()->json(null, 204);
    }
}

