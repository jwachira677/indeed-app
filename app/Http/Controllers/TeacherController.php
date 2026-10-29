<?php

namespace App\Http\Controllers;

use App\Models\Role;
Use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TeacherController extends Controller
{
     public function index(Request $request)
    {
        try {
            // Option A: Hardcoded ID based on your MySQL output
            // $teachers = User::where('role_id', 4)->paginate(15);

            // Option B: Fetch dynamically using the slug (Recommended)
            $teacherRole = Role::where('slug', 'teacher')->first();
            
            $query = User::query();

            if ($teacherRole) {
                $query->where('role_id', $teacherRole->id);
            } else {
                // Fallback if the role doesn't exist, return empty
                $query->whereRaw('1 = 0'); 
            }

            // Optional: Add search filtering
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone_number', 'like', "%{$search}%");
                });
            }

            $teachers = $query->latest()->paginate(15)->withQueryString();

            return view('teachers.index', compact('teachers'));

        } catch (\Exception $e) {
            \Log::error('Error fetching teachers: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load teachers list.');
        }
    }
}