<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use MongoDB\Driver\Session;

class SectionController extends Controller
{


    function __construct()
    {

        $this->middleware('permission:الاقسام', ['only' => ['index']]);
        $this->middleware('permission:اضافة قسم', ['only' => ['store']]);
        $this->middleware('permission:تعديل قسم', ['only' => ['update']]);
        $this->middleware('permission:حذف قسم', ['only' => ['destroy']]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $sections = Section::all();
        return view('sections.sections', ['sections' => $sections]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store()
    {
        $attributes = request()->validate([
            'section_name' => ['required',Rule::unique('sections', 'section_name'), 'max:255'],
            'description' => ''
        ], [
            'section_name.required' => 'يرجي ادخال اسم القسم',
            'section_name.unique' => 'اسم القسم مسجل مسبقا'
        ]);
        $attributes['created_by'] = Auth::user()->email;

        Section::create($attributes);
        return redirect('/sections');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\section  $section
     * @return \Illuminate\Http\Response
     */
    public function show(section $section)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\section  $section
     * @return \Illuminate\Http\Response
     */
    public function edit(section $section)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\section  $section
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        if (!Section::find($request['id'])) {
            return redirect('/sections')->with('Error', 'This record not found');
        }
        $attributes = $request->validate([
            'section_name' => ['required', Rule::unique('sections', 'section_name')->ignore($request['id'])],
            'description' => '',
            'id' => ''
        ], [
            'section_name.required' => 'يرجي ادخال اسم القسم',
            'section_name.unique' => 'اسم القسم مسجل مسبقا'
        ]);
        $section = Section::find($request['id']);
        $section->update($attributes);

        return redirect('/sections')->with('Edit', 'تم تعديل القسم بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\section  $section
     * @return \Illuminate\Http\Response
     */
    public function destroy(Section $section)
    {
        $section->delete();
        return redirect('/sections')->with('Delete', 'Section deleted successfully');
    }
}
