$(document).ready(function() {
    $("#proyb_domainmodelbundle_transaction_amount").keydown(function (e) {
        //Alow only one . (190)
        var value=$("#proyb_domainmodelbundle_transaction_amount").val(),
        regex = /\./igm,
        count = value.match(regex),
        count = (count) ? count.length : 0;
        
        if (count == 1 && e.keyCode == 190){
            e.preventDefault();
        }
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
             // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) || 
             // Allow F5 and CTRL+F5
            (e.keyCode == 116) || (e.keyCode == 116 && e.ctrlKey == true) ||
             //Aloww CTRL+C, CTRL+V and CTRL+X
            (e.keyCode == 67 && e.ctrlKey) || (e.keyCode == 86 && e.ctrlKey) || (e.keyCode == 88 && e.ctrlKey) ||
             // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
                 // let it happen, don't do anything
                 return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    
    $("#proyb_domainmodelbundle_transaction_state").change(function() {
    var idTrx = $( "#proyb_domainmodelbundle_transaction_id" ).val();
    var idState = $( "#proyb_domainmodelbundle_transaction_state" ).val();
    $.ajax({
        url: ajaxPath+'count_states',
        type: 'post',
        data: {'idTrx': idTrx,
               'idState': idState},
        success: function(response) {
            if(response.success) {
                $('#proyb_domainmodelbundle_transaction_count').val(response.count);
            }else{
                $('#proyb_domainmodelbundle_transaction_count').val('N/A');
            }
        },
        error: function(xhr, desc, err) {
            console.log(xhr);
            console.log("Details: " + desc + "\nError:" + err);
        }
        }); 
    });
});