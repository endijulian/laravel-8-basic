<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends Controller
{

    public function __construct(){
        $this->middleware('auth'); 
    }

    public function AllCat()
    {
        // $categories = DB::table('categories') //Query Builder
        //                 ->join('users', 'categories.user_id', 'users.id')
        //                 ->select('categories.*', 'users.name')
        //                 ->latest()->paginate(3);

        // $categories = DB::table('categories')->latest()->paginate(3);
        $categories = Category::latest()->paginate(3); //ORM
        $trashCat   = Category::onlyTrashed()->latest()->paginate(3);

        return view('admin.category.index', compact('categories', 'trashCat'));
    }

    public function AddCat(Request $request)
    {
        $validated = $request->validate(
            [
                'category_name' => 'required|unique:categories|max:255',
            ],
            [
                'category_name.required' => 'Please input category name',
            ]
        );

        //ORM
        Category::create([
            'category_name' => $request->category_name,
            'user_id'       => Auth::user()->id,
            'created_at'    => Carbon::now(),
        ]);

        // $category                   = new Category;
        // $category->category_name    = $request->category_name;
        // $category->user_id          = Auth::user()->id;
        // $category->save();

        // $data                   = array();
        // $data['category_name']  = $request->category_name;
        // $data['user_id']        = Auth::user()->id;
        // DB::table('categories')->insert($data);

        return Redirect()->back()->with('success', 'Category inserted successfull');
    }

    public function Edit($id)
    {
        // $categories = Category::find($id);
        $categories = DB::table('categories')->where('id', $id)->first();
        return view('admin.category.edit', compact('categories'));
    }

    public function Update(Request $request, $id)
    {
        //ORM
        // $categories = Category::find($id)->update([
        //     'category_name' => $request->category_name,
        //     'user_id'       => Auth::user()->id
        // ]);

        //Query Builder
        $data                       = array();
        $data['category_name']      = $request->category_name;
        $data['user_id']            = Auth::user()->id;
        DB::table('categories')->where('id', $id)->update($data);

        return Redirect()->route('all.category')->with('success', 'Category updated successfull');
    }

    public function SoftDelete($id)
    {
        $delete = Category::find($id)->delete();
        return Redirect()->back()->with('success', 'Category soft delete successfull');
    }

    public function Restore($id)
    {
        $delete = Category::withTrashed()->find($id)->restore();
        return Redirect()->back()->with('success', 'Category restore successfull');
    }

    public function Pdelete($id)
    {
        $delete = Category::onlyTrashed()->find($id)->forceDelete();
        return Redirect()->back()->with('success', 'Category permanently deleted');
    }
}
