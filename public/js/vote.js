$(function(){

  $("tr.post .vote a").click(function(){
    $.post("/vote", {
      id: $(this).data("id"),
      vote: $(this).data("vote")
    }, function(data){
      $(".post_"+data.id+" .vote a").hide();
      if(data.points) {
        $(".post_"+data.id+" .points").text(data.points);
      }
    });
  });

});
