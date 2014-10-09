/* ---------- @ coll -----------*/
jQuery.noConflict()(function($){
    $(window).load(function() {   
            $(function () {
                $('.coll-item-trigger span').click(function () {
                    if ($(this).hasClass('icon-plus-sign')) {
                        $(this).removeClass('icon-plus-sign').addClass('icon-minus-sign');
                    } else {
                        $(this).removeClass('icon-minus-sign').addClass('icon-plus-sign');
                    }
                });
                $('.coll-item-title').click(function () {
                    $trigger = $(this).parent().parent().find('.coll-item-trigger span');
                    if ($trigger.hasClass('icon-plus-sign')) {
                        $trigger.removeClass('icon-plus-sign').addClass('icon-minus-sign');
                    } else {
                        $trigger.removeClass('icon-minus-sign').addClass('icon-plus-sign');
                    }
                });
            });
        });
    });
/* ---------- @ Portfolio -----------*/
jQuery.noConflict()(function($){
$(window).load(function() {   
 $(function(){
      
      var $container = $('#portfolio');
      

                $container.isotope({
                  itemSelector : '.block',
                  layoutMode : 'masonry'
                  
                });
      
      var $optionSets = $('#options .option-set'),
          $optionLinks = $optionSets.find('a');

      $optionLinks.click(function(){
        var $this = $(this);
        // don't proceed if already selected
        if ( $this.hasClass('selected') ) {
          return false;
        }
        var $optionSet = $this.parents('.option-set');
        $optionSet.find('.selected').removeClass('selected');
        $this.addClass('selected');
  
        // make option object dynamically, i.e. { filter: '.my-filter-class' }
        var options = {},
            key = $optionSet.attr('data-option-key'),
            value = $this.attr('data-option-value');
        // parse 'false' as false boolean
        value = value === 'false' ? false : value;
        options[ key ] = value;
        if ( key === 'layoutMode' && typeof changeLayoutMode === 'function' ) {
          // changes in layout modes need extra logic
          changeLayoutMode( $this, options )
        } else {
          // otherwise, apply new options
          $container.isotope( options );
        }
        
        return false;
      });
      });
      
    });
});

/* ---------- @ Prettyphoto -----------*/
jQuery.noConflict()(function($){

    jQuery("a[data-rel^='prettyPhoto']").prettyPhoto({hook: 'data-rel'});
    jQuery('a[data-rel]').each(function() {
        jQuery(this).attr('rel', $(this).attr('data-rel')).removeAttr('data-rel');
    });
    

});

 /* ---------- @ GoogleMap ------*/
jQuery.noConflict()(function($){
var $map = $('#map-content');
    google.maps.event.addDomListener(window, 'resize', function() {
      map.setCenter(homeLatlng);
    });
    if( $map.length ) {

      $map.gMap({
        address: '14 Crittenden Way, Apt 6, Rochester, NY, 14623, United States',
        zoom: 14,
        markers: [
          { 'address': '14 Crittenden Way, Apt 6, Rochester, NY, 14623, United States', }
        ]
      });

    }
});


/* ---------- @ Contact From -----------*/

jQuery.noConflict()(function($){
$(document).ready(function ()
{ // 
    $("#ajax-contact-form").submit(function ()
    {
        //
        var str = $(this).serialize(); // 
        $.ajax(
        {
            type: "POST",
            url: "contact.php",
            data: str,
            success: function (msg)
            {
                $("#note").ajaxComplete(function (event, request, settings)
                {
                    if (msg == 'OK') //
                    {
                        result = '<div class="notification_ok">Message was sent to website administrator, thank you!</div>';
                        $("#fields").hide();
                    }
                    else
                    {
                        result = msg;
                    }
                    $(this).html(result);
                });
            }
        });
        return false;
    });
});
});

/* ---------- @ Scroll to Top -----------*/
jQuery.noConflict()(function($){
    // Scroll to top button
    var scrollTimeout;
    
    $('a.scroll-top').click(function(){
        $('html,body').animate({scrollTop:0},500);
        return false;
    });

    $(window).scroll(function(){
        clearTimeout(scrollTimeout);
        if($(window).scrollTop()>400){
            scrollTimeout = setTimeout(function(){$('a.scroll-top:hidden').fadeIn()},100);
        }
        else{
            scrollTimeout = setTimeout(function(){$('a.scroll-top:visible').fadeOut()},100);    
    }
    });
    
});

/* ---------- @ Scroll to Section -----------*/
jQuery.noConflict()(function($){
$('.nav a').click(function(e){
e.preventDefault();
var des = $(this).attr('href');
goToSectionID(des);
})
function goToSectionID(des){
    var os = (history.pushState)?51:0;
    os = (jQuery(window).width()>800)?os:0;

    var pos = (jQuery(des).length>0 )?jQuery(des).offset().top-os:0;
    onanimate = true;
    jQuery('html,body').animate({scrollTop:pos},1000,function(){
        if(history.pushState){
            history.pushState(null,null,des);
        }else       window.location.hash = des;
        jQuery(window).scrollTop(pos);
        onanimate=false
    });
}
});
/* ---------- @ Responsive Nav -----------*/
jQuery.noConflict()(function($){
// Create the dropdown nav for responsive
$("<select />").appendTo(".nav-collapse ");
    $("<option />", {
    "selected": "selected",
    "value"   : "",
    "text"    : "Menu"
    }).appendTo(".nav-collapse select");
    // Populate dropdown with nav items
    $(".nav-collapse a").each(function() {
    var el = $(this);
    $("<option />", {
    "value"   : el.attr("href"),
    "text"    : el.text()
    }).appendTo(".nav-collapse select");
    });
    // To make dropdown actually work
    $(".nav-collapse select").change(function() {
    window.location = $(this).find("option:selected").val();
});
});