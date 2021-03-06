var localVideo;
var firstPerson = false;
var socketCount = 0;
var socketId;
var localStream;
var connections = [];
var timer;

var config = {'host': 'https://socket.yourconference.live'};

var MEETING_ROOM = socket_lounge_video_meet_room+'_'+meeting_id;

var peerConnectionConfig = {
    'iceServers': [
        { 'urls': 'stun:stun.l.google.com:19302' },
        { 'urls': 'stun:stun1.l.google.com:19302' },
        {
            url: 'turn:numb.viagenie.ca',
            credential: 'muazkh',
            username: 'webrtc@live.com'
        },
        {
            url: 'turn:192.158.29.39:3478?transport=udp',
            credential: 'JZEOEt2V3Qb0y27GRntt2u2PAYA=',
            username: '28224511:1379330808'
        }
    ]
};

$(function() {

    $("html").on("contextmenu",function(){
        return false;
    });

    $('#main_menu_top_bar').html('');

    timer = setInterval(meetingTimer, 1000); //Countdown until meeting ends

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

    shareCam();

    // $('.share-screen-btn').on('click', function () {
    //
    // });


    $('.leave-btn').on('click', function () {
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to leave the meeting but you can always come back!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, leave!'
        }).then((result) => {
            if (result.isConfirmed) {
                if(!window.top.close())
                {
                    Swal.fire(
                        'Problem!',
                        "Since you didn't open this meeting tab from our app, we are unable to automatically make you leave but you can simply close this browser tab and you will leave the meeting!",
                        'error'
                    )
                }
            }
        });
    });

});

function shareCam() {

    localVideo = document.getElementById('localVideo');

    var constraints = {
        video: true,
        audio: true,
    };
    if(navigator.mediaDevices.getUserMedia) {
        socket = io.connect(config.host, {secure: true});
        navigator.mediaDevices.getUserMedia(constraints)
            .then(getUserMediaSuccess)
            .then(function(){
                socket.emit('joinRoundTable', MEETING_ROOM, user_name, user_id, 'cam');
                socket.on('signal', gotMessageFromServer);

                socket.on('joinRoundTable', function(){
                    console.log('connected with id:'+socket.id);

                    socketId = socket.id;

                    socket.on('user-left-roundtable', function(id){
                        var video = document.querySelector('[data-socket="'+ id +'"]');
                        if(video == null)
                            return;
                        var parentDiv = video.parentElement;
                        video.parentElement.parentElement.removeChild(parentDiv);
                    });


                    socket.on('user-joined-roundtable', function(id, count, clients, table, attendees_list){
                        if (table != MEETING_ROOM)
                            return;
                        clients.forEach(function(socketListId) {
                            if(!connections[socketListId]){
                                connections[socketListId] = new RTCPeerConnection(peerConnectionConfig);
                                //Wait for their ice candidate
                                connections[socketListId].onicecandidate = function(event){
                                    if(event.candidate != null) {
                                        console.log('SENDING ICE');
                                        socket.emit('signal', socketListId, JSON.stringify({'ice': event.candidate}));
                                    }
                                }

                                //Wait for their video stream
                                connections[socketListId].onaddstream = function(event){
                                    gotRemoteStream(event, socketListId, attendees_list[socketListId])
                                }

                                //Add the local video stream
                                connections[socketListId].addStream(localStream);
                            }
                        });

                        //Create an offer to connect with your local description

                        if(count >= 2){
                            connections[id].createOffer().then(function(description){
                                connections[id].setLocalDescription(description).then(function() {
                                    // console.log(connections);
                                    socket.emit('signal', id, JSON.stringify({'sdp': connections[id].localDescription}));
                                }).catch(e => console.log(e));
                            });
                        }
                    });
                })

            });


        // Muting functionality
        $('.mute-mic-btn').on('click', function () {

            if ($('#muteStatus').val() == 'unmuted')
            {
                $('#muteStatus').val('muted');
                socket.emit('mute-me', MEETING_ROOM);
                $('.mute-mic-btn').html('<i class="fa fa-microphone-slash fa-3x mute-mic-btn-icon" aria-hidden="true" style="color:#ff422b;"></i>');
                $('.my-muteIndicator-icon').html('<i class="fa fa-microphone-slash fa-2x" aria-hidden="true" style="color: #ff422b"></i>');
                $('.localvideo-div > .name-tag').text('You(Muted)');
            }else{
                $('#muteStatus').val('unmuted');
                socket.emit('unmute-me', MEETING_ROOM);
                $('.mute-mic-btn').html('<i class="fa fa-microphone fa-3x mute-mic-btn-icon" aria-hidden="true" style="color:#12b81c;"></i>');
                $('.my-muteIndicator-icon').html('<i class="fa fa-microphone fa-2x" aria-hidden="true" style="color: #12b81c"></i>');
                $('.localvideo-div > .name-tag').text('You');
            }
        });
        socket.on('mute-me', function(user_socket){
            $('video[data-socket="'+user_socket+'"]').prop('muted', true);
            $('.muteIndicator-icon[data-socket="'+user_socket+'"]').css('display', '');
        });

        socket.on('unmute-me', function(user_socket){
            $('video[data-socket="'+user_socket+'"]').prop('muted', false);
            $('.muteIndicator-icon[data-socket="'+user_socket+'"]').css('display', 'none');
        });
        // End of muting functionality

        // Turn off/on cam functionality
        $('.cam-btn').on('click', function () {
            if ($('#camStatus').val() == 'on')
            {
                $('#camStatus').val('off');
                socket.emit('cam-off', MEETING_ROOM);
                $('.cam-btn').html('<i class="fa fa-video-camera fa-3x cam-btn-icon" aria-hidden="true" style="color:#ff422b;"></i>');

                $('.localvideo-div > .videoCover').css('display', '');
            }else{
                $('#camStatus').val('on');
                socket.emit('cam-on', MEETING_ROOM);
                $('.cam-btn').html('<i class="fa fa-video-camera fa-3x cam-btn-icon" aria-hidden="true" style="color:#12b81c;"></i>');
                $('.localvideo-div > .videoCover').css('display', 'none');
            }
        });
        socket.on('cam-off', function(user_socket){
            $('.videoCover[data-socket="'+user_socket+'"]').css('display', '');
        });

        socket.on('cam-on', function(user_socket){
            $('.videoCover[data-socket="'+user_socket+'"]').css('display', 'none');
        });
        // End of turn off/on cam functionality

    } else {
        alert('Your browser does not support getUserMedia API');
    }
}

