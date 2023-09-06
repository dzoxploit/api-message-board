<?php

namespace App\Http\Controllers\API;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
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
                                    ->orWhereHas('user', function ($query) use ($searchQuery) {
                                        $query->where('name', 'LIKE', '%' . $searchQuery . '%')
                                            ->orWhere('email', 'LIKE', '%' . $searchQuery . '%');
                                    })
                                    ->with('user') // Load relasi user
                                    ->paginate(10);
                return response()->json($messages, 200);    
            }else{
                $messages= Message::with('user')->paginate(10);
                return response()->json($messages, 200);
            }

        }catch(\Exception $e){
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try{
            $messages = Message::with('user')->find($id);
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
            'user_id' => $user->id,
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

        $user = User::where('id', $message->user_id)->firstOrFail();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->save();

        return response()->json(['message' => 'User and Message updated successfully']);
    }

    public function destroy($id)
    {
        try{
            $message = Message::find($id);

            if (!$message) {
                return response()->json(['message' => 'Message not found'], 404);
            }

            $user = $message->user;

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            if ($message->user_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $message->delete();
            $user->delete(); 

            return response()->json(['message' => 'Message deleted successfully']);
        } catch(\Exception $e) {
            return response()->json(['message' => 'An error occurred', 'error' => $e->getMessage()], 500);
        }
    }

}
