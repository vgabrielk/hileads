<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WuzapiService;

class WuzapiToolsController extends Controller
{
    private function service(): WuzapiService
    {
        $token = auth()->user()->api_token;
        return new WuzapiService($token);
    }

    public function status()
    {
        $result = $this->service()->getStatus();
        return view('whatsapp.status', ['result' => $result]);
    }

    public function contacts()
    {
        $result = $this->service()->getContacts();
        return view('whatsapp.contacts', ['result' => $result]);
    }

    public function userInfo(Request $request)
    {
        $phones = array_filter(array_map('trim', preg_split('/[\n,;\s]+/', (string) $request->input('phones'))));
        $result = $this->service()->getUserInfo($phones);
        return back()->with('result_info', $result)->withInput();
    }

    public function userCheck(Request $request)
    {
        $phones = array_filter(array_map('trim', preg_split('/[\n,;\s]+/', (string) $request->input('phones'))));
        $result = $this->service()->checkUsers($phones);
        return back()->with('result_check', $result)->withInput();
    }

    public function setPresence(Request $request)
    {
        $type = $request->input('type', 'available');
        $result = $this->service()->setPresence($type);
        return back()->with('result_presence', $result);
    }

    public function getAvatar(Request $request)
    {
        $phone = $request->input('phone');
        $preview = (bool) $request->input('preview', true);
        $result = $this->service()->getAvatar($phone, $preview);
        return back()->with('result_avatar', $result)->withInput();
    }

    public function sendText(Request $request)
    {
        $phone = $request->input('phone');
        $body = $request->input('body');
        $id = $request->input('id');
        
        $result = $this->service()->sendTextMessage($phone, $body, $id);
        return back()->with('result_send', $result)->withInput();
    }

    public function markRead(Request $request)
    {
        $ids = array_filter(array_map('trim', preg_split('/[\n,;\s]+/', (string) $request->input('ids'))));
        $chat = $request->input('chat');
        $sender = $request->input('sender');
        $result = $this->service()->markRead($ids, $chat, $sender);
        return back()->with('result_markread', $result)->withInput();
    }

    public function react(Request $request)
    {
        $phone = $request->input('phone');
        $emoji = $request->input('emoji', '👍');
        $id = $request->input('id');
        $result = $this->service()->react($phone, $emoji, $id);
        return back()->with('result_react', $result)->withInput();
    }

    public function deleteMessage(Request $request)
    {
        $id = $request->input('id');
        $result = $this->service()->deleteMessage($id);
        return back()->with('result_delete', $result)->withInput();
    }
}
