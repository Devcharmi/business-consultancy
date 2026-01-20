<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\ExpertiseManager;
use App\Models\Segment;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:user.allow')->only(['index', 'show']);
        $this->middleware('permission:user.create')->only(['store']);
        $this->middleware('permission:user.edit')->only(['update']);
        $this->middleware('permission:user.delete')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $request->all();
            $columns = ['id', 'name', 'email', 'phone']; // example columns

            // 🔥 Base query (excluding Super Admin)
            $query = User::Filters($data, $columns)
                // ->whereDoesntHave('roles', function ($query) {
                //     $query->where('name', 'Super Admin');
                // })
                ->with(['roles']);

            // Clone query for counting
            $tableDataCount = (clone $query)->count();

            // Fetch data
            $tableData = $query->get()->map(function ($user) {
                $user->role_names = $user->roles->pluck('name')->implode(', ');
                return $user;
            });

            // Prepare DataTables response
            $response = [
                'iTotalDisplayRecords' => $tableDataCount,
                'iTotalRecords' => $tableDataCount,
                'draw' => intval(collect($data)->get('draw')),
                'aaData' => $tableData->toArray(),
            ];

            return $response;
        }

        return view('admin.user_manager.index');
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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
            ],

            // 'designation' => ['required'],
            // 'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        try {
            $profileImagePath = null;

            // 🖼 Handle profile image upload
            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $path = 'uploads/user/';
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path($path), $filename);
                $profileImagePath = $path . $filename;
            }

            // 👤 Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'original_password' => $request->password,
                'profile_image' => $profileImagePath,
            ]);

            $user->expertiseManagers()->sync(
                $request->expertise_manager_ids
            );

            // ✅ Assign role
            $user->syncRoles($request->role);

            // Auto assign all permissions of the role
            $role = Role::find($request->role);
            if ($role) {
                $permissions = $role->permissions->pluck('name')->toArray();
                $user->syncPermissions($permissions);
            }

            /* ✅ Auto verify email */
            if (! $user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
                event(new Verified($user));
            }

            return response()->json([
                'success' => true,
                'message' => 'User created successfully!',
            ], 200);
        } catch (\Throwable $th) {
            Log::error('User creation failed: ' . $th->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while creating user!',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        $expertises = ExpertiseManager::activeExpertise();
        if ($id != 'new') {
            $userData = User::find($id);
            $userRole = $userData->roles->pluck('id')->first(); // assuming single role
            // ✅ Get selected expertise IDs from pivot
            $userExpertiseIds = $userData->expertiseManagers->pluck('id')->toArray();
            return view('admin.user_manager.user-form', [
                'userData' => $userData,
                'roles' => $roles,
                'userRole' => $userRole,
                'expertises' => $expertises,
                'userExpertiseIds' => $userExpertiseIds,
            ]);
        } else {
            return view('admin.user_manager.user-form', compact('roles', 'expertises'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $user = User::findOrFail($id);

        // 🧩 Validation rules
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
            ],
            // 'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            // 🌟 Update basic info
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;

            if ($request->hasFile('profile_image')) {
                $profileImage = $request->file('profile_image');
                // Log::info('profileImage - ' . $profileImage);
                if ($user->profile_image) {
                    ImageHelper::deleteImage('uploads/users/profile/' . $user->profile_image);
                }
                $imagePath = ImageHelper::uploadImageSimple($profileImage, 'uploads/users/profile');
                $user->profile_image = $imagePath;
            }
            // Remove images if flagged
            if ($request->remove_profile_image) {
                if ($user->profile_image) {
                    ImageHelper::deleteImage('uploads/users/profile/' . $user->profile_image);
                }
                $user->profile_image = null;
            }

            // 🔑 Update password (only if provided)
            if (!empty($request->password)) {
                // If editing existing user, check current password if required
                // if (!empty($request->current_password)) {
                //     if (!Hash::check($request->current_password, $user->password)) {
                //         return response()->json([
                //             'success' => false,
                //             'message' => 'Current password is incorrect!',
                //         ], 422);
                //     }
                // }

                $user->password = Hash::make($request->password);
                $user->original_password = $request->password;
            }

            $user->save();

            $user->expertiseManagers()->sync(
                $request->expertise_manager_ids
            );

            // Step 4: Update role ONLY IF CHANGED
            $oldRole = $user->getRoleNames()->first();
            $newRole = $request->role;

            if ($oldRole != $newRole) {

                // Update role
                $user->syncRoles($newRole);

                // Sync permissions based on new role
                $role = Role::find($newRole);

                if ($role) {
                    $permissions = $role->permissions->pluck('name')->toArray();
                    $user->syncPermissions($permissions);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully!',
            ], 200);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while updating user!',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['success' => true, 'message' => 'User deleted successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }
}
