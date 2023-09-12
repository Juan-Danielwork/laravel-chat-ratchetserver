@extends('layouts.cont')

@section('content')
<div id="floating_menu" class="hidden fixed inset-0 z-50 overflow-hidden lg:hidden" id="headlessui-dialog-10" role="dialog" aria-modal="true" data-headlessui-state="open" aria-labelledby="headlessui-dialog-title-12">
    <div class="absolute inset-0 bg-slate-900/25 backdrop-blur-sm transition-opacity opacity-100"></div>
    <div class="fixed inset-0 flex items-start justify-end overflow-y-auto translate-x-0 back-body">
        <div class="min-h-full w-[min(20rem,calc(100vw-theme(spacing.10)))] bg-white shadow-2xl ring-1 ring-black/10 transition" id="headlessui-dialog-panel-11" data-headlessui-state="open">
            <button type="button" id="close_nav" class="p-2 outline-none flex h-10 w-8 items-center justify-center" tabindex="0"><svg class="h-3.5 w-3.5 overflow-visible stroke-slate-900" fill="none" stroke-width="1.5" stroke-linecap="round" xmlns="http://www.w3.org/2000/svg"><path d="M0 0L14 14M14 0L0 14"></path></svg>
            </button>
            <nav class="divide-y divide-slate-900/10 text-base leading-7 text-slate-900">
                <div class="py-3">
                    <div class="-my-2 items-start" id="user_list_nav">
                        @foreach($users as $user)
                            <div data-userid="{{$user->id}}" class="relative flex items-center px-3 py-2 text-sm transition duration-150 ease-in-out border-b border-gray-300 cursor-pointer hover:bg-gray-100 focus:outline-none">
                                @if ($user->unread_count > 0)
                                    <span class="absolute w-6 h-6 text-center text-white text-base bg-red-600 rounded-full right-3 unread-ball">{{$user->unread_count}}</span>
                                @endif                                
                                <img class="object-cover w-10 h-10 rounded-full"
                                    src="{{asset('img/icon.png')}}" alt="username" />
                                    <div class="w-full pb-2">
                                    <div class="flex justify-between">
                                        <span class="block ml-2 font-semibold text-gray-600">{{ $user->name }}</span>
                                    </div>
                                    <span class="block ml-2 text-sm text-gray-600">{{ $user->email }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </nav>
        </div>
    </div>
</div>

<div class="min-w-full h-full border rounded lg:grid lg:grid-cols-3">
    <div class="border-r border-gray-300 lg:col-span-1 overflow-auto user-list-container">

        <ul class="">
            <h2 class="my-2 mb-2 ml-2 text-lg text-gray-800 font-bold">Users</h2>
            <li id="user_list">
                @foreach($users as $user)
                    <div data-userid="{{$user->id}}" class="relative flex items-center px-3 py-2 text-sm transition duration-150 ease-in-out border-b border-gray-300 cursor-pointer hover:bg-gray-100 focus:outline-none">
                        @if ($user->unread_count > 0)
                            <span class="absolute w-6 h-6 text-center text-white text-base bg-red-600 rounded-full right-3 unread-ball">{{$user->unread_count}}</span>
                        @endif
                        <img class="object-cover w-10 h-10 rounded-full"
                            src="{{asset('img/icon.png')}}" alt="username" />

                        <div class="w-full pb-2">
                            <div class="flex justify-between">
                                <span class="block ml-2 font-semibold text-gray-600">{{ $user->name }}</span>
                            </div>
                            <span class="block ml-2 text-sm text-gray-600">{{ $user->email }}</span>
                        </div>
                    </div>
                @endforeach
            </li>
        </ul>
    </div>
        <div class="lg:col-span-2 lg:block border-gray-300 h-full">
            <div class="w-full h-full">
                <div class="relative flex items-center p-3 border-b border-gray-300 h-[82px]" id="recv_name">
                </div>
                <div class="relative w-full p-6 overflow-y-auto" style="height: calc(100vh - 210px)">
                    <ul class="space-y-2" id="chat_output">
                    </ul>
                </div>
                <div dd="{{route('chat.destroy', 3)}}" class="flex items-center justify-between w-full p-3 border-t border-gray-300">
                    <input type="text" id="chat_input" placeholder="Message"
                            class="block w-full py-2 pl-4 mx-3 bg-gray-100 rounded-full outline-none focus:text-gray-700" style="width: calc(100% - 215px);"
                            name="message" required />
                    <button type="button" id="send_msg" class="bg-violet-500 hover:bg-violet-600 focus:outline-none focus:ring focus:ring-violet-300 active:bg-violet-700 px-3 py-2 text-sm leading-5 rounded-full font-semibold text-white">Send</button>
                    <button type="button" class="bg-violet-500 hover:bg-violet-600 focus:outline-none focus:ring focus:ring-violet-300 active:bg-violet-700 px-3 py-2 text-sm leading-5 rounded-full font-semibold text-white" id="delete_chat">Delete Chat</button>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{asset('dist/jquery.js')}}"></script>
    <script type="text/javascript">
        var chatting_user_id;
        $('document').ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#chat_output").animate({scrollTop: $('#chat_output').prop("scrollHeight")}, 1000); // Scroll the chat output div
            
            $("#toggle_float").click(function() {
                $("#floating_menu").removeClass('hidden');
            });
            $("#close_nav").click(function() {
                $("#floating_menu").addClass('hidden');
            });
            $("#floating_menu").click(function(e) {
                if (e.target == $(".back-body").get(0)) {
                    $("#floating_menu").addClass('hidden');
                }
            });
            $("#send_msg").click(function() {
                if (!chatting_user_id) return;
                if (!$("#chat_input").val()) return;
                
                ws.send(
                    JSON.stringify({
                        'type': 'chat',
                        'user_id': '{{auth()->id()}}',
                        'user_name': '{{auth()->user()->name}}',
                        'recv_id': chatting_user_id,
                        'chat_msg': $("#chat_input").val()
                    })
                );
                $("#chat_input").val('');
            });

            $("#delete_chat").click(function() {
                if (!chatting_user_id) return;
                $.ajax({
                    url: 'chat/' + chatting_user_id,
                    method: 'DELETE',
                    success:function(response)
                    {
                        $("#chat_output").empty();
                    },
                    error: function(response) {
                    }
                });
            });

            $("#user_list > div, #user_list_nav > div").click(function() {
                $.get('chat/show', {recv_id: $(this).attr('data-userid')}, function(json_msg) {
                    var msg_html = '';
                    json_msg.forEach(function(msg) {
                        if (msg.user_id == '{{auth()->id()}}') {
                            msg_html += '<li class="flex justify-end"> \
                                    <div class="group/item relative max-w-xl pl-3 pr-1 py-1 text-gray-700 bg-lime-100 rounded shadow text-lg" >\
                                       <span class="block flex items-end"><span class="pr-3">'+msg.message+'</span><span class="group/edit group-hover/item:visible text-xs text-green-600 flex justify-end">'+msg.msg_date+'</span></span>\
                                        \
                                    </div>\
                                </li>';
                        } else {
                            msg_html += '<li class="flex justify-start">\
                                    <div class="group/item relative max-w-xl pl-3 pr-1 py-1 text-gray-700 bg-slate-200 rounded shadow text-lg" >\
                                    <span class="block flex items-end"><span class="pr-3">'+msg.message+'</span><span class="group/edit group-hover/item:visible text-xs text-green-600 flex justify-end">'+msg.msg_date+'</span></span>\
                                    </div>\
                                </li>';
                        }
                    }); 
                    $("#chat_output").html(msg_html);
                });
                chatting_user_id = $(this).attr('data-userid');
                $("#recv_name").html($(this).html()).find("> span").remove();
                $("#floating_menu").addClass('hidden');

                $("#user_list_nav > div").removeClass('bg-slate-200');
                $("#user_list_nav > div[data-userid="+chatting_user_id+"]").addClass('bg-slate-200');
                $("#user_list_nav > div[data-userid="+chatting_user_id+"]").find('.unread-ball').remove();

                $("#user_list > div").removeClass('bg-slate-200');
                $(this).addClass('bg-slate-200');
                $(this).find('.unread-ball').remove();
                $("#chat_input").focus();
            });
        });

        // Websocket
        let ws = new WebSocket("{{env('RATCHET_HOST')}}:{{env('RATCHET_PORT')}}");
        ws.onopen = function (e) {
            // Connect to websocket
            ws.send(
                JSON.stringify({
                    'type': 'socket',
                    'status': 'connect',
                    'user_id': '{{auth()->id()}}'
                })
            );

            // Bind onkeyup event after connection
            $('#chat_input').on('keyup', function (e) {
                if (e.keyCode === 13 && !e.shiftKey) {
                    let chat_msg = $(this).val();
                    if (!chat_msg) return;
                    if (!chatting_user_id) return;
                    ws.send(
                        JSON.stringify({
                            'type': 'chat',
                            'user_id': '{{auth()->id()}}',
                            'user_name': '{{auth()->user()->name}}',
                            'recv_id': chatting_user_id,
                            'chat_msg': chat_msg
                        })
                    );
                    $(this).val('');
                }
            });
        };
        ws.onerror = function (e) {
            // Error handling
            ws.onopen();
        };
        ws.onclose = function(e) {
            ws.send(
                JSON.stringify({
                    'type': 'socket',
                    'status': 'disconnect',
                    'user_id': '{{auth()->id()}}'
                })
            );

            ws.onopen();
        };
        ws.onmessage = function (e) {
            let json = $.parseJSON(e.data);
            switch (json.type) {
                case 'chat':
                    if (json.send_id == '{{auth()->id()}}' || json.send_id == chatting_user_id) {
                        $('#chat_output').append(json.msg); // Append the new message received
                        $("#chat_output").animate({scrollTop: $('#chat_output').prop("scrollHeight")}, 1000); // Scroll the chat output div
                    } else {
                        $.post('chat', {recv_id: json.send_id}, function(unread_count) {
                            $('[data-userid=' + json.send_id + ']').append('<span class="absolute w-6 h-6 text-center  text-base text-white bg-red-600 rounded-full right-3 unread-ball">'+unread_count+'</span>');
                        });
                    }
                    break;

                case 'socket':
                    $('#total_client').html(json.msg);
                    break;

                case 'users':
                    var user_ids = $.parseJSON(json.user_ids);
                    var user_html = '';
                    $("#user_list > div, #nav_user_list > div").append('<span class="absolute w-3 h-3 bg-slate-400 rounded-full left-10 top-3"></span>');
                    user_ids.forEach(function(user_id) {
                        $('[data-userid=' + user_id + ']').append('<span class="absolute w-3 h-3 bg-green-600 rounded-full left-10 top-3"></span>');
                    });
                    break;
            }
        };
    </script>
@endsection
