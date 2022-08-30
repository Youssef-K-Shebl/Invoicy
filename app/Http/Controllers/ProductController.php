<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{


    function __construct()
    {

        $this->middleware('permission:المنتجات', ['only' => ['index']]);
        $this->middleware('permission:اضافة منتج', ['only' => ['store']]);
        $this->middleware('permission:تعديل منتج', ['only' => ['update']]);
        $this->middleware('permission:حذف منتج', ['only' => ['destroy']]);

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('products.products', ['products' => Product::all()]);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $attributes = request()->validate([
            'product_name' => ['required', Rule::unique('products', 'product_name')],
            'description' => '',
            'section_id' => [Rule::exists('sections','id')]
        ], [
            'product_name.required' => 'يرجي ادخال اسم القسم',
            'product_name.unique' => 'اسم القسم مسجل مسبقا',
            'section_id.exists' => 'القسم غير موجود'
        ]);

        Product::create($attributes);
        return redirect('/products')->with('Add', 'Product added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {

        if (!Product::find($request['id'])) {
            return redirect('/products')->with('Error', 'This record not found');
        }


        $attributes = $request->validate([
            'product_name' => ['required', Rule::unique('products', 'product_name')->ignore($request->id)],
            'description' => '',
            'section_name' => [Rule::exists('sections','section_name')],
            'id' => ''
        ], [
            'section_name.required' => 'يرجي ادخال اسم القسم',
            'section_name.unique' => 'اسم القسم مسجل مسبقا'
        ]);
        $attributes['section_id'] = Section::where('section_name', $request->section_name)->first()->id;
        unset($attributes['section_name']);



        $product->update($attributes);


        return redirect('/products')->with('Edit', 'تم تعديل المنتج بنجاح');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect('/products')->with('Delete', 'تم تعديل المنتج بنجاح');
    }
}