function getUserMediaSuccess(stream) {

    localStream = stream;

    navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia || navigator.mozGetUserMedia;
    navigator.getUserMedia({
        'audio': false,
        'video': true
    }, function (stream) {
        localVideo.srcObject = stream;
        setTimeout(function () {
            $('.control-icons-col').css('display', '');
        }, 5000);
    }, logError);

    $('.localvideo-div').prepend('<span class="my-muteIndicator-icon" ><i class="fa fa-microphone-slash fa-2x" aria-hidden="true" style="color: #12b81c"></i></span>');

}

function gotRemoteStream(event, id, attendee) {

    if(id == socketId)
        return;

    var videos = document.querySelectorAll('camera-feeds'),
        video  = document.createElement('video'),
        div    = document.createElement('div'),
        nameTag = document.createElement('span'),
        fullscreenBtn = document.createElement('span'),
        muteIndicator = document.createElement('span'),
        videoCover = document.createElement('div');

    div.setAttribute('class', 'col-md-3');

    nameTag.setAttribute('class', 'name-tag');
    if (attendee.sharingType == 'screen'){
        nameTag.innerHTML = attendee.name+' (Screen)';
    }else{
        nameTag.innerHTML = attendee.name;
    }

    videoCover.setAttribute('class', 'videoCover');
    videoCover.setAttribute('data-socket', id);
    videoCover.style.display = (attendee.camStatus == 'off')?'':'none';

    fullscreenBtn.setAttribute('class', 'fullscreen-btn');
    fullscreenBtn.innerHTML = '<i class="fa fa-arrows" aria-hidden="true" style="border: 1px solid;"></i>';

    muteIndicator.setAttribute('class', 'muteIndicator-icon');
    muteIndicator.setAttribute('data-socket', id);
    muteIndicator.style.display = (attendee.muteStatus == 'muted')?'':'none';
    muteIndicator.innerHTML = '<i class="fa fa-microphone-slash fa-2x" aria-hidden="true" style="color: red"></i>';

    video.setAttribute('data-socket', id);
    video.setAttribute('width', '100%');
    video.srcObject   = event.stream;
    video.autoplay    = true;
    if (attendee.muteStatus == 'muted')
        video.muted       = true;
    video.playsinline = true;

    div.appendChild(videoCover);
    div.appendChild(nameTag);
    div.appendChild(fullscreenBtn);
    div.appendChild(muteIndicator);
    div.appendChild(video);
    document.querySelector('.camera-feeds').prepend(div);
}

