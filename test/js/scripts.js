$(document).ready(function () {
    $("#submit").click(function (){
        console.log($("#date").val());
        let dateAndTime = $("#date").val().split("T");
        let date_ = dateAndTime[0];
        let time_ = dateAndTime[1];
        console.log(date_+" "+time_);
        let sub = $('#select option:selected').text();
        if (sub!="Выберите предмет")
        $.post("/test/api.php",{module: "add", date: date_,time: time_,subject: sub },
            function (data){
                console.log("Данные пришли");
                $("#ans").text(data.answer);
                if (data.answer=="Успешно добавленно"){
                    $("#ans").addClass("text-success");
                    $("#ans").removeClass("text-danger");
                }
                else{
                    $("#ans").addClass("text-danger");
                    $("#ans").removeClass("text-success");
                }
            }, "json");
        else {
            $("#ans").text("Выберите предмет!");
            $("#ans").addClass("text-danger");
            $("#ans").removeClass("text-success");
        }
    });
})