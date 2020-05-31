<?php

namespace App\Http\Controllers;

use App\Contact;
use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;

class ContactsApiController extends Controller
{
    function getContacts($page=1, $pageSize=false) {
        if (!$pageSize) $pageSize = env('DEF_PAGE_SIZE');
        $offset = (($page-1)*$pageSize);
        $totalContacts =
            Contact::where('user1', $this->user->id)
            ->orWhere('user2', $this->user->id)
            ->count();
        $contacts =
            Contact::where('user1', $this->user->id)
            ->orWhere('user2', $this->user->id)
            ->orderBy('last_action_at', 'desc')
            ->skip($offset)
            ->take($pageSize)
            ->get();
        $ids = $contacts->map(function ($contact) {
            return (($contact->user1===$this->user->id) ? $contact->user2 : $contact->user1);
        });
        $users = UserResource::collection(User::whereIn('id', $ids->all())
            ->get());
        $sortedUsers = collect();
        foreach ($ids as $id) {
            foreach ($users as $user){
                if ($id === $user->id) {
                    $sortedUsers->push($user);
                    break;
                }
            }
        }

        return $this->answer(['totalContacts'=>$totalContacts, 'page'=>$page, 'pageSize'=>$pageSize, 'users'=>$sortedUsers]);
    }
}
