$(document).ready(()=>{
	var canvas = document.querySelector('.canvas');
	canvas.width = canvas.offsetWidth;
	canvas.height = canvas.offsetHeight;
	var ctx = canvas.getContext('2d');
	ctx.font = "10px Arial";
	let fontSize = 10;
	let selectedTool = $(".pen");
    let painting = false;
    let texting = false;
    let workspace_id = $(".canvas").attr("data-id");
    let color = 'black';
    let tool = "pen";
    let selected = $(".color-black");
    $(".color-black").toggleClass("selected-color");

    $(".canvas").mouseout(()=>{
    	painting = false;
    })

   	$(".canvas").mousedown((event)=>{
   		if(tool === "pen")
   		{
   			painting = true;
 			sendMessage(event,"initializePath");
   			ctx.beginPath();
   		}
   		else if(tool === "text"){
   			tool = "selecting";
   			let posObject = relativePos(event,canvas);
   			$(".canvas-wrapper").append("<textarea class=\"text-input\"></textarea>");
   			$(".text-input").mousemove(resizeTextInput(event));
   			$(".text-input").css("left",event.clientX-6);
   			$(".text-input").css("top",event.clientY-11);
   			$(".text-input").css("width",'1px');
   		}
   		else if(tool === 'typing'){
   			tool = 'text';
   			let text = $(".text-input").val();
   			let offset = $(".text-input").offset();
   			let width = $(".text-input").width();
   			$(".text-input").remove();
   			wrapText(text,offset.left,offset.top,width,fontSize,color);
   			sendMessage(event,"wrapText",{text:text,x:offset.left,y:offset.top,width:width,lineHeight:fontSize,color:color});
   		}
   	});

   	$(".text").click(()=>{
   		tool = 'text';
   		toggleSelectedTool(".text");
   	})

   	$(".canvas").mouseup((event)=>{
   		if(tool == "pen"){
   			painting = false;
   		}
   		if(tool == 'selecting'){
   			tool = 'typing';
   		}
   	});

   	$(".pen").click(()=>{
   		tool = 'pen';
   		toggleSelectedTool(".pen");
   	})

   	function resizeTextInput(event){
   		let offset = $(".text-input").offset();
   		$(".text-input").css("width",(event.clientX)-offset.left);
   		$(".text-input").css("height",(event.clientY)- offset.top);
   	}


   	$(".canvas").mousemove((event)=>{
   		if(tool === 'pen'){
	   		if(painting){
	   			let coordinates = relativePos(event,canvas);
	   			ctx.strokeStyle = color;
	   			ctx.lineTo(coordinates.x, coordinates.y);
	   			ctx.stroke();
	   			sendMessage(event,"draw",[coordinates.x,coordinates.y,color]);
	   		}
   		}
   		if(tool === 'selecting'){
   			let offset = $(".text-input").offset();
   			$(".text-input").css("width",(event.clientX)-offset.left);
   			$(".text-input").css("height",(event.clientY)- offset.top);
   		}
   	});

   	function wrapText(text, marginLeft, marginTop, maxWidth, lineHeight,color)
    {
    	ctx.fillStyle = color;
        var words = text.split(" ");
        var countWords = words.length;
        var line = "";
        for (var n = 0; n < countWords; n++) {
            var testLine = line + words[n] + " ";
            var testWidth = ctx.measureText(testLine).width;
            if (testWidth > maxWidth) {
                ctx.fillText(line, marginLeft, marginTop);
                line = words[n] + " ";
                marginTop += lineHeight;
            }
            else {
                line = testLine;
            }
        }
        ctx.fillText(line, marginLeft, marginTop);
    }

   	function generateCanvasText(text,x,y,width){
   		let iteration = 1;
   		while(ctx.measureText(text).width > width){
   			let buffer = (text.length*fontSize-width)/fontSize;
   			let printText = text.substr(0,text.length-1-buffer);
   			text = text.substr(text.length-1-buffer);
   			ctx.fillText(printText,x,y+iteration*fontSize);
   			iteration += 1;
   		}
   		ctx.fillText(text,x,y+iteration*fontSize);
   	}


   	function toggleSelectedColor(cls){
   		$(cls).toggleClass("selected-color");
   		$(selected).toggleClass("selected-color");
   		selected = $(cls);
   	}

   	function toggleSelectedTool(cls){
   		$(cls).toggleClass("selected-tool");
   		$(selectedTool).toggleClass("selected-tool");
   		selectedTool = $(cls);
   	}

   	$(".eraser").click(()=>{
   		tool = "pen";
   		color = 'white';
   		toggleSelectedTool(".eraser");
   	})


   	$(".color-red").click(()=>{
   		color = "red";
   		toggleSelectedColor(".color-red");
   	});

   	$(".color-black").click(()=>{
   		color = "black";
   		toggleSelectedColor(".color-black");
   	});

   	$(".color-green").click(()=>{
   		color = "green";
   		toggleSelectedColor(".color-green");
   	});

   	$(".color-blue").click(()=>{
   		color = "blue";
   		toggleSelectedColor(".color-blue");
   	});

   	$(".color-bluesky").click(()=>{
   		color = "deepskyblue";
   		toggleSelectedColor(".color-bluesky");
   	})

   	function subcribeToChannel(){
   		let msg = {
   			command: "subscribe",
   			workspace_id: workspace_id
   		}
   		let jsonMsg = JSON.stringify(msg);
   		conn.send(jsonMsg);
   	}


   	function printServerMessage(message){
   		console.log(message);
   	}

   	function execute(action,parametres){
   		switch(action){
   			case "initializePath": initializePath();
  			break;
  			case "draw": draw(...parametres); break;
  			case 'printMessage': printMessage(parametres); break;
  			case 'printServerMessage': printServerMessage(parametres); break;
  			case 'wrapText': wrapText(parametres.text,parametres.x,parametres.y,parametres.width,parametres.lineHeight,parametres.color); break;
   		}

   	}


   	var conn = new WebSocket('ws://localhost:8080');

    conn.onopen = function(e) {
    	subcribeToChannel();
        console.info("Connection established succesfully");
    };


    function initializePath(){
    	ctx.beginPath();
    }

    conn.onmessage = function(e) {
        var data = JSON.parse(e.data);
       	execute(data.action,data.parametres);
    };

    function draw(posX,posY,fillStyle){
    	ctx.strokeStyle = fillStyle;
    	ctx.lineTo(posX,posY);
    	ctx.stroke();
    	ctx.strokeStyle = color;
    }

    function printMessage(parametres){
    	$(".chat-content").append("<div class=\"col-mg-6 message\"><b>"+parametres.username + "</b>:"+parametres.msg+"</div>")
    }

    conn.onerror = function(e){
    	alert("Error: something went wrong with the socket.");
        console.error(e);
    }

    function relativePos(event, element) {
  		var rect = element.getBoundingClientRect();
  		return {x: Math.floor(event.clientX - rect.left),
          		y: Math.floor(event.clientY - rect.top)};
	}

    window.sendMessage = (event,action,parametres)=>{
    	let msg = {
    		command: "message",
    		action: action,
    		parametres:parametres,
    		workspace_id: workspace_id
    	}
    	conn.send(JSON.stringify(msg));

    }


    $('.send-button').click((event)=>{
    	let id = $(".send-button").attr("data-id");
    	let message = $(".message-input").val();
    	let parametres = {id:id,msg:message,username:$(".chat").attr("data-user")};
    	sendMessage(event,"printMessage",parametres);
    	printMessage(parametres);
    })

});