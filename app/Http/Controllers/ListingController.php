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

    //show create form
    public function create(){
        return view('listings.create');
    }

    //store listing data
    public function store(Request $request){
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

        Listing::create($formField);

        return redirect('/')->with('message', 'Listing created successfully!');
    }
}
