<?php
namespace App\Livewire;

use App\Events\MessageSendEvent;
use Livewire\Component;
use App\Models\User;
use App\Models\Message;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class ChatComponent extends Component
{
    public $user;
    public $sender_id;
    public $receiver_id;
    public $message = '';
    public $messages = [];

    public function render(){
        return view('livewire.chat-component');
    }
    public function mount($user_id){
        $this->sender_id =Auth::id();
        
        $this->receiver_id = $user_id;
       // dd($this->receiver_id);
        $messages = Message::where(function($query){
            $query->where('sender_id',$this->sender_id)
                 ->where('receiver_id',$this->receiver_id);
        })->orWhere(function($query){
            $query->where('sender_id',$this->receiver_id)
            ->where('receiver_id',$this->sender_id);
        })->with('sender:id,name','receiver:id,name')
        ->get();
        foreach($messages as $message){
            $this->appendChatMessage($message);
        }
        //dd($messages);
        $this->user = User::whereId($user_id)->first();
    }

    public function sendMessage(){
        //dd($this->message);
        $chatMessage = new Message();
        $chatMessage->sender_id = $this->sender_id;
        $chatMessage->receiver_id = $this->receiver_id;
        $chatMessage->message = $this->message;

        $chatMessage->save();
        $this->appendChatMessage($chatMessage);
        broadcast(new MessageSendEvent($chatMessage))->toOthers();
        $this->message = '';
    }
    #[On('echo-private:chat-channel.{sender_id},MessageSendEvent')]
    public function listenForMessage($event){
        $chatMessage = Message::whereId($event['message']['id'])->with('sender:id,name','receiver:id,name')->first();
        $this->appendChatMessage($chatMessage);
    }


    public function appendChatMessage($message){
        $this->messages[]=[
            'id'=>$message->id,
            'message'=>$message->message,
            'sender'=>$message->sender->name,
            'receiver'=>$message->receiver->name,
        ];
    }
    
}
?>