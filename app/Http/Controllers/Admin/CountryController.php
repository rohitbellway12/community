<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::latest()->paginate(10);
        return view('admin.countries.index', compact('countries'));
    }

   public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:countries,name',
            'code' => 'required|string|max:3|unique:countries,code', // Restricted to 3 chars (e.g., IND)
            'iso_change' => 'sometimes',
            'iso_code' => 'required|string|max:2|unique:countries,iso_code', // Restricted to 2 chars (e.g., IN)
            'flag' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        Country::create($validated);

        return redirect()->route('admin.countries.index')->with('success', 'Country added successfully.');
    }

    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:countries,name,' . $country->id,
            'code' => 'required|string|max:3|unique:countries,code,' . $country->id, // Restricted to 3 chars
            'iso_code' => 'required|string|max:2|unique:countries,iso_code,' . $country->id, // Restricted to 2 chars
            'flag' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ]);

        $country->update($validated);

        return redirect()->route('admin.countries.index')->with('success', 'Country updated successfully.');
    }

    public function destroy(Country $country)
    {
        $country->delete();
        return redirect()->route('admin.countries.index')->with('success', 'Country deleted successfully.');
    }
}