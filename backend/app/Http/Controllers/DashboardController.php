<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Medecin;
use App\Models\RendezVous;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats()
    {
        $totalPatients = Patient::count();
        $totalMedecins = Medecin::count();

        $rdvParStatut = RendezVous::selectRaw('statut, COUNT(*) as count')
            ->groupBy('statut')
            ->pluck('count', 'statut');

        $rdvAujourdhui = RendezVous::whereDate('date_heure', today())->count();
        $rdvAVenir = RendezVous::where('date_heure', '>', now())->count();

        return response()->json([
            'total_patients'     => $totalPatients,
            'total_medecins'     => $totalMedecins,
            'rdv_par_statut'     => $rdvParStatut,
            'rdv_aujourdhui'     => $rdvAujourdhui,
            'rdv_a_venir'        => $rdvAVenir,
        ]);
    }
}