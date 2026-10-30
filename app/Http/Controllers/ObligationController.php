<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleObligation;
use App\Models\VehicleObligationInstallment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ObligationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $vehicles = $user->vehicles()->get();

        $query = VehicleObligation::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->with(['vehicle', 'installments'])
            ->orderBy('due_date');

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $obligations = $query->paginate(15)->withQueryString();

        $totalPending = VehicleObligation::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->where('status', '!=', 'paid')
            ->sum('amount');

        $totalPaid = VehicleObligation::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->where('status', 'paid')
            ->sum('amount_paid');

        return view('ipva-taxas.index', compact('obligations', 'vehicles', 'totalPending', 'totalPaid'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => ['required', Rule::exists('vehicles', 'id')->where('user_id', $request->user()->id)->whereNull('deleted_at')],
            'type' => 'required|in:ipva,licensing,insurance,dpvat,fine,inspection',
            'title' => 'required|string|max:150',
            'reference_year' => 'required|integer|min:2000|max:'.(date('Y') + 5),
            'due_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01|max:99999999',
            'status' => 'required|in:pending,paid',
            'paid_at' => 'nullable|date',
            'amount_paid' => 'nullable|numeric|min:0|max:99999999',
            'payment_mode' => 'nullable|in:single,installments',
            'installments_count' => 'nullable|integer|min:1|max:12',
            'notes' => 'nullable|string|max:2000',
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        if ($vehicle->user_id !== $request->user()->id) {
            abort(403);
        }

        $isInstallment = ($request->input('payment_mode') === 'installments') && ((int) $request->input('installments_count', 1) > 1);
        $totalInstallments = $isInstallment ? (int) $request->input('installments_count') : 1;

        $totalAmount = (float) $validated['amount'];
        $validated['total_installments'] = $totalInstallments;

        if (!$isInstallment && $validated['status'] === 'paid' && empty($validated['paid_at'])) {
            $validated['paid_at'] = now();
            $validated['amount_paid'] = $totalAmount;
        }

        // Criar registro mestre da obrigação
        $obligation = VehicleObligation::create([
            'vehicle_id' => $vehicle->id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'reference_year' => $validated['reference_year'],
            'due_date' => $validated['due_date'],
            'amount' => $totalAmount,
            'status' => $validated['status'],
            'paid_at' => $validated['paid_at'] ?? null,
            'amount_paid' => $validated['amount_paid'] ?? 0,
            'total_installments' => $totalInstallments,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Criar parcelas na tabela associativa vehicle_obligation_installments
        if ($isInstallment) {
            $baseInstallmentAmount = round($totalAmount / $totalInstallments, 2);
            $accumulated = 0;

            for ($i = 1; $i <= $totalInstallments; $i++) {
                // Ajusta arredondamento na última parcela para bater o valor exato
                $installmentValue = ($i === $totalInstallments) 
                    ? round($totalAmount - $accumulated, 2) 
                    : $baseInstallmentAmount;
                $accumulated += $installmentValue;

                $installmentDueDate = Carbon::parse($validated['due_date'])->addMonths($i - 1);

                VehicleObligationInstallment::create([
                    'vehicle_obligation_id' => $obligation->id,
                    'installment_number' => $i,
                    'total_installments' => $totalInstallments,
                    'due_date' => $installmentDueDate->format('Y-m-d'),
                    'amount' => $installmentValue,
                    'status' => 'pending',
                    'paid_at' => null,
                    'amount_paid' => null,
                ]);
            }
        } else {
            // Se for parcela única (à vista)
            VehicleObligationInstallment::create([
                'vehicle_obligation_id' => $obligation->id,
                'installment_number' => 1,
                'total_installments' => 1,
                'due_date' => $validated['due_date'],
                'amount' => $totalAmount,
                'status' => $validated['status'],
                'paid_at' => ($validated['status'] === 'paid') ? now() : null,
                'amount_paid' => ($validated['status'] === 'paid') ? $totalAmount : null,
            ]);
        }

        return back()->with('success', 'Obrigação fiscal cadastrada com sucesso! ' . ($isInstallment ? "Geradas {$totalInstallments} parcelas associadas." : ''));
    }

    public function payInstallment(Request $request, VehicleObligationInstallment $installment)
    {
        $obligation = $installment->obligation;
        if ($obligation->vehicle->user_id !== auth()->id()) {
            abort(403);
        }

        // Quitar esta parcela
        $installment->update([
            'status' => 'paid',
            'paid_at' => now(),
            'amount_paid' => $installment->amount,
        ]);

        // Recalcular totais na obrigação mestre
        $totalPaid = $obligation->installments()->where('status', 'paid')->sum('amount_paid');
        $allPaid = $obligation->installments()->where('status', '!=', 'paid')->count() === 0;

        $obligation->update([
            'amount_paid' => $totalPaid,
            'status' => $allPaid ? 'paid' : 'pending',
            'paid_at' => $allPaid ? now() : null,
        ]);

        return back()->with('success', "Parcela {$installment->installment_number}/{$installment->total_installments} (R$ " . number_format($installment->amount, 2, ',', '.') . ") quitada com sucesso!");
    }

    public function reopenInstallment(Request $request, VehicleObligationInstallment $installment)
    {
        $obligation = $installment->obligation;
        if ($obligation->vehicle->user_id !== auth()->id()) {
            abort(403);
        }

        // Reabrir esta parcela
        $installment->update([
            'status' => 'pending',
            'paid_at' => null,
            'amount_paid' => null,
        ]);

        // Recalcular totais na obrigação mestre
        $totalPaid = $obligation->installments()->where('status', 'paid')->sum('amount_paid');

        $obligation->update([
            'amount_paid' => $totalPaid,
            'status' => 'pending',
            'paid_at' => null,
        ]);

        return back()->with('success', "Parcela {$installment->installment_number}/{$installment->total_installments} reaberta como pendente.");
    }

    public function markAsPaid(Request $request, VehicleObligation $obligation)
    {
        if ($obligation->vehicle->user_id !== auth()->id()) {
            abort(403);
        }

        // Quitar todas as parcelas associadas
        $obligation->installments()->update([
            'status' => 'paid',
            'paid_at' => now(),
            'amount_paid' => \Illuminate\Support\Facades\DB::raw('amount'),
        ]);

        $obligation->update([
            'status' => 'paid',
            'paid_at' => now(),
            'amount_paid' => $obligation->amount,
        ]);

        return back()->with('success', 'Status da obrigação e todas as suas parcelas atualizadas para PAGO!');
    }

    public function destroy(VehicleObligation $obligation)
    {
        if ($obligation->vehicle->user_id !== auth()->id()) {
            abort(403);
        }

        $obligation->delete();
        return back()->with('success', 'Registro excluído com sucesso.');
    }
}
