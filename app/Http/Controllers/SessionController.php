<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActiveSession;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function index()
    {
        $sessions = ActiveSession::where('user_id', Auth::id())->get();

        return view('sessions.index', ['sessions' => $sessions]);
    }

    public function destroy($id)
    {
        $session = ActiveSession::findOrFail($id);

        if ($session->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $session->delete();

        return redirect()->route('sessions.index')->with('success', 'Session terminated successfully.');
    }
}
