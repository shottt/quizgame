$(function() {
  'use strict';

  $('.js-answer-check').on('click', function(){
    var $selected = $(this);
    if($selected.hasClass('correct') || $selected.hasClass('wrong')){
      return;
    }
    $selected.addClass('selected');
    var answer = $selected.text();
    //post方式のAjax通信
    $.ajax({
      type: 'post',
      url: 'answer.php',
      dataType: 'json',
    }).done(function(res, status){
      console.log(res);
      console.log(status);
      $('.js-answer-check').each(function(){
        if($(this).text() === res.correct_answer){
          $(this).addClass('correct');
        }else{
          $(this).addClass('wrong');
        }
      });
      if(answer === res.correct_answer){
        $selected.children('.js-judge').text('⭕️');
        var intervalId = setInterval(function(){
          $('.blink').stop().fadeToggle(50);
        }, 100);
        setTimeout(function(){
          clearInterval(intervalId);
        }, 1000);

        setTimeout(function(){
            var html = "<form method='post' action='' id='attack' style='display: none;'>" +
            "<input type='hidden' name='attack' value='attack' >" +
            "</form>";
            $('body').append(html);
            $('#attack').submit();
        }, 2000);
      }else{
        $selected.children('.js-judge').text('✖️');
        setTimeout(function(){
            var html = "<form method='post' action='' id='stay' style='display: none;'>" +
            "<input type='hidden' name='stay' value='stay' >" +
            "</form>";
            $('body').append(html);
            $('#stay').submit();
      }, 2000);
      }
    }).fail(function(jqXHR, textStatus, errorThrown){
      console.log('error');
      console.log("jqXHR          : " + jqXHR.status);
      console.log("textStatus     : " + textStatus);
      console.log("errorThrown    : " + errorThrown.message);
    });
  });
});