<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UploadSetting;
use Illuminate\Http\Request;

class UploadSettingController extends Controller
{
    public function getSettings()
    {
        $setting = UploadSetting::latest()->first();
        if (!$setting) {
            $setting = UploadSetting::create([
                'max_file_size_mb' => 10,
                'allowed_types' => 'pdf,doc,docx,xls,xlsx,jpg,jpeg,png',
            ]);
        }
        return response()->json(['status' => 'success', 'data' => $setting]);
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'max_file_size_mb' => 'required|integer|min:1|max:500',
            'allowed_types' => 'required|string',
        ]);

        $setting = UploadSetting::latest()->first();
        if (!$setting) {
            $setting = new UploadSetting();
        }

        $setting->max_file_size_mb = $request->max_file_size_mb;
        $setting->allowed_types = $request->allowed_types;
        $setting->updated_by = auth()->id();
        $setting->save();

        return response()->json(['status' => 'success', 'message' => 'Cập nhật thành công!']);
    }
}
