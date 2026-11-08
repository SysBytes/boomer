<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use App\Models\Work;
use App\Models\MaterialCategory;
use App\Models\RequisitionComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequisitionCreatedSupervisorMail;

class RequisitionController extends Controller
{
    /**
     * Show the interface for requesting materials (Technician & Admin view)
     */
    public function createMaterialRequest()
    {
        $user = Auth::user();

        // 1. Obras asignadas o todas si es admin
        if ($user->hasAnyRole(['admin', 'developer'])) {
            $works = Work::where('active', 1)->with('location.client')->get();
        } elseif ($user->hasRole('supervisor')) {
            $supervised = $user->supervisedWorks()->where('active', 1)->with('location.client')->get();
            $assigned = $user->works()->where('active', 1)->with('location.client')->get();
            $works = $supervised->merge($assigned)->unique('work_id');
        } else {
            $works = $user->works()->where('active', 1)->with('location.client')->get();
        }

        $works = $works->map(function($work) {
            if ($work->location) {
                $work->location->location_name = mb_strtoupper($work->location->location_name, 'UTF-8');
            }
            $work->work_name = mb_strtoupper($work->work_name, 'UTF-8');
            return $work;
        })->sortBy(function($work) {
            return ($work->location->location_name ?? '') . ' - ' . ($work->work_name ?? '');
        });

        // 2. Categorías y materiales
        $categories = MaterialCategory::with([
            'materials' => function ($query) {
                $query->where('active', 1);
            }
        ])->where('active', 1)->get();

        return view('backoffice.requisition.request', compact('works', 'categories'));
    }

