<?php

namespace App\Http\Controllers;

use App\Models\TalentPoolEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TalentPoolController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $returnRoute = $request->input('return_to') === 'vacancies.index' ? 'vacancies.index' : 'home';
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255'],
            'area_of_interest' => ['required', Rule::in([
                'Technology',
                'Digital Marketing',
                'Operations',
                'Finance',
                'Business Development',
                'Human Resources',
                'Compliance',
            ])],
            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        if ($validator->fails()) {
            return to_route($returnRoute)
                ->withFragment('talent-pool')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        $cvPath = $request->file('cv')->store('talent-pool-cvs', 'local');

        TalentPoolEntry::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'area_of_interest' => $data['area_of_interest'],
            'cv_path' => $cvPath,
        ]);

        return to_route($returnRoute)->withFragment('talent-pool')->with('talent_pool_success', true);
    }
}
