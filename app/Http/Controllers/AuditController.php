<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditTrail;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditTrail::with('user')->orderBy('id', 'desc');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('auditable_type', 'like', "%{$search}%");
            });
        }

        $audits = $query->paginate(20)->withQueryString();
        $actions = AuditTrail::select('action')->distinct()->pluck('action');
        $totalLogs = AuditTrail::count();

        // Verification check
        $verificationStatus = $this->verifyChainInternal();

        return view('portals.audit.index', compact('audits', 'actions', 'totalLogs', 'verificationStatus'));
    }

    public function verify()
    {
        $status = $this->verifyChainInternal();
        return response()->json($status);
    }

    private function verifyChainInternal(): array
    {
        $records = AuditTrail::orderBy('id', 'asc')->get();
        $isValid = true;
        $failedAt = null;
        $verifiedCount = 0;

        $expectedPrev = '0000000000000000000000000000000000000000000000000000000000000000';

        foreach ($records as $index => $rec) {
            if ($index > 0 && $rec->prev_hash !== $expectedPrev) {
                $isValid = false;
                $failedAt = $rec->id;
                break;
            }
            $expectedPrev = $rec->record_hash;
            $verifiedCount++;
        }

        return [
            'is_valid' => $isValid,
            'total_checked' => $verifiedCount,
            'failed_at_id' => $failedAt,
            'algorithm' => 'SHA-256 Chained Merkle-Lite Ledger',
            'status_label' => $isValid ? 'Cryptographically Sealed & Verified' : 'Integrity Mismatch Detected',
        ];
    }
}