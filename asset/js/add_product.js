//Ukrywanie sekcji
$(document).ready(function () {

    var length_section = $("section").length;

    var x = 1;
    for ( var i = 2 ; i<=length_section; i++)
    {

        $("#"+i).hide();

    }


    $( ".btn").click(function(e) {
        console.log(e);
        $("#"+x).hide();
        var var_help = x+1;
        x = x+1;
        $("#"+var_help).show();
        console.log("x wynosi:" + x + "  A var_help = " + var_help)
    });

    $( ".prev").click(function(e) {
        console.log(e);
        $("#"+x).hide();
        var var_help = x-2;
        x = x-2;
        $("#"+var_help).show();
        console.log("x wynosi:" + x + "  A var_help = " + var_help)
    });

});
//Progress bar. Będzie się aktualizował po każdym wypełnieniu danych



//$("#sum_DSW").html($("#wart_dl").val() * $("#wart_szer").val() * $("#wart_wys").val())

$("input[id^=wart_]").on("change",function () {

    $("#sum_DSW").html($("#wart_dl").val() * $("#wart_szer").val() * $("#wart_wys").val());
});
