var socketServer = "https://socket.yourconference.live:443";
// var socketServer = "https://127.0.0.1:3080";
//let socket = io(socketServer);







toastr.options = {
    "closeButton": false,
    "debug": false,
    "newestOnTop": false,
    "progressBar": false,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

$(function() {

    socket.on('serverStatus', function(data) {
        console.log(data);
    });

    socket.emit('joinLoungeGroupChat', {"room":socket_lounge_room, "name":user_name});
    socket.on('newLoungeGroupText', function(data) {
        if (data.user_id == user_id)
        {
            $('.group-chat').append(
                '<li class="grp-chat right clearfix">\n' +
                '   <span class="chat-img pull-right">\n' +
                '     <img src="'+user_logo_url+'" alt="User DP" class="img-circle" />\n' +
                '   </span>\n' +
                '   <div class="chat-body clearfix">\n' +
                '     <div class="header">\n' +
                '       <small class="pull-left text-muted"><span class="glyphicon glyphicon-time"></span>'+data.datetime+'</small>\n' +
                '       <strong class="pull-right primary-font">'+user_name+'</strong>\n' +
                '     </div>\n' +
                '     <br><p class="pull-right">\n' +
                '      '+data.chat_text+'\n' +
                '     </p>\n' +
                '   </div>\n' +
                '</li>'
            );
            $('#grp-chat-body').scrollTop($('#grp-chat-body')[0].scrollHeight);
        }else{

            $('.group-chat').append(
                '<li class="grp-chat left clearfix">\n' +
                '   <span class="chat-img pull-left">\n' +
                '     <img src="'+data.profile+'" alt="User Avatar" class="img-circle">\n' +
                '   </span>\n' +
                '   <div class="chat-body clearfix">\n' +
                '      <div class="header">\n' +
                '         <strong class="primary-font pull-left">'+data.name+'</strong> <small class="pull-right text-muted">\n' +
                '         <span class="glyphicon glyphicon-time pull-right"></span>'+data.datetime+'</small>\n' +
                '      </div>\n' +
                '      <br><p class="pull-left">\n' +
                '       '+data.chat_text+'\n' +
                '      </p>\n' +
                '    </div>\n' +
                '</li>'
            );
            $('#grp-chat-body').scrollTop($('#grp-chat-body')[0].scrollHeight);
        }
    });

    socket.on('newLoungeGroupJoin', function(data) {
    });

    $.get( base_url+"LoungeGroupChat/getAllChats", function(chatJson) {
        console.log("geldi");
        var chats = JSON.parse(chatJson);

        if (chats == 0)
            $('.group-chat').append('<p>No messages!</p>');

        $.each( chats, function( number, chat ) {
            if (chat.chat_from == user_id)
            {
                $('.group-chat').append(
                    '<li class="grp-chat right clearfix">\n' +
                    '   <span class="chat-img pull-right">\n' +
                    '     <img src="'+user_logo_url+'" alt="Sponsor Logo" class="img-circle" />\n' +
                    '   </span>\n' +
                    '   <div class="chat-body clearfix">\n' +
                    '     <div class="header">\n' +
                    '       <small class="pull-left text-muted"><span class="glyphicon glyphicon-time"></span>'+chat.datetime+'</small>\n' +
                    '       <strong class="pull-right primary-font">'+user_name+'</strong>\n' +
                    '     </div>\n' +
                    '     <br><p class="pull-right">\n' +
                    '      '+chat.chat_text+'\n' +
                    '     </p>\n' +
                    '   </div>\n' +
                    '</li>'
                );
            }else{

                if (chat.from_name == null)
                    chat.from_name = 'Name Unavailable';
                var nameAcronym = chat.from_name.match(/\b(\w)/g).join('');
                var color = md5(nameAcronym+chat.chat_from).slice(0, 6);

                var userAvatarSrc = (chat.profile != '' && chat.profile != null)?'uploads/customer_profile/'+chat.profile:'https://placehold.it/50/'+color+'/fff&amp;text='+nameAcronym;
                var userAvatarAlt = 'https://placehold.it/50/'+color+'/fff&amp;text='+nameAcronym;

                $('.group-chat').append(
                    '<li class="grp-chat left clearfix">\n' +
                    '   <span class="chat-img pull-left">\n' +
                    '     <img src="'+userAvatarSrc+'" alt="User Avatar" onerror=this.src="'+userAvatarAlt+'" class="img-circle" />\n' +
                    '   </span>\n' +
                    '   <div class="chat-body clearfix">\n' +
                    '      <div class="header">\n' +
                    '         <strong class="primary-font pull-left">'+chat.from_name+'</strong> <small class="pull-right text-muted">\n' +
                    '         <span class="glyphicon glyphicon-time"></span>'+chat.datetime+'</small>\n' +
                    '      </div>\n' +
                    '      <br><p class="pull-left">\n' +
                    '       '+chat.chat_text+'\n' +
                    '      </p>\n' +
                    '    </div>\n' +
                    '</li>'
                );
            }
        });

        $('#grp-chat-body').scrollTop($('#grp-chat-body')[0].scrollHeight);


    });


    $('#groupChatText').keypress(function(e){
        if(e.which == 13){//Enter key pressed
            $('.send-grp-chat-btn').click();//Trigger search button click event
        }else{
            socket.emit('isTyping', {"room":socket_lounge_room, "someone":user_name});
        }
    });

    socket.on('isTyping', function(someone) {
        $('.is-typing').html(someone+' is typing...');
        setTimeout(
            function() {
                $('.is-typing').html('');
            }, 1000);
    });

    $(".send-grp-chat-btn").on( "click", function() {
        var text = $('#groupChatText').val();

        if (text == '')
            return;

        $.post(base_url+"LoungeGroupChat/newText",
            {
                'chat_text': text
            },
            function(data, status){
                if(status == 'success')
                {
                    var dataFromDb = JSON.parse(data);

                    $('#groupChatText').val('');

                    socket.emit('newLoungeGroupText',
                        {
                            "room":socket_lounge_room,
                            "name":user_name,
                            "chat_text":text,
                            "user_id":user_id,
                            "datetime":dataFromDb.datetime,
                            "profile":user_logo_url
                        });

                }else{
                    toastr["error"]("Network problem!");
                }
            });
    });

    socket.on('clearChat', function() {
        $('.group-chat').html('');
    });

    $(".clear-group-chat-btn").on( "click", function() {

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, clear chat!'
        }).then((result) => {
            if (result.value) {

                $.post("sponsor-admin/GroupChat/clearChat",
                    {

                    },
                    function(data, status){
                        if(status == 'success')
                        {
                            socket.emit('clearChat', GROUP_CHAT_ROOM);

                            Swal.fire(
                                'Cleared!',
                                'Group chat has been cleared.',
                                'success'
                            )

                        }else{
                            toastr["error"]("Problem clearing chat!")
                        }
                    });
            }
        })
    });

    $(".save-group-chat-btn").on( "click", function() {
        toastr["warning"]("Under development!")
    });

    var chatIsLoad=false;
    $.get( base_url+"LoungeOtoChat/checkForUnreadChat/"+user_id, function(unreadMsgsFrom) {
        unreadMsgsFrom = JSON.parse(unreadMsgsFrom);
        $.get( base_url+"user/UserDetails/getAllUsers", function(allUsers) {
            var users = JSON.parse(allUsers);

            socket.emit('joinLoungeOtoChat', {"room":socket_lounge_oto_chat_group, "name":user_name, "userId":user_id, "userType":user_type});

            $.each( users, function( number, user ) {

                var fullname = user.first_name+' '+user.last_name;
                if (fullname == ' ')
                {
                    var fullname = 'Name Unavailable';
                }

                var nameAcronym = fullname.match(/\b(\w)/g).join('');
                var color = md5(nameAcronym+user.cust_id).slice(0, 6);

                var userAvatarSrc = (user.profile != '' && user.profile != null)?'uploads/customer_profile/'+user.profile:'https://placehold.it/50/'+color+'/fff&amp;text='+nameAcronym;
                var userAvatarAlt = 'https://placehold.it/50/'+color+'/fff&amp;text='+nameAcronym;

                var company_name_html = '';
                if (user.company_name && (user.company_name != null || user.company_name != '')){
                    company_name_html = '<small class="companyNameBadge">'+user.company_name+'</small>';
                    var company_name = user.company_name;
                }else{
                    var company_name = '';
                }

                if(unreadMsgsFrom != 0 && unreadMsgsFrom.includes(user.cust_id))
                {
                    var newTextBadge = 1;
                    var badgeIcon = '';
                }else{
                    var newTextBadge = 0;
                    var badgeIcon = 'display: none;';
                }


                $('.attendees-chat-list').append(
                    '<li class="attendees-chat-list-item list-group-item text-left" userName="'+fullname+'" userId="'+user.cust_id+'" company_name="'+company_name+'" status="offline" new-text="'+newTextBadge+'">\n' +
                    '<img src="'+userAvatarSrc+'" alt="User Avatar" onerror=this.src="'+userAvatarAlt+'" class="img-circle"> \n' +
                    '<span class="oto-chat-user-list-name" style="font-weight: bold;"> '+fullname+' <span class="badge new-text" style="background-color: #ff0a0a; '+badgeIcon+'">new</span> </span> \n' +

                    '<i class="active-icon fa fa-circle" style="color: #454543;" aria-hidden="true" userId="'+user.cust_id+'"></i>'+company_name_html+'</li>'

                );
                console.log("eklendi");
            });
            chatIsLoad=true;

            $('.attendees-chat-list-item').on("click", function () {

                $(this).children('.oto-chat-user-list-name').children('.new-text').hide();
                $(this).attr('new-text', '0');
                $(".attendees-chat-list li").sort(active_change_dec_sort).appendTo('.attendees-chat-list');
                $(".attendees-chat-list li").sort(newtext_dec_sort).appendTo('.attendees-chat-list');

                $(".attendees-chat-list>li.selected").removeClass("selected");
                $(this).addClass('selected');

                var fullname = $(this).attr('userName');
                var company_name = $(this).attr('company_name');
                var otherUserId = $(this).attr('userId');
                var nameAcronym = fullname.match(/\b(\w)/g).join('');
                var color = md5(nameAcronym+otherUserId).slice(0, 6);
                var activeStatus = $(this).attr('status');
                var statusColour = (activeStatus == 'active')?'#26ff49':(activeStatus == 'inactive')?'#ff9a41':'#454543';

                var company_name_html = '';
                if (company_name && (company_name != null || company_name != ''))
                    company_name_html = '<small class="companyNameBadgeMain">'+company_name+'</small>';

                var userAvatar = $(this).children('img').attr('src');
                var userAvatarAlt = 'https://placehold.it/50/'+color+'/fff&amp;text='+nameAcronym;

                $('.lounge-video-call-btn').attr('user-id', otherUserId);
                $('.lounge-video-call-btn').attr('user-name', fullname);

                $('.send-oto-chat-btn').attr('send-to', otherUserId);
                $('.one-to-one-chat-heading > .attendee-profile-btn').attr('userId', otherUserId);

                $('.selected-user-name-area').html(
                    '<img src="'+userAvatar+'" alt="User Avatar" onerror=this.src="'+userAvatarAlt+'" class="img-circle"> '+
                    fullname +
                    ' <i class="active-icon fa fa-circle" style="color: '+statusColour+';" aria-hidden="true" userId="'+otherUserId+'"></i>' +
                    company_name_html
                );

                $('.selected-user-name-area').attr('userId', otherUserId);
                $('.selected-user-name-area').attr('room', socket_lounge_oto_chat_group);

                $('.oto-messages').html('');

                $.post(base_url+"LoungeOtoChat/getChatsUserToUser/"+otherUserId,
                    {
                    },
                    function(data, status){
                        if(status == 'success')
                        {
                            if (data == 'false'){
                                $('.oto-messages').append('No chat!');
                                return false;
                            }
                            var dataFromDb = JSON.parse(data);

                            if (user_logo == '')
                            {
                                var nameAcronym = user_name.match(/\b(\w)/g).join('');
                                var color = md5(nameAcronym+user_id).slice(0, 6);

                                user_logo_url = 'https://placehold.it/50/'+color+'/fff&amp;text='+nameAcronym;
                            }else{
                                user_logo_url = base_url+'uploads/customer_profile/'+user_logo;
                            }

                            $.each( dataFromDb, function( number, text ) {
                                if (text.from_id == user_id)
                                {
                                    var nameAcronym = text.from_name.match(/\b(\w)/g).join('');
                                    var color = md5(nameAcronym+text.user_id).slice(0, 6);

                                    $('.oto-messages').append(
                                        '<li class="grp-chat text-right clearfix">\n' +
                                        '   <span class="chat-img pull-right">\n' +
                                        '     <img src="'+user_logo_url+'" alt="Sponsor Logo" class="img-circle" />\n' +
                                        '   </span>\n' +
                                        '   <div class="chat-body clearfix">\n' +
                                        '     <div class="header">\n' +
                                        '       <small class="pull-left text-muted"><span class="glyphicon glyphicon-time"></span>'+text.datetime+'</small>\n' +
                                        '       <strong class="pull-right primary-font">'+text.from_name+'</strong>\n' +
                                        '     </div>\n' +
                                        '     <br><p class="pull-right">\n' +
                                        '      '+text.text+' \n' +
                                        '     </p>\n' +
                                        '   </div>\n' +
                                        '</li>'
                                    );
                                    $('.oto-chat-body').scrollTop($('.oto-chat-body')[0].scrollHeight);
                                }else{
                                    var nameAcronym = text.from_name.match(/\b(\w)/g).join('');
                                    var color = md5(nameAcronym+text.from_id).slice(0, 6);

                                    $('.oto-messages').append(
                                        '<li class="grp-chat text-left clearfix">\n' +
                                        '   <span class="chat-img pull-left">\n' +
                                        '     <img src="'+userAvatar+'" alt="User Avatar" onerror=this.src="'+userAvatarAlt+'" class="img-circle">\n' +
                                        '   </span>\n' +
                                        '   <div class="chat-body clearfix">\n' +
                                        '      <div class="header">\n' +
                                        '         <strong class="primary-font">'+text.from_name+'</strong> <small class="pull-right text-muted">\n' +
                                        '         <span class="glyphicon glyphicon-time"></span>'+text.datetime+'</small>\n' +
                                        '      </div>\n' +
                                        '      <p>\n' +
                                        '       '+text.text+' \n' +
                                        '      </p>\n' +
                                        '    </div>\n' +
                                        '</li>'
                                    );
                                    $('.oto-chat-body').scrollTop($('.oto-chat-body')[0].scrollHeight);
                                }
                            });

                            $.get( base_url+"LoungeOtoChat/readAllTextsOf/"+otherUserId, function() {
                                fillUnreadMessages();
                            });

                        }else{
                            toastr["error"]("Network problem!");
                        }
                    });
            });

            $(".attendees-chat-list li:first-child").click();
        });

    });

   var isLoadInt= setInterval(function () {
        if(chatIsLoad){
            clearInterval(isLoadInt);
            var $unreadMsgCount=$(".unread-msg-count");
            var unreadMessage=localStorage.getItem("unreadMessage");
            if(unreadMessage){
                unreadMessage=JSON.parse(unreadMessage);

                if(unreadMessage["clicked"]==true){
                    $unreadMsgCount.html("0");

                    var $chatUsersList=$(".chat-users-list");
                    if($chatUsersList.length>0){
                        $(".attendees-chat-list-item").each(function () {
                            var userid=$(this).attr("userid");
                            if(userid==unreadMessage.from_id){
                                $(this).click();
                            }
                        })
                    }
                }
                localStorage.removeItem("unreadMessage");

            }
        }

    },200)


    $('#one-to-one-ChatText').keypress(function(e){
        if(e.which == 13){//Enter key pressed
            $('.send-oto-chat-btn').click();//Trigger search button click event
        }else{
            var room = $('.selected-user-name-area').attr('room')
            var selectedUser = $('.selected-user-name-area').attr('userId');
            socket.emit('otoTyping', {"room":room, "someone":user_name, "typingTo":selectedUser});
        }
    });

    $(".send-oto-chat-btn").on( "click", function() {
        var text = $('#one-to-one-ChatText').val();
        var chat_to = $('.send-oto-chat-btn').attr('send-to');

        if (text == '')
            return;

        $.post(base_url+"LoungeOtoChat/newText",
            {
                'chat_text': text,
                'chat_from': user_id,
                'chat_to': chat_to
            },
            function(data, status){
                if(status == 'success')
                {
                    var dataFromDb = JSON.parse(data);

                    $('#one-to-one-ChatText').val('');

                    socket.emit('newLoungeOtoText',
                        {
                            "room":socket_lounge_oto_chat_group,
                            "name":user_name,
                            "from_id": user_id,
                            "chat_text":text,
                            "chat_to":chat_to,
                            "datetime":dataFromDb.datetime,
                            "profile": user_logo_url
                        });
                    console.log(user_logo_url);

                }else{
                    toastr["error"]("Network problem!");
                }
            });
    });

    //get a message
    socket.on('newLoungeOtoText', function(data) {

        var selectedUser = $('.selected-user-name-area').attr('userid');

        if ((user_id == data.to_id) && selectedUser != data.from_id)
        {
            $('.attendees-chat-list > li[userid="' + data.from_id + '"] > .oto-chat-user-list-name > .new-text').show();
            $('.attendees-chat-list > li[userid="' + data.from_id + '"]').attr('new-text', '1');

            $(".attendees-chat-list li").sort(newtext_dec_sort).appendTo('.attendees-chat-list');
            return;
        }

        if ((selectedUser == data.to_id && user_id == data.from_id) || (selectedUser == data.from_id && user_id == data.to_id))
        {
            if (data.from_id == user_id) {

                $('.oto-messages').append(
                    '<li class="grp-chat text-right clearfix">\n' +
                    '   <span class="chat-img pull-right">\n' +
                    '     <img src="' + user_logo_url + '" alt="User DP" class="img-circle" />\n' +
                    '   </span>\n' +
                    '   <div class="chat-body clearfix">\n' +
                    '     <div class="header">\n' +
                    '       <small class="pull-left text-muted"><span class="glyphicon glyphicon-time"></span>' + data.datetime + '</small>\n' +
                    '       <strong class="pull-right primary-font">' + data.name + '</strong>\n' +
                    '     </div>\n' +
                    '     <br><p class="pull-right">\n' +
                    '      ' + data.chat_text + ' \n' +
                    '     </p>\n' +
                    '   </div>\n' +
                    '</li>'
                );
                $('.oto-chat-body').scrollTop($('.oto-chat-body')[0].scrollHeight);
            } else {

                $('.oto-messages').append(
                    '<li class="grp-chat text-left clearfix">\n' +
                    '   <span class="chat-img pull-left">\n' +
                    '     <img src="' + data.profile + '" alt="User Avatar" class="img-circle" />\n' +
                    '   </span>\n' +
                    '   <div class="chat-body clearfix">\n' +
                    '      <div class="header">\n' +
                    '         <strong class="primary-font">' + data.name + '</strong> <small class="pull-right text-muted">\n' +
                    '         <span class="glyphicon glyphicon-time"></span>' + data.datetime + '</small>\n' +
                    '      </div>\n' +
                    '      <p>\n' +
                    '       ' + data.chat_text + ' \n' +
                    '      </p>\n' +
                    '    </div>\n' +
                    '</li>'
                );
                $('.oto-chat-body').scrollTop($('.oto-chat-body')[0].scrollHeight);
            }
        }
    });

    socket.on('otoTyping', function(data) {
        var selectedUser = $('.selected-user-name-area').attr('userId');
        if (selectedUser == data.from)
        {
            $('.oto-typing').html(data.someone+' is typing...');
            setTimeout(
                function() {
                    $('.oto-typing').html('');
                }, 1000);
        }
    });



    /*********************** user active status **************************/

    setTimeout(function () {
        socket.emit('getActiveUserListPerApp', socket_app_name);
    }, 4000); // Wait 4 seconds and request for new list of active users

    socket.on('activeUserListPerApp', function(data) {
        if (data == null)
            return;
        $.each(data, function( number, userId ) {
            $('.active-icon[userId="'+userId+'"]').css('color', '#26ff49');
            $('.attendees-chat-list-item[userId="'+userId+'"]').attr('status', 'active');
        });

        $(".attendees-chat-list li").sort(active_change_dec_sort).appendTo('.attendees-chat-list');
        $(".attendees-chat-list li").sort(newtext_dec_sort).appendTo('.attendees-chat-list');
    });

    socket.on('userActiveChangeInApp', function(data) {
        socket.emit('getActiveUserListPerApp', socket_app_name);
        if (data.app == socket_app_name)
        {
            if (data.status == true)
            {
                var color = '#26ff49';
                var status = 'active';
            }else{
                var color = '#ffc500';
                var status = 'inactive';
            }

            $('.active-icon[userId="'+data.userId+'"]').css('color', color);
            $('.attendees-chat-list-item[userId="'+data.userId+'"]').attr('status', status);

            $(".attendees-chat-list li").sort(active_change_dec_sort).appendTo('.attendees-chat-list');
            $(".attendees-chat-list li").sort(newtext_dec_sort).appendTo('.attendees-chat-list');
        }

    });

    /******************* end of user active status *****************************/



    $(".oto-attendee-search").keyup(function () {
        var filter = $(this).val();
        $(".attendees-chat-list li").each(function () {
            if ($(this).text().search(new RegExp(filter, "i")) < 0) {
                $(this).hide();
            } else {
                $(this).show()
            }
        });
    });


    $(".attendee-profile-btn").on( "click", function() {
        var userId = $(this).attr('userId');
        userProfileModal(userId);
    });


    function newtext_asc_sort(a, b) {
        return ($(b).attr('new-text')) < ($(a).attr('new-text')) ? 1 : -1;
    }
    function newtext_dec_sort(a, b) {
        return ($(b).attr('new-text')) > ($(a).attr('new-text')) ? 1 : -1;
    }

    function active_change_asc_sort(a, b){
        return ($(b).attr('status')) < ($(a).attr('status')) ? 1 : -1;
    }
    function active_change_dec_sort(a, b){
        if ($.browser.mozilla)
            return active_change_asc_sort(a, b);
        return ($(b).attr('status')) > ($(a).attr('status')) ? 1 : -1;
    }
});
