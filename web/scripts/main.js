'use strict';
$(document).ready(()=>{
    let selected = [];
    let currentID = $(".user").attr("data-id");
    $(".delete-block").click(()=>{
        let stringifyObject = JSON.stringify(selected);
        let dataObject = {
            selected: stringifyObject,
            id : currentID
        }
        $.ajax({
            url: "/api/deleteSelected",
            type: "POST",
            data: dataObject,
            success: (data)=>{
                if(data.result){
                    if(selected.indexOf(currentID) == -1){
                        selected.forEach((item,i,arr)=>{
                            $('[data-id="' + item + '"]').remove();
                        });
                        $("#hide-block").collapse("hide");
                        selected = [];
                    }
                    else{
                        window.location.href = "/login";
                    }
                }
                else{
                    showError();
                }
            }
        });
    })

    $(".workspace-button").click((event)=>{
        let id =$(event.target).attr("data-id");
        window.location.href = "/workspacelist/"+id;
    })

    $(".info").click(()=>{
        window.location.href = "/logout";
    })


    $(".block-block").click((event)=>{
        let stringifyObject = JSON.stringify(selected);
        let dataObject = {
            selected: stringifyObject,
            id : currentID
        }
        $.ajax({
            url: "/api/blockSelected",
            type: "POST",
            data: dataObject,
            success: (data)=>{
            if(data.result){
            if(selected.indexOf(currentID) == -1){
                selected.forEach((item,i,arr)=>{
                    $('[data-id="' + item + '"]').find(".active").replaceWith("<span class=\"label label-info banned\">Banned</span>");
            });
                selected.forEach((item,i,arr)=>{
                    $('[data-id=' + item + ']').toggleClass("selected");
                });
                $("#hide-block").collapse("hide");
                selected = [];
            }
            else{
                window.location.href = "/login";
            }
        }
        else{
            showError();
        }
        }
        });
    })


    $(".block").click((event)=>{
        let target = event.target;
        let element = $(target).closest(".user");
        let id = element.attr("data-id");
        let sendObject = {
            id: id,
            currentID:currentID
        }

        $.ajax({
            url:"/api/block",
            type:"POST",
            data:sendObject,
            success:(data)=>{
                if(data.result){
                    $(element).find(".active").replaceWith("<span class=\"label label-info banned\">Banned</span>");
                    if(currentID == id){
                        window.location.href = "/login";
                    }
                }
                else{
                    showError();
                }
            },
            error:(data)=>{
            }
        })
    })

    $(".workspace-manager").click(()=>{
        window.location.href = "/spacemanager";
    })

    function showError(){
        $("#error-modal").modal('show');
        setTimeout(()=>{window.location.href = "/login"},2000);
    }

    $(".delete").click((event)=>{
        let target = event.target;
        let element = $(target).closest(".user");
        let id = element.attr("data-id");
        let sendObject = {
            id:id,
            currentID: currentID
        }
        $.ajax({
            url: "/api/delete",
            type: "POST",
            data:sendObject,
            success: (data)=>{
            $(".cssload-preloader").css("display","none");
                if(data.result){
                    if(currentID == id){
                        $(element).remove();
                        window.location.href = "/login";
                    }
                    else{
                        $(element).remove();
                    }
                }
                else{
                    $("#error-modal").modal('show');
                    setTimeout(()=>{window.location.href = "/login"},2000);
                }
            },
            error:(data)=>{
            }
        })
    })


    $(".users-info").click((event)=>{
       let target = event.target;
       if($(target).closest(".user").length && $(target).attr('data-class') != 'non-active'){
           let element = $(target).closest(".user");
           let index =selected.indexOf(element.attr('data-id'));


           if(index != -1){
               selected.splice(index,1);
               if(selected.length == 0){
                   $("#hide-block").collapse("hide");
               }
           }
           else{
               if(selected.length == 0){
                   $("#hide-block").collapse("show");
               }
               selected.push(element.attr('data-id'));
           }
           $(element).toggleClass("selected");
       }
    });

});