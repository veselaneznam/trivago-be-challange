/**
 * Created by vesela on 4/16/16.
 */

$(function() {

    $('#create_criteria').click(function (e) {
        e.preventDefault();
        $(location).attr('href','/criteria/add');
    });

    $('#create_positive').click(function (e) {
        e.preventDefault();
        $(location).attr('href','/positive/add');
    });

    $('#create_negative').click(function (e) {
        e.preventDefault();
        $(location).attr('href','/negative/add');
    });

    $('.back_criteria').click(function(e){
        e.preventDefault();
        $(location).attr('href','/criteria');

    });

    $('.back_negative').click(function(e){
        e.preventDefault();
        $(location).attr('href','/negative');

    });
    
    $('.back_positive').click(function(e){
        e.preventDefault();
        $(location).attr('href','/positive');

    });
    
    $('.back_hotel').click(function(e){
        e.preventDefault();
        $(location).attr('href','/hotel/add');

    });
    
    $('.back_home').click(function(e){
        e.preventDefault();
        $(location).attr('href','/');

    });
});