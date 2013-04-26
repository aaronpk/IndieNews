$(function(){

  $("tr.post .vote a").click(function(){
    $.post("/vote", {
      id: $(this).data("id"),
      vote: $(this).data("vote")
    }, function(data){
      $(".vote .post_"+data.id).hide();
    });
  });

});
