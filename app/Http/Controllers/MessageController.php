<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Validation\Rule;
use App\Rules\ValidEmailExtension;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        // Return a list of candidates
        try{
            
            if($request->get('search') != null){
                $searchQuery = $request->get('search');
                
                $messages = Message::where('content', 'LIKE', '%' . $searchQuery . '%')
                                    ->orWhereHas('users', function ($query) use ($searchQuery) {
                                        $query->where('name', 'LIKE', '%' . $searchQuery . '%')
                                            ->orWhere('email', 'LIKE', '%' . $searchQuery . '%');
                                    })
                                    ->with('users')
                                    ->orderBy('id','DESC') // Load relasi user
                                    ->paginate(10);
                return response()->json($messages, 200);    
            }else{
                $messages= Message::with('users')->orderBy('id','DESC')->paginate(10);
                return response()->json($messages, 200);
            }

        }catch(\Exception $e){
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try{
            $messages = Message::with('users')->find($id);
            if (!$messages) {
                return response()->json(['message' => 'Messages not found'], 404);
            }
            return response()->json($messages, 200);
        }catch(\Exception $e){
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->where(function ($query) use ($request) {
                    return $query->where('email', $request->input('email'));
                }),
                new ValidEmailExtension,
            ],
            'content' => 'required|string',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        ]);

        Message::create([
            'users_id' => $user->id,
            'content' => $request->input('content'),
        ]);

        return response()->json(['message' => 'User and Message created successfully'], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($id), // Untuk mengabaikan email saat validasi unik
                new ValidEmailExtension, // Validasi kustom untuk ekstensi email
            ],
            'content' => 'required|string',
        ]);

        // Cari pengguna (user) yang akan diupdate
        $message = Message::findOrFail($id);
        $message->content = $request->input('content');
        $message->save();

        $user = User::where('id', $message->users_id)->firstOrFail();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->save();

        return response()->json(['message' => 'User and Message updated successfully']);
    }

    public function destroy($id)
    {
        try{
            $message = Message::with('users')->find($id);

            if (!$message) {
                return response()->json(['message' => 'Message not found'], 404);
            }

            $user = $message->users;

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            if ($message->users_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $message->delete();

            $userDel = User::where('id',$message->users->id)->first();
            $userDel->delete(); 

            return response()->json(['message' => 'Message deleted successfully']);
        } catch(\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }
}
