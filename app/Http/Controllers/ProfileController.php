<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Http\Requests\ProfileUpdateRequest;



class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user_id = Auth()->user()->id;
        $userInfo = User::where('id','=', $user_id)->first();
        $userProfile = Profile::where('user_id','=', $user_id)->first();
        
        return view('profiles.index', compact('userInfo' , 'userProfile'));
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
    public function store(ProfileUpdateRequest $request)
    {
        $request->validated();

        //dd($request);

        Profile::create([
            'user_id' => $request->input('user_id'),
            'mobile_number' => $request->input('mobile_number'),
            'address' => $request->input('address'),
        ]);

        toast('Profile data added successfully!','success');

        return redirect()->route('profiles.index'); 
    }

    /**
     * Display the specified resource.
     */
    public function show(Profile $profile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Profile $profile)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Profile $profile)
    { 
        
        $profile->where('id', $request->id)->update([
            'mobile_number' => $request->mobile_number,
            'address' => $request->address
        ]);

        toast('Profile data updated successfully!','success');

        return redirect()->route('profiles.index');   
        
    }


    public function updatePic(Request $request)
    {
        
        if($request->hasFile('avatar'))
        {
            $manager = new ImageManager(new Driver());
            $avatar = $request->file('avatar');
            $userid = $request->user_id;
             
            $newFilename = time() . $avatar->getClientOriginalName();
            $newFilenameToPng = explode(".", $newFilename)[0] . '.png';

            $img = $manager->read($avatar);
            $img->resize(300,300);

            $img->save(public_path('images/' . $newFilenameToPng));
            
            $userProfile = Profile::where('user_id','=',$userid)->first();
            unlink(public_path('images/' . $userProfile->picture));
            $userProfile->picture = $newFilenameToPng;
            $userProfile->save(); 

            toast('User Profile Picture updated successfully!','success');

        }

        return redirect()->route('profiles.index');

    }

    public function updatePassword(Request $request) 
    {

        //$request->validated();

        $user = User::find($request->id);

        if(Hash::check($request->current_password, $user->password))
        {
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            toast('Password updated successfully!','success');
        } else {
            toast('Old password does not match.','error');
        }
        
        //return redirect()->back();
        return redirect()->route('profiles.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Profile $profile)
    {
        //
    }
}
