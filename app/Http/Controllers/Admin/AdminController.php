<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $leads = Lead::query()
            ->with(['diagnoses' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q)  use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Dashboard', [
            'leads' => $leads,
            'filters' => $request->only(['search', 'type'])
        ]);
    }

    public function show(Lead $lead)
    {
        return Inertia::render('Admin/Show', [
            'lead' => $lead->load([
                'answers.question.category',
                'answers.option',
                'diagnoses'
            ])
        ]);
    }
}