    /**
     * Store the material requisition from the dashboard
     */
    public function storeMaterialRequest(Request $request)
    {
        $request->validate([
            'work_id' => 'required|exists:works,work_id',
            'materials' => 'required|array|min:1',
            'materials.*.id' => 'required|exists:materials,material_id',
            'materials.*.quantity' => 'required|integer|min:1',
            'comment' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // 1. Crear requisición
            $requisition = Requisition::create([
                'user_id' => Auth::id(),
                'work_id' => $request->work_id,
                'status' => 'pending',
                'priority' => 'medium'
            ]);

            // 2. Asociar materiales a la requisición
            $materialsData = [];
            foreach ($request->materials as $mat) {
                $materialsData[$mat['id']] = ['quantity' => $mat['quantity']];
            }
            $requisition->materials()->attach($materialsData);

            // 3. Crear comentario si existe
            if ($request->filled('comment')) {
                RequisitionComment::create([
                    'requisition_id' => $requisition->requisition_id,
                    'user_id' => Auth::id(),
                    'comment' => $request->comment
                ]);
            }

            DB::commit();

            // 4. Enviar correo electrónico al supervisor de la obra
            // Recargamos el modelo con sus relaciones necesarias para el mail
            $requisition->load(['work.supervisor', 'user', 'comments', 'materials.category']);

            if ($requisition->work && $requisition->work->supervisor && $requisition->work->supervisor->email) {
                try {
                    $devEmails = $this->getDeveloperEmails();
                    $mail = Mail::to($requisition->work->supervisor->email);
                    if (!empty($devEmails)) {
                        $mail->bcc($devEmails);
                    }
                    $mail->send(new RequisitionCreatedSupervisorMail($requisition));
                } catch (\Exception $e) {
                    // Si falla el mail no detenemos al usuario, pero idealmente lo logueamos
                    \Log::error('No se pudo enviar el correo de requisición al supervisor: ' . $e->getMessage());
                }
            }

            return redirect()->route('dashboard')->with('success', 'La solicitud de materiales se ha enviado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al guardar la solicitud: ' . $e->getMessage())->withInput();
        }
    }


    /**
     * Show detail view from Dashboard with Comments
     */
    public function showDashboard(Requisition $requisition)
    {
        $user = Auth::user();
        // Seguridad: Verificar que pertece al técnico logueado o tiene privilegios administrativos
        if ($requisition->user_id !== $user->id && !$user->hasAnyRole(['developer', 'admin'])) {
            abort(403, 'Acceso denegado a esta solicitud.');
        }

        $requisition->load(['work', 'materials.category', 'comments.user']);

        return view('backoffice.requisition.request_detail', compact('requisition'));
    }

    /**
     * Add a comment to the requisition from the Dashboard
     */
    public function addCommentDashboard(Request $request, Requisition $requisition)
    {
        $user = Auth::user();
        if ($requisition->user_id !== $user->id && !$user->hasAnyRole(['developer', 'admin'])) {
            abort(403, 'Acceso denegado para comentar esta solicitud.');
        }

        $request->validate([
            'comment' => 'required|string|max:1000'
        ]);

        RequisitionComment::create([
            'requisition_id' => $requisition->requisition_id,
            'user_id' => Auth::id(),
            'comment' => $request->comment
        ]);

        return back()->with('success', 'Comentario añadido exitosamente.');
    }

    /**
     * Display a listing of the resource for Backoffice Administrators.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Requisition::with(['work.location.client', 'user'])->orderBy('created_at', 'desc');

        if ($user->hasRole('supervisor')) {
            $query->whereHas('work', function ($q) use ($user) {
                $q->where('supervisor_id', $user->id);
                $countryIds = $user->countries->pluck('id')->toArray();
                if (!empty($countryIds)) {
                    $q->whereHas('location.client', function($qc) use ($countryIds) {
                        $qc->whereIn('country_id', $countryIds);
                    });
                }
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requisitions = $query->get();

        return view('backoffice.requisition.index', compact('requisitions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $requisition = Requisition::with(['work', 'materials.category', 'comments.user'])->findOrFail($id);
        return view('backoffice.requisition.show', compact('requisition'));
    }

    /**
     * Add a comment to the requisition from backoffice.
     */
    public function addCommentBackoffice(Request $request, $id)
    {
        // if (Auth::user()->role !== 'admin') {
        //     abort(403, 'No autorizado.');
        // }

        $request->validate([
            'comment' => 'required|string|max:1000'
        ]);

        $requisition = Requisition::findOrFail($id);

        \App\Models\RequisitionComment::create([
            'requisition_id' => $requisition->requisition_id,
            'user_id' => Auth::id(),
            'comment' => $request->comment
        ]);

        return redirect()->back()->with('success', 'Observación gerencial añadida exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Requisition $requisition)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Requisition $requisition)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Requisition $requisition)
    {
        //
    }

    /**
     * Devolver requisición a estado pendiente.
     */
    public function returnToPending($id)
    {
        $requisition = Requisition::findOrFail($id);
        $user = Auth::user();

        if (!$user->hasRole('developer')) {
            abort(403, 'Solo los desarrolladores pueden devolver una requisición a estado pendiente.');
        }

        if ($requisition->status !== 'approved') {
            return redirect()->back()->with('error', 'Solo las requisiciones aprobadas pueden ser devueltas a pendiente.');
        }

        $requisition->status = 'pending';
        $requisition->date_approved = null;
        $requisition->approved_by = null;
        $requisition->save();

        return redirect()->route('requisitions.status', ['requisition' => $id, 'status' => 'pending'])->with('success', 'La requisición ha sido devuelta a estado Pendiente correctamente.');
    }

    /**
     * Aprobar requisición.
     */
    public function approve($id)
    {
        $requisition = Requisition::with('work')->findOrFail($id);
        $user = Auth::user();

        // Validación de permisos
        if (!$user->hasAnyRole(['developer', 'admin']) && !($user->hasRole('supervisor') && $requisition->work->supervisor_id == $user->id)) {
            abort(403, 'No tienes permisos para aprobar esta requisición.');
        }

        if ($requisition->status !== 'pending') {
            return redirect()->back()->with('error', 'Esta requisición ya no se encuentra pendiente.');
        }

        $requisition->status = 'approved';
        $requisition->date_approved = now();
        $requisition->approved_by = $user->id;
        $requisition->save();

        // Enviar correo a compras
        $purchasingEmails = \App\Models\User::whereHas('roles', function($q) {
            $q->where('slug', 'purchasing');
        })->where('active', 1)->pluck('email')->filter()->toArray();

        $adminEmails = $this->getAdminEmails();
        $devEmails = $this->getDeveloperEmails();

        $mainRecipients = !empty($purchasingEmails) ? $purchasingEmails : $adminEmails;

        if (!empty($mainRecipients) || !empty($devEmails)) {
            try {
                $mail = Mail::to(!empty($mainRecipients) ? $mainRecipients : $devEmails);
                if (!empty($mainRecipients) && !empty($devEmails)) {
                    $mail->bcc($devEmails);
                }
                $mail->send(new \App\Mail\RequisitionApprovedMail($requisition));
            } catch (\Exception $e) {
                \Log::error('Error enviando correo de aprobación de requisición: ' . $e->getMessage());
            }
        }

        return redirect()->route('requisitions.status', ['requisition' => $id, 'status' => 'approved'])->with('success', 'Requisición Aprobada. Se ha notificado al departamento de compras.');
    }

    /**
     * Change priority of a requisition
     */
    public function updatePriority(Request $request, $id)
    {
        $request->validate([
            'priority' => 'required|in:low,medium,high'
        ]);

        $requisition = Requisition::with('work')->findOrFail($id);
        $user = Auth::user();

        if ($requisition->status !== 'pending') {
            return redirect()->back()->with('error', 'La prioridad de una requisición solo puede modificarse mientras está pendiente de aprobación.');
        }

        // Admin, dev, or auth supervisor allows this
        if (!$user->hasAnyRole(['developer', 'admin']) && !($user->hasRole('supervisor') && $requisition->work->supervisor_id == $user->id)) {
            abort(403, 'No tienes permisos para modificar la prioridad de esta requisición.');
        }

        $requisition->priority = $request->priority;
        $requisition->save();

        return redirect()->back()->with('success', 'La prioridad ha sido actualizada exitosamente.');
    }

    /**
     * Rechazar requisición.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:1500'
        ]);

        $requisition = Requisition::with(['work', 'user'])->findOrFail($id);
        $user = Auth::user();

        // Validación de permisos
        if (!$user->hasAnyRole(['developer', 'admin']) && !($user->hasRole('supervisor') && $requisition->work->supervisor_id == $user->id)) {
            abort(403, 'No tienes permisos para rechazar esta requisición.');
        }

        if ($requisition->status !== 'pending') {
            return redirect()->back()->with('error', 'Esta requisición ya no se encuentra pendiente.');
        }

        $requisition->status = 'rejected';
        $requisition->reject_reason = $request->reject_reason;
        $requisition->rejected_by = $user->id;
        $requisition->date_rejected = now();
        $requisition->save();

        // Notificar al originador/supervisor
        $recipients = [];
        if ($requisition->work && $requisition->work->supervisor && $requisition->work->supervisor->email) {
            $recipients[] = $requisition->work->supervisor->email;
        }

        if (!empty($recipients)) {
            try {
                $mail = \Illuminate\Support\Facades\Mail::to($recipients);
                $mail->bcc($this->getDeveloperEmails());
                $mail->send(new \App\Mail\RequisitionRejectedMail($requisition));
            } catch (\Exception $e) {
                \Log::error('Error enviando correo de rechazo de requisición: ' . $e->getMessage());
            }
        }

        return redirect()->route('requisitions.status', ['requisition' => $id, 'status' => 'rejected'])->with('success', 'Requisición Rechazada.');
    }

    /**
     * Print View for the Requisition.
     */
    public function print($id)
    {
        $requisition = Requisition::with(['work.location.client', 'user', 'materials.category'])->findOrFail($id);
        $user = Auth::user();

        if ($requisition->status !== 'approved') {
            abort(403, 'Solo se pueden imprimir requisiciones aprobadas.');
        }

        if (!$user->hasAnyRole(['purchasing', 'developer', 'admin'])) {
            abort(403, 'No tienes permiso para imprimir.');
        }

        return view('backoffice.requisition.print', compact('requisition', 'user'));
    }

    /**
     * Show form to confirm purchase and append stock
     */
    public function purchaseReceiveForm($id)
    {
        $requisition = Requisition::with(['work.location.client', 'user', 'materials.category'])->findOrFail($id);
        $user = Auth::user();

        if ($requisition->status !== 'approved') {
            return redirect()->route('dashboard')->with('error', 'Solo se puede confirmar compra de requisiciones aprobadas.');
        }

        if (!$user->hasAnyRole(['purchasing', 'developer', 'admin'])) {
            abort(403, 'No tienes permiso para confirmar compras de proveedor.');
        }

        return view('backoffice.requisition.purchase', compact('requisition', 'user'));
    }

    /**
     * Process purchased quantities, sum to stock and mark as purchased
     */
    public function purchaseReceive(Request $request, $id)
    {
        $requisition = Requisition::with('materials')->findOrFail($id);
        $user = Auth::user();

        if ($requisition->status !== 'approved') {
            abort(403, 'Esta requisición ya no se encuentra en estado Aprobada.');
        }

        if (!$user->hasAnyRole(['purchasing', 'developer', 'admin'])) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->quantities as $material_id => $qty) {
                // Ignore zero received completely or just add 0 which is effectively the same.
                if ($qty > 0) {
                    $material = \App\Models\Material::findOrFail($material_id);
                    $material->stock += $qty;
                    $material->save();
                }
            }

            $requisition->status = 'purchased';
            $requisition->purchased_by = $user->id;
            $requisition->date_purchased = now();
            $requisition->save();

            DB::commit();

            // Notificar al originador y supervisor de que se hizo la compra
            $recipients = [];
            if ($requisition->work && $requisition->work->supervisor && $requisition->work->supervisor->email) {
                $recipients[] = $requisition->work->supervisor->email;
            }
            $recipients = array_unique($recipients);

            if (!empty($recipients)) {
                try {
                    $mail = \Illuminate\Support\Facades\Mail::to($recipients);
                    $mail->bcc($this->getDeveloperEmails());
                    $mail->send(new \App\Mail\RequisitionPurchasedMail($requisition));
                } catch (\Exception $e) {
                    \Log::error('Error enviando correo de requisición comprada: ' . $e->getMessage());
                }
            }

            return redirect()->route('requisitions.status', ['requisition' => $id, 'status' => 'purchased'])
                ->with('success', '¡Recepción de compra registrada! Se han sumado exitosamente los materiales al almacén.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error al procesar el ingreso de inventario: ' . $e->getMessage());
        }
    }

    /**
     * Mark requisition as Delivered and discount stock
     */
    public function deliver($id)
    {
        $requisition = Requisition::with(['work', 'user', 'materials'])->findOrFail($id);
        $user = Auth::user();

        if ($requisition->status !== 'purchased') {
            abort(403, 'Solo se pueden entregar órdenes que hayan sido registradas como Compradas.');
        }

        // Auth requirement: Supervisor of this work, dev, or admin
        if (!$user->hasAnyRole(['developer', 'admin']) && !($user->hasRole('supervisor') && $requisition->work->supervisor_id == $user->id)) {
            abort(403, 'No tienes permiso para marcar como entregada esta requisición.');
        }

        // Validate stock sufficiency for every material before committing
        $stockErrors = [];
        foreach ($requisition->materials as $mat) {
            $material = \App\Models\Material::findOrFail($mat->material_id);
            if ($material->stock < $mat->pivot->quantity) {
                $stockErrors[] = [
                    'material' => $material->material_name,
                    'requested' => $mat->pivot->quantity,
                    'available' => $material->stock,
                    'shortfall' => $mat->pivot->quantity - $material->stock,
                ];
            }
        }

        if (!empty($stockErrors)) {
            return back()->with('stock_errors', $stockErrors);
        }

        DB::beginTransaction();
        try {
            foreach ($requisition->materials as $mat) {
                // Descontar la cantidad solicitada (almacenada en pivot) del stock general
                $material = \App\Models\Material::findOrFail($mat->material_id);
                $material->stock -= $mat->pivot->quantity;
                $material->save();
            }

            $requisition->status = 'delivered';
            $requisition->delivered_by = $user->id;
            $requisition->date_delivered = now();
            $requisition->save();

            DB::commit();

            return redirect()->route('requisitions.status', ['requisition' => $id, 'status' => 'delivered'])
                ->with('success', '¡Bienes despachados exitosamente! La requisición está entregada y el inventario descontado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ocurrió un error en la entrega: ' . $e->getMessage());
        }
    }

    private function getDeveloperEmails()
    {
        return \App\Models\User::whereHas('roles', function($q) {
            $q->where('slug', 'developer');
        })->where('active', 1)->pluck('email')->filter()->toArray();
    }

    private function getAdminEmails()
    {
        return \App\Models\User::whereHas('roles', function($q) {
            $q->where('slug', 'admin');
        })->where('active', 1)->pluck('email')->filter()->toArray();
    }
}
