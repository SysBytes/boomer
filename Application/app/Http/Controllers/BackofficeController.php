<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BackofficeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Fila 1 — conteos generales
        $totalClients             = \App\Models\Client::where('active', 1)->count();
        $totalWorks               = \App\Models\Work::where('active', 1)->count();
        $totalMaterials           = \App\Models\Material::where('active', 1)->count();
        $totalPendingRequisitions = \App\Models\Requisition::where('status', 'pending')->count();

        // Fila 2 — semáforos operacionales
        $totalNoStock             = \App\Models\Material::where('active', 1)->where('stock', '<=', 0)->count();
        $totalApproved            = \App\Models\Requisition::where('status', 'approved')->count();
        $totalPurchased           = \App\Models\Requisition::where('status', 'purchased')->count();

        // Tabla — últimas 5 requisiciones
        $latestRequisitions = \App\Models\Requisition::with(['work.client', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('backoffice.backoffice', compact(
            'totalClients',
            'totalWorks',
            'totalMaterials',
            'totalPendingRequisitions',
            'totalNoStock',
            'totalApproved',
            'totalPurchased',
            'latestRequisitions'
        ));
    }

    public function dashboard(Request $request)
    {
        $user = auth()->user();
        
        if ($user->role === 'developer' || $user->role === 'admin') {
            $query = \App\Models\Requisition::with('work.client')->orderBy('created_at', 'desc');
        } else {
            $query = $user->requisitions()->with('work.client')->orderBy('created_at', 'desc');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requisitions = $query->get();
        return view('backoffice.dashboard', compact('requisitions'));
    }
}
