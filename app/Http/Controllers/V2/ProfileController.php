<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profiles.index_v2');
    }

    public function update(Request $request)
    {
        $profile = Profile::where('user_id', Auth::id())->first();
        
        $profile->update([
            'mobile_number' => $request->mobile_number,
            'address' => $request->address
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!'
        ]);
    }

    public function updatePic(Request $request)
    {
        if ($request->hasFile('avatar')) {
            $manager = new ImageManager(new Driver());
            $avatar = $request->file('avatar');
            $userid = Auth::id();
             
            $newFilename = time() . '_' . $avatar->getClientOriginalName();
            $newFilenameToPng = pathinfo($newFilename, PATHINFO_FILENAME) . '.png';

            $img = $manager->read($avatar);
            $img->resize(300, 300);

            $img->save(public_path('images/' . $newFilenameToPng));
            
            $userProfile = Profile::where('user_id', $userid)->first();
            if ($userProfile->picture && file_exists(public_path('images/' . $userProfile->picture))) {
                unlink(public_path('images/' . $userProfile->picture));
            }
            $userProfile->picture = $newFilenameToPng;
            $userProfile->save(); 

            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully!',
                'picture' => asset('images/' . $newFilenameToPng)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No image uploaded.'
        ], 422);
    }

    public function updatePassword(Request $request) 
    {
        $user = Auth::user();

        if (Hash::check($request->current_password, $user->password)) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Current password does not match.'
        ], 422);
    }
}
