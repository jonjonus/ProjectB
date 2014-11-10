$(function(){
  $('#more').on('click', function(e){
    var path = $(location).attr('pathname');
    var unfiltered = (path.search("/admin/") >= 0) ? true : false;
    //el admin ve todas las transacciones
    $.ajax({
      url: ajaxPath+'load_more',
      type: 'post',
      data: {'lastrow': $('#count').text(),
            'unfiltered': unfiltered},
      success: function(response) {
        if(response.success) {
            $('#count').html(response.lastrow);
            
            response.data.forEach(function(entity) {
                var tbody = document.getElementById('tbody');
                var row = document.createElement('tr');

                var cellId = document.createElement('td');
                var cellState = document.createElement('td');
                var cellDate = document.createElement('td');
                var cellInactiveDate = document.createElement('td');
                var cellAmount = document.createElement('td');
                var cellComment = document.createElement('td');
                var cellColumns = document.createElement('td');
            
                var id = document.createTextNode(entity.id);
                var state = document.createTextNode(entity.state);
                var date = document.createTextNode(entity.date);
                var amount = document.createTextNode(entity.amount);
                var comment = document.createTextNode(entity.comment);
                var inactiveDate = document.createTextNode(entity.inactiveDate);

                cellColumns.innerHTML = '<div style="width:120px">'+
                            '<a href="'+entity.show_path +'\" title="Show"><span class="glyphicon glyphicon-eye-open"></span></a>&nbsp;&nbsp;&nbsp; '+
                            '<a href="'+entity.edit_path +'\" title="Edit"><span class="glyphicon glyphicon-pencil"></span></a>&nbsp;&nbsp;&nbsp; '+
                            '<a href="'+entity.new_path  +'\" title="New from"><span class="glyphicon glyphicon-export"></span></a>&nbsp;&nbsp;&nbsp; '+
                            '<input name="rows['+entity.id+']\" type="checkbox" id="rows_'+entity.id+'"> '+
                        '</div>';
                cellColumns.width = '120px';
                
                cellId.appendChild(id);
                cellState.appendChild(state);
                cellDate.appendChild(date);
                cellAmount.appendChild(amount);
                cellComment.appendChild(comment);
                if (unfiltered) {cellInactiveDate.appendChild(inactiveDate)};

                row.appendChild(cellId);
                row.appendChild(cellState);
                row.appendChild(cellDate);
                row.appendChild(cellAmount);
                row.appendChild(cellComment);
                if (unfiltered) {row.appendChild(cellInactiveDate)};
                row.appendChild(cellColumns);

                tbody.appendChild(row);
                
                delete tbody;
                delete row;
                delete cellId;
                delete cellState;
                delete cellDate;
                delete cellAmount;
                delete cellComment;
                delete cellColumns;
                delete id;
                delete state;
                delete date;
                delete amount;
            });    
        
            if (response.eof){
                $("#more").removeClass("btn-primary");
                $("#more").addClass("btn-danger");
            }else{
                $("#more").removeClass("btn-danger");
                $("#more").addClass("btn-primary");
            }
        }else{
            $('#more').fadeOut(300);
        }
      },
      error: function(xhr, desc, err) {
        console.log(xhr);
        console.log("Details: " + desc + "\nError:" + err);
      }
    }); 
  });
});

