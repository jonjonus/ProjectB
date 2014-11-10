function checkboxesToggleAll(toggle)
{
  var checkboxes = new Array(); 
  checkboxes = document.getElementsByTagName('input');
 
  for (var i=0; i<checkboxes.length; i++){
    if (checkboxes[i].type == 'checkbox'){
      checkboxes[i].checked = toggle;
    }
  }
}

function checkboxesToggleInvert()
{
  var checkboxes = new Array(); 
  checkboxes = document.getElementsByTagName('input');
 
  for (var i=0; i<checkboxes.length; i++){
    if (checkboxes[i].type == 'checkbox'){
      checkboxes[i].checked = !checkboxes[i].checked;
    }
  }
}

function inactive(){
    if($("input:checkbox:checked").length === 0 ){
        alert("You must select at least one Transaction.");
    }else{
        document.forms["form"].submit();
    }
}