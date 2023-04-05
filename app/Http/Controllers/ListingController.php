<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{
    //show all listing
    public function index(){
        // dd(request('tag'));
        return view('listings.index', [
            'listings' => Listing::latest()->filter(request(['tag', 'search']))->paginate(6)
        ]);
    }


    //single listing
    public function show(Listing $listing){
        return view('listings.show', [
            'listing' => $listing
        ]);
    }

    //show edit form
    public function edit(Listing $listing){
        return view('listings.edit', [
            'listing' => $listing
        ]);
    }

    //show create form
    public function create(){
        return view('listings.create');
    }

    //delete listing
    public function destroy(Listing $listing){
        //make sure logged in user is the owner of the listing
        // if($listing->user_id!=auth()->id()){
        //     abort(403, 'Unauthorized Action');
        // }

        $listing->delete();
        return redirect('/')->with('message', 'Listing deleted successfully');
    }

    //update listing data
    public function update(Request $request, Listing $listing){
        // dd($request->file('logo'));

        //dd($listing->user_id);
        // dd(auth()->id());

        //make sure logged in user is the owner of the listing
        // if($listing->user_id != auth()->id()){
        //     abort(403, 'Unauthorized Action');
        // }


        $formField = $request->validate([
            'title' => 'required',
            'company' => ['required'],
            'location' => 'required',
            'descriptions' =>'required',
            'website' =>'required',
            'email' => ['required', 'email'],
            'tags' =>'required',
        ]);

        if($request->hasFile('logo')){
            $formField['logo'] = $request->file('logo')->store('logos', 'public');
        }

        // dd($formField);
        // dd(Listing::create($formField));

        $listing->update($formField);

        return back()->with('message', 'Listing update successfully!');
    }


    //Create new listing
    public function store(Request $request){
        // dd($request->file('logo'));
        $formField = $request->validate([
            'title' => 'required',
            'company' => ['required', Rule::unique('listings', 'company')],
            'location' => 'required',
            'descriptions' =>'required',
            'website' =>'required',
            'email' => ['required', 'email'],
            'tags' =>'required',
        ]);

        if($request->hasFile('logo')){
            $formField['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $formField['user_id'] = auth()->id();

        // dd($formField);
        // dd(Listing::create($formField));

        Listing::create($formField);

        return redirect('/')->with('message', 'Listing created successfully!');
    }

    //manage listing based on user
    public function manage(Request $request, Listing $listing){
        return view('listings.manage', ['listings' => auth()->user()->listing()->get()]);
    }
}
