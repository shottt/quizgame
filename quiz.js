$(function() {
  'use strict';

  // 自動スクロール
  var $scrollAuto = $('.js-auto-scroll');
  //animate関数で利用できるプロパティは数値を扱うプロパティの値を簡単に変化させることができる関数
  //scrollTop()」は、ブラウザの画面をスクロールした時の位置（スクロール量）を取得できるメソッド。引数を設定することで任意のスクロール位置まで移動させることが可能
  //scrollHeightは、あふれた(overflowした)画面上に表示されていないコンテンツを含む要素の内容の高さ
  //scrollTopの要素をscrollHeightに徐々に変化させている
  $scrollAuto.animate({
    scrollTop: $scrollAuto[0].scrollHeight
  }, 'fast');
    
  // 回答クリック時の処理
  $('.js-answer-check').on('click', function(){
    var $selected = $(this);
    if($selected.hasClass('correct') || $selected.hasClass('wrong')){
      return;
    }
    $selected.addClass('selected');
    var answer = $selected.text();
    
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
          $('.blink').fadeToggle(50);
        }, 100);
        setTimeout(function(){
          clearInterval(intervalId);
        }, 1000);

        // 
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