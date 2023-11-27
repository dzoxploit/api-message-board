<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PeopleGamification;
use App\Models\User;
use Illuminate\Validation\Rule;
use App\Rules\ValidEmailExtension;

class PeopleGamificationController extends Controller
{
    public function index(Request $request)
    {
        // Return a list of candidates
        try{
            
            if($request->get('search') != null){
                $searchQuery = $request->get('search');
                
                $people_gamification = PeopleGamification::where('nama_character', 'LIKE', '%' . $searchQuery . '%')
                                    ->orWhere('strength_power', 'LIKE', '%' . $searchQuery . '%')
                                    ->orderBy('id','DESC') // Load relasi user
                                    ->paginate(10);
                return response()->json($people_gamification, 200);    
            }else{
                $people_gamification = PeopleGamification::orderBy('id','DESC')->paginate(10);
                return response()->json($people_gamification, 200);
            }

        }catch(\Exception $e){
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try{
            $people_gamification = PeopleGamification::find($id);
            if (!$people_gamification) {
                return response()->json(['message' => 'People Gamification not found'], 404);
            }
            return response()->json($messages, 200);
        }catch(\Exception $e){
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_character' => 'required|string',
            'strength_power' => 'required|integer',
        ]);

        PeopleGamification::create([
            'nama_character' => $request->input('nama_character'),
            'strength_power' => $request->input('strength_power'),
        ]);

        return response()->json(['message' => 'People Gamification created successfully'], 201);
    }

    public function update(Request $request, $id)
    {
         $request->validate([
            'nama_character' => 'required|string',
            'strength_power' => 'required|integer',
        ]);


        // People Gamification
        $people_gamification = PeopleGamification::findOrFail($id);
        $people_gamification->nama_character = $request->input('nama_character');
        $people_gamification->strength_power = $request->input('strength_power');
        $people_gamification->save();

        return response()->json(['message' => 'People Gamification updated successfully']);
    }

    public function destroy($id)
    {
        try{
            $people_gamification= PeopleGamification::find($id);
            $people_gamification->delete();

            return response()->json(['message' => 'People Gamification deleted successfully']);
        } catch(\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
}
