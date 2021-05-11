var numberNewLabel = 0;
$('#addLabel').on('click',function(){
     numberNewLabel++;
   $('#LabelBody').append("<tr id=\"labelNewKeywords"+numberNewLabel+"\"><td><input  name=\"labelNewKeywords[]\" type=\"text\" placeholder=\"Nouveau Mot-Clé N°"+numberNewLabel+"\"/></td></tr>");
  
});

$('#removeLabel').on('click',function(){
    if(numberNewLabel>0)
  $('#labelNewKeywords'+numberNewLabel).remove();

  if(numberNewLabel >0)
  numberNewLabel--;
  else
  numberNewLabel = 0;

});