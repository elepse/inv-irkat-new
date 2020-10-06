var elements = $('.modal-overlay, .modal');

$('#edit').click(function(){
    elements.addClass('active');
});

$('.close-modal').click(function(){
    elements.removeClass('active');
});
$('#AddDocument').click(function(){
    elements.addClass('active');
});