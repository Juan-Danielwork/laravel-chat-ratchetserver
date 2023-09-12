<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\ConfirmMessage;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users              = User::where('id', '!=', auth()->user()->id)->get();
        $user_confirm       = ConfirmMessage::where('user_id', auth()->user()->id)->get()->toArray();

        $unread_by_users    = array();
        foreach ($user_confirm as $key => $row) {
            $unread_by_users[$row['recv_id']] = $row['unread_count'];
        }
        
        foreach ($users as $key => $user) {
            $users[$key]['unread_count'] = isset($unread_by_users[$user['id']]) ? $unread_by_users[$user['id']] : 0;
        }
        // print_r(compact('users'));exit;
        return view('chat.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dObj               = new \stdClass();
        $dObj->user_id      = auth()->user()->id;
        $dObj->recv_id      = $request->input('recv_id');
        $unread_count       = ConfirmMessage::updateConfirm($dObj);
        return response($unread_count, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $recv_id = $request->input('recv_id');
        $msg = DB::table('messages')
                    ->select('*', DB::raw("DATE_FORMAT(created_at, '%b %d %H:%i') as msg_date"))
                    ->where([['user_id', auth()->user()->id], ['recv_id', $recv_id]])
                    ->orWhere([['recv_id', auth()->user()->id], ['user_id', $recv_id]])
                    ->get()
                    ->toArray();

        ConfirmMessage::where([['user_id', auth()->user()->id], ['recv_id', $recv_id]])->delete();
        return response()->json($msg);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($recv_id)
    {
        DB::table('messages')
                    ->where([['user_id', auth()->user()->id], ['recv_id', $recv_id]])
                    ->orWhere([['recv_id', auth()->user()->id], ['user_id', $recv_id]])
                    ->delete();
        return response('ok', 200);
    }
}
