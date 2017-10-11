$(document).ready(()=>{
    $(".add-workspace").click(()=>{
        let workSpaceName = $(".workspace-create").val();
        let userId = $(".workspace-create").attr("data-id");
        let object = {
            id: userId,
            workSpaceName: workSpaceName
        };
        $.ajax({
           url:"/api/workspace",
           type:"POST",
            data: object,
            success: (data)=>{
                $(".container").append("<div data-id='"+data.id+"' class='row workspace'><b>"+workSpaceName+"</b></div>");
            }
        });
    });

    $(".workspace").click((event)=>{
        console.log($(event.target).attr("data-id"));
       window.location.href = "/workspace/"+$(event.target).attr("data-id");
    });
})