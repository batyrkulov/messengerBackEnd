<?php

namespace App\Http\Controllers;

use App\Message;
use App\User;
use App\Contact;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MessagesApiController extends Controller
{
    function send(Request $request) {
        if ($request->to>0 && !empty($request->body) && User::where('id', $request->to)->count()>0)  {
            $message = new Message();
            $message->from = $this->user->id;
            $message->to = $request->to;
            $message->body = $request->body;
            $message->save();

            $affected =
                Contact::where([['user1', '=', $this->user->id], ['user2', '=', $request->to]])
                ->orWhere([['user2', '=', $this->user->id], ['user1', '=', $request->to]])
                ->update(['last_action_at'=>Carbon::now()]);
            if (!$affected) {
                $contact = new Contact();
                $contact->user1 = $this->user->id;
                $contact->user2 = $request->to;
                $contact->last_action_at = Carbon::now();
                $contact->save();
            }

            return $this->answer(['new_message_id'=>$message->id]);
        } else
            return $this->answer('', 'Input error');
    }

    function getMessages($userId, $page=1, $pageSize=false) {
        if (!$pageSize) $pageSize = env('DEF_PAGE_SIZE');
        $offset = (($page-1)*$pageSize);
        $totalMessages =
            Message::where([['to', '=', $this->user->id], ['from', '=', $userId]])
                ->orWhere([['from', '=', $this->user->id], ['to', '=', $userId]])
                ->count();

        $messages =
            Message::where([['to', '=', $this->user->id], ['from', '=', $userId]])
                ->orWhere([['from', '=', $this->user->id], ['to', '=', $userId]])
                ->orderBy('created_at', 'desc')
                ->skip($offset)
                ->take($pageSize)
                ->get();
        return $this->answer(['totalMessages'=>$totalMessages, 'page'=>$page, 'pageSize'=>$pageSize, 'messages'=>$messages]);
    }
}
