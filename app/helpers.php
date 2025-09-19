<?php

use App\Models\Profile;


function document_path() 
{    
    $arr_path = explode("/", request()->path());
    return $arr_path[0];
}

function avatar() {

    $avatar = 'no-pic.png';
    $profile = Profile::where('user_id', Auth()->user()->id)->first();

    if(!is_null($profile)) {
        $avatar = $profile->picture;
    }

    return $avatar;

}