<?php

namespace App\Http\Controllers;

use App\Models\Tree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TreeController extends Controller
{
   
    public function index()
    {
        $trees = Tree::with('plantedBy')->latest()->paginate(12);
        $statistics = [
            'total_trees' => Tree::count(),
            'planted_trees' => Tree::where('status', 'Planted')->count(),
            'not_yet_planted' => Tree::where('status', 'Not Yet')->count(),
            'sick_trees' => Tree::where('status', 'Sick')->count(),
            'dead_trees' => Tree::where('status', 'Dead')->count(),
            'my_trees' => Tree::where('planted_by_user', Auth::id())->count()
        ];

        return view('pages.trees', compact('trees', 'statistics'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'species' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'planting_date' => 'required|date',
            'status' => ['required', Rule::in(['Planted', 'Not Yet', 'Sick', 'Dead'])],
            'type' => ['required', Rule::in(['Fruit', 'Ornamental', 'Forest', 'Medicinal'])],
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:500',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $treeData = $request->only([
            'species', 'latitude', 'longitude', 'planting_date', 
            'status', 'type', 'description', 'address'
        ]);
        
        $treeData['planted_by_user'] = Auth::id();

     
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('trees', 'public');
                $imagePaths[] = $path;
            }
            $treeData['images'] = $imagePaths;
        }

        $tree = Tree::create($treeData);
        $tree->load('plantedBy');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tree added successfully!',
                'data' => $tree
            ], 201);
        }

        return redirect()->route('trees.index')->with('success', 'Tree added successfully!');
    }


    public function show(Tree $tree)
    {
        $tree->load('plantedBy');
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $tree
            ]);
        }

        return view('pages.tree-details', compact('tree'));
    }

   
    public function update(Request $request, Tree $tree)
    {
        // Check if user can edit this tree
        if ($tree->planted_by_user !== Auth::id() && !Auth::user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to edit this tree.'
            ], 403);
        }

        $request->validate([
            'species' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'planting_date' => 'required|date',
            'status' => ['required', Rule::in(['Planted', 'Not Yet', 'Sick', 'Dead'])],
            'type' => ['required', Rule::in(['Fruit', 'Ornamental', 'Forest', 'Medicinal'])],
            'description' => 'nullable|string',
            'address' => 'nullable|string|max:500',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $treeData = $request->only([
            'species', 'latitude', 'longitude', 'planting_date', 
            'status', 'type', 'description', 'address'
        ]);

      
        $existingImages = $tree->images ?? [];
        
       
        if ($request->has('images_to_delete')) {
            $imagesToDelete = json_decode($request->images_to_delete, true) ?? [];
            foreach ($imagesToDelete as $imageUrl) {
                // Extract path from URL
                $imagePath = str_replace(asset('storage/'), '', $imageUrl);
                if (in_array($imagePath, $existingImages)) {
                    Storage::disk('public')->delete($imagePath);
                    $existingImages = array_filter($existingImages, fn($img) => $img !== $imagePath);
                }
            }
        }

       
        if ($request->hasFile('images')) {
            $newImagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('trees', 'public');
                $newImagePaths[] = $path;
            }
            $existingImages = array_merge($existingImages, $newImagePaths);
        }

        $treeData['images'] = array_values($existingImages); // Re-index array

        $tree->update($treeData);
        $tree->load('plantedBy');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tree updated successfully!',
                'data' => $tree
            ]);
        }

        return redirect()->route('trees.index')->with('success', 'Tree updated successfully!');
    }

   
    public function destroy(Tree $tree)
    {
       
        if ($tree->planted_by_user !== Auth::id() && !Auth::user()->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this tree.'
            ], 403);
        }

      
        if ($tree->images) {
            foreach ($tree->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $tree->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tree deleted successfully!'
            ]);
        }

        return redirect()->route('trees.index')->with('success', 'Tree deleted successfully!');
    }

  
    public function mapData()
    {
        $trees = Tree::with('plantedBy')->get();
        
        return response()->json([
            'success' => true,
            'data' => $trees
        ]);
    }

 
    public function myTrees()
    {
        $trees = Tree::with('plantedBy')
            ->where('planted_by_user', Auth::id())
            ->latest()
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $trees
        ]);
    }
}
