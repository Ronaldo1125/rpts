<?php

namespace App\Http\Controllers;

use App\Models\Cipg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCipgRequest;

class CipgController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cipgs = Cipg::all();

        return view('cipgs.index', compact('cipgs'));
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
    public function store(StoreCipgRequest $request)
    {

        $request->validated();

        //dd($request->all());

        // Save Project Data
        $cipg = Cipg::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => Auth::id(),
        ]);
        

        // Save Image on the Attachments Table
        if($request->document) {
            //dd($request->document);
            foreach ($request->input('document', []) as $file) {
                $cipg->addMedia(storage_path('app/media/' . $file))->toMediaCollection('cipg_submission');
            }
        }

        toast('CIPG Data Stored Successfully!','success');
        
        return redirect()->route('cipgs.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cipg $cipg)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cipg $cipg)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cipg $cipg)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cipg $cipg)
    {
        //
    }

    public function storeMedia(Request $request){

       $path = storage_path('app/media');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $request->file('file');

        //$name = time().'.'.$file->getClientOriginalExtension();

        $name = uniqid() . '_' . trim($file->getClientOriginalName());

        $file->move($path, $name);

        return response()->json([
            'name'          => $name,
            'original_name' => $file->getClientOriginalName(),
        ]);
    }
}
