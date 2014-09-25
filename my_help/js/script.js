$(document).ready(function(){
    var url = getDomen(),
        stop = false;
       
    $('#startObserve').on('click', function(){
        var mainDiv     = $('.mainDiv'),
            user_id     = parseInt($(mainDiv).find('#user_id').val()),
            last_msg_id = parseInt($(mainDiv).find('#last_msg_id').val()),
            room_id     = parseInt($(mainDiv).find('#room_id').val()),
            requestSart = $(mainDiv).find('#requestStart').find('.text'),
            requestRes  = $(mainDiv).find('#requestResult').find('.text'),
            messageSart = $(mainDiv).find('#messagesStart').find('.text'),
            messageRes  = $(mainDiv).find('#messagesResult').find('.text'),
            observe_request,
            observe_message,
            timeOutRequest      = 1000 * 10,
            timeOutMessgae      = 1000 * 10;
            
        stop = false;    

        observe_request = function (){
            var date = new Date(),
                time = date.toLocaleString();
        
            $(requestSart).prepend('<span style="color:green">Start</span>: time - '+time+'\n');
            
            $.ajax({
                type        : 'POST',
                url         : url + '/my_tests/memcache/observe_requests.php',
                data        : {'id' : user_id},
                timeout     : timeOutRequest,
                cache       : false,
                dataType    : 'json',
                success     : function (data) {
                    if (data) {
                        var result = JSON.stringify(data);
                        $(requestRes).prepend(result+'\n');
                    }  
                    if (!stop) {
                        setTimeout(observe_request ,1000);
                    }
                    else {
                        return false;
                    }
                },
                error : function(){
                    if (!stop) {
                        setTimeout(observe_request ,1000);
                    }
                    else {
                        return false;
                    }
                }
            });
        };    
        
        observe_message = function (){
            var date = new Date();
            $(messageSart).prepend('<span style="color:green">Start</span>: time - '+date.toLocaleString()+'\n');
            $.ajax({
                type        : 'POST',
                url         : url + '/my_tests/memcache/observe_messages.php',
                data        : {
                    'id'            :user_id,
                    'last_msg_id'   :last_msg_id,
                    'room_id'       :room_id,
                },
                timeout     : timeOutMessgae,
                cache       : false,
                dataType    : 'json',
                success     : function (data) {
                    if (data) {
                        var result = JSON.stringify(data);
                        $(messageRes).prepend(result+'\n');
                    }  
                    if (!stop) {
                        setTimeout(observe_message ,1000);
                    }
                    else {
                        return false;
                    }
                },
                error : function(){
                    if (!stop) {
                        setTimeout(observe_message ,1000);
                    }
                    else {
                        return false;
                    }
                }
            });
        };

//        observe_request();
        observe_message();
    });
    
    $('#stopObserve').on('click', function(){
        var mainDiv     = $('.mainDiv'),
            requestSart = $(mainDiv).find('#requestStart').find('.text'),
            messageSart = $(mainDiv).find('#messagesStart').find('.text');

        var date = new Date();
        $(messageSart).prepend('<span style="color:red">Stop</span>: time - '+date.toLocaleString()+'\n');
        $(requestSart).prepend('<span style="color:red">Stop</span>: time - '+date.toLocaleString()+'\n');
        stop = true;
    });
});

function getDomen(){
    var url     = window.location.href,
        host    = url.match(/^http:\/\/[^/]+/);

    return host ? host[0] : null;
}

