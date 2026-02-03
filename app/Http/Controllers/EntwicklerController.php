<?php

namespace App\Http\Controllers;

use App\Models\Entwickler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EntwicklerController extends Controller
{
    public function index()
    {
        $entwicklers = Entwickler::orderBy('name')->paginate(15);
        return view('entwicklers.index', compact('entwicklers'));
    }

    public function create()
    {
        return view('entwicklers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:entwicklers',
            'phone' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'active' => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('entwicklers', 'public');
        }

        $validated['active'] = $request->has('active');

        Entwickler::create($validated);

        return redirect()->route('entwicklers.index')
            ->with('success', 'Entwickler erfolgreich erstellt.');
    }

    public function show(Entwickler $entwickler)
    {
        $entwickler->load('maintenanceReports.website.client');
        return view('entwicklers.show', compact('entwickler'));
    }

    public function edit(Entwickler $entwickler)
    {
        return view('entwicklers.edit', compact('entwickler'));
    }

    public function update(Request $request, Entwickler $entwickler)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:entwicklers,email,' . $entwickler->id,
            'phone' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
            'active' => 'boolean',
        ]);

        if ($request->hasFile('photo')) {
            if ($entwickler->photo) {
                Storage::disk('public')->delete($entwickler->photo);
            }
            $validated['photo'] = $request->file('photo')->store('entwicklers', 'public');
        }

        $validated['active'] = $request->has('active');

        $entwickler->update($validated);

        return redirect()->route('entwicklers.index')
            ->with('success', 'Entwickler erfolgreich aktualisiert.');
    }

    public function destroy(Entwickler $entwickler)
    {
        if ($entwickler->photo) {
            Storage::disk('public')->delete($entwickler->photo);
        }

        $entwickler->delete();

        return redirect()->route('entwicklers.index')
            ->with('success', 'Entwickler erfolgreich gelöscht.');
    }
}
