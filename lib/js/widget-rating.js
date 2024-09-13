$('.star-input').click(function() {
    $(this).parent()[0].reset();
    var prevStars = $(this).prevAll();
    var nextStars = $(this).nextAll();
    prevStars.attr('checked',true);
    nextStars.attr('checked',false);
    $(this).attr('checked',true);
  });
  
  $('.star-input-label').on('mouseover',function() {
    var prevStars = $(this).prevAll();
    prevStars.addClass('hovered');
  });
  $('.star-input-label').on('mouseout',function(){
    var prevStars = $(this).prevAll();
    prevStars.removeClass('hovered');
  })