function gotMessageFromServer(fromId, message) {

    //Parse the incoming signal
    var signal = JSON.parse(message)

    //Make sure it's not coming from yourself
    if(fromId != socketId) {

        if(signal.sdp){
            connections[fromId].setRemoteDescription(new RTCSessionDescription(signal.sdp)).then(function() {
                if(signal.sdp.type == 'offer') {
                    connections[fromId].createAnswer().then(function(description){
                        connections[fromId].setLocalDescription(description).then(function() {
                            socket.emit('signal', fromId, JSON.stringify({'sdp': connections[fromId].localDescription}));
                        }).catch(e => console.log(e));
                    }).catch(e => console.log(e));
                }
            }).catch(e => console.log(e));
        }

        if(signal.ice) {
            connections[fromId].addIceCandidate(new RTCIceCandidate(signal.ice)).catch(e => console.log(e));
        }
    }
}

function logError(error) {
    displaySignalMessage(error.name + ': ' + error.message);
}

$('.camera-feeds').on('click', 'span.fullscreen-btn', function () {

    // if already full screen; exit
    // else go fullscreen
    if (
        document.fullscreenElement ||
        document.webkitFullscreenElement ||
        document.mozFullScreenElement ||
        document.msFullscreenElement
    ) {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }

        $(this).parent().children('.fullscreen-btn-fullscreen').eq(0).html('<i class="fa fa-arrows" aria-hidden="true" style="border: 1px solid;"></i>');
        $(this).parent().children('.fullscreen-btn-fullscreen').eq(0).removeClass('nametag-fullscreen');

    } else {
        element = $(this).parent().get(0);
        if (element.requestFullscreen) {
            element.requestFullscreen();
        } else if (element.mozRequestFullScreen) {
            element.mozRequestFullScreen();
        } else if (element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
        } else if (element.msRequestFullscreen) {
            element.msRequestFullscreen();
        }

        $(this).parent().children('.name-tag').eq(0).addClass('nametag-fullscreen');

        $(this).parent().children('.fullscreen-btn').eq(0).addClass('fullscreen-btn-fullscreen');
        $(this).parent().children('.fullscreen-btn').eq(0).html('<i class="fa fa-compress fa-2x" aria-hidden="true" style="border: 1px solid;"></i>');

    }
});

$(document).bind('fullscreenchange webkitfullscreenchange mozfullscreenchange msfullscreenchange', function (e) {
    var fullscreenElement = document.fullscreenElement || document.webkitFullscreenElement || document.mozFullscreenElement || document.msFullscreenElement;

    if (!fullscreenElement) {
        //console.log('Leaving full-screen mode...');
        $('.nametag-fullscreen').removeClass('nametag-fullscreen');

        $('.fullscreen-btn-fullscreen').html('<i class="fa fa-arrows" aria-hidden="true" style="border: 1px solid;"></i>');
        $('.fullscreen-btn-fullscreen').removeClass('fullscreen-btn-fullscreen');
    } else {
        //console.log('Entering full-screen mode...');
    }
});

// Meeting timer to force users out once allowed time is over
var _second = 1000;
var _minute = _second * 60;
var _hour = _minute * 60;
var _day = _hour * 24;
var end = new Date(meeting_to);
function meetingTimer() {
    var now = new Date();
    var now_ct = new Date(now.toLocaleString('en-US', { timeZone: 'America/Chicago' }));

    var distance = end - now_ct;
    if (distance < 0) {
        clearInterval(timer);
        location.reload();
        return;
    }
    var days = Math.floor(distance / _day);
    var hours = Math.floor((distance % _day) / _hour);
    var minutes = Math.floor((distance % _hour) / _minute);
    var seconds = Math.floor((distance % _minute) / _second);
}
