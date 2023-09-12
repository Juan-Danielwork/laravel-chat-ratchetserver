<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class ConfirmMessage extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'confirm_message';

    public function updateConfirm($pObj = null) {
        if (!$pObj) return;

        $user_confirm_obj = DB::table('confirm_message')->where([
            'user_id' => $pObj->user_id, 
            'recv_id' => $pObj->recv_id
        ]);
        if (!empty($user_confirm_obj->get()->toArray())) {
            $user_confirm_row = $user_confirm_obj->first();
            $user_confirm_obj->update(['unread_count' => $user_confirm_row->unread_count + 1]);

            return $user_confirm_row->unread_count + 1;
        } else {
            DB::table('confirm_message')->insert([
                'user_id'       => $pObj->user_id, 
                'recv_id'       => $pObj->recv_id,
                'unread_count'  => 1
            ]);

            return 1;
        }
    }
}
