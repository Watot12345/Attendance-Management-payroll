<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\Request;

class CompanySettingController extends Controller
{
    public function index()
    {
        $settings = CompanySetting::all()->pluck('value', 'key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_email' => ['required', 'email', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:50'],
            'company_address' => ['nullable', 'string', 'max:255'],
            'work_start_time' => ['required', 'date_format:H:i'],
            'work_end_time' => ['required', 'date_format:H:i'],
            'regular_hours_per_day' => ['required', 'numeric', 'min:1', 'max:24'],
            'overtime_rate_30m' => ['required', 'numeric', 'min:0'],
            'late_penalty_30m' => ['required', 'numeric', 'min:0'],
            'sss_monthly_deduction' => ['required', 'numeric', 'min:0'],
            'insurance_monthly_deduction' => ['required', 'numeric', 'min:0'],
        ]);

        foreach ($validated as $key => $value) {
            CompanySetting::set($key, (string) $value);
        }

        return back()->with('success', 'Company system settings updated successfully.');
    }
}
