//Ukrywanie sekcji
$(document).ready(function ()
{


    var length_section = $("section").length;

    var x = 1;
    for ( var i = 2 ; i<=length_section; i++)
    {

      //  $("#"+i).hide();

    }


    $( "form .btn").click(function(e) {
        console.log(e);
        $("#"+x).hide();
        var var_help = x+1;
        x = x+1;
        $("#"+var_help).show();
        console.log("x wynosi:" + x + "  A var_help = " + var_help)
    });

    $( "form .prev").click(function(e) {
        console.log(e);
        $("#"+x).hide();
        var var_help = x-2;
        x = x-2;
        $("#"+var_help).show();
        console.log("x wynosi:" + x + "  A var_help = " + var_help)
    });


    $(".dost").hide();

    $(".empty_dost").click(function () {
        if($(".empty_dost").is(':checked')) {
            $(".dost").show();
            $("[name=select_dost]").prop("disabled", true)
        }
        else
        {
            $(".dost").hide();
            $("[name=select_dost]").prop("disabled", false)
        }
    })


});
//Progress bar. Będzie się aktualizował po każdym wypełnieniu danych



//$("#sum_DSW").html($("#wart_dl").val() * $("#wart_szer").val() * $("#wart_wys").val())

$("input[id^=wart_]").on("change",function () {

    $("#sum_DSW").html($("#wart_dl").val() * $("#wart_szer").val() * $("#wart_wys").val());
});


