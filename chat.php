<style>

#message { width:30%; float:left;}
#chat { width:60%;float: right;height:50%; overflow:scroll; }
</style>

<div id="chat"></div>

<h1>Chat</h1> 
		<form id="message" action="send_message.php" method="post"> 
		    id:<br /> 
		    <input type="text" name="messages[id]" placeholder="id" value="8" /> 
		    <br /><br /> 
		    room_id:<br /> 
		    <input type="text" name="room_id" placeholder="room_id" value="7" /> 
            <br /><br /> 
             messsge:<br /> 
            <textarea name="messages[message_text]" placeholder="messsge" >Test</textarea>   
		    <br /><br /> 
		    <input id="addShowFormSubmit" type="submit" value="Send" /> 
		</form> 

 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.6/jquery.min.js"></script>
<script>
$("#addShowFormSubmit").click(function(event){
  event.preventDefault();
  var datastring = $("#message").serialize();
$.ajax({
            type: "POST",
            url: "send_message.php",
            data: datastring,
            success: function(data) {
				var obj = jQuery.parseJSON(data);
              $("#chat").prepend("<br>Id: "+obj.user_id +"<br> Message: "+obj.message_text+"<br>");
			 
            },
            error: function(){
                  alert('error handing here');
            }
        }); 

});


</script>