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


    $(".vend").hide();

    $(".empty_vend").click(function () {
        if($(".empty_vend").is(':checked')) {
            $(".vend").show();
            $("[name=select_vend]").prop("disabled", true)
        }
        else
        {
            $(".vend").hide();
            $("[name=select_vend]").prop("disabled", false)
        }
    })

    $(".vend_refund").hide();

    $(".empty_vend_refund").click(function () {
        if($(".empty_vend_refund").is(':checked')) {
            $(".vend_refund").show();
            $("[name=select_vend_refund]").prop("disabled", true)
        }
        else
        {
            $(".vend_refund").hide();
            $("[name=select_vend_refund]").prop("disabled", false)
        }
    })



    $('.section_2 tr').on('change',function()
    {
        var dl = $(this).find("#value_length").val();
        var szer = $(this).find("#value_width").val();
        var wys = $(this).find("#value_height").val();

        $(this).find("#sum_DSW").html(dl*szer*wys);
        console.log(this);

    });

});
//Progress bar. Będzie się aktualizował po każdym wypełnieniu danych



//$("#sum_DSW").html($("#wart_dl").val() * $("#wart_szer").val() * $("#wart_wys").val())

/*$("input[id^=wart_]").on("change",function () {
 $("#sum_DSW").html($("#wart_dl").val() * $("#wart_szer").val() * $("#wart_wys").val());
 });*/