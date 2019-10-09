console.log('hello, stranger');


jQuery(function($) {
		
  $(window).on('load',function() {
    console.log('page loaded');
    $('.acf-repeater .acf-row:first-of-type').removeClass('-collapsed');
    
  });
});


jQuery(function($) {
		
    $('#pattern-desc .acf-actions .acf-button').click(function() {
      console.log('clicked add another pattern');
      $('.acf-repeater .acf-row:not(:last-of-type)').addClass('-collapsed');
      
    });
  });