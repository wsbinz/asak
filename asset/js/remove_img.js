/**
 * Created by s.manczak on 07.12.2017.
 */

/*$('.remove_img > img').on('click',function(){

    alert("cos");
/!*
    $.ajax({
        type: 'POST',
        url: "../../admin/product/remove_img",
        data: {id_img: $('[name=mat_nazwk]').val()},
        dataType: 'text',
        success: function(html){
            html = JSON.parse(html);

            $('tbody > tr').remove();
            var count = html.length;
            console.log(html);
            for(var i = 0; i<count; i++)
            {
                $("tbody").append("<tr><td width=5%><input type='checkbox' class='pull-left' name='retire_product["+html[i]['nr_mat']+"]' ></td><td width=15%>"+ html[i]['kod_pkwiu'] +"</td><td width=80%>"+ html[i]['mat_nazwk'] +"</td><td><a href='{{ base_url() }}admin/product/edit_product/"+html[i]['id_indk']+"' </a> </td></tr>");
            }

        }

    });*!/

});*/

$('.remove_img > img').on('click',function(){

    var segment_url = $(location).attr('href').split("/");
    console.log(this.id);


    $.ajax({
        type: 'POST',
        url: "../../../admin/product/remove_img",
        data: {id_img: this.id,segment_url: segment_url},
        dataType: 'text',
        success: function(html){

            html = JSON.parse(html);
         if(html.code = 200)
         {
             $('.edit_img  img').remove()
         }

        }

    });

});