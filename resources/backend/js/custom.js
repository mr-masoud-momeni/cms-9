/**
 * Created by masoud on 7/20/2017.
 */
$(document).ready(function (){
    $('.top-bar').addClass('animate');
    $('.page-content-wrapper').addClass('animate');
    $('#sidebar-wrapper').addClass('animate');
    $('.logo-cms').addClass('animate');
});

$("#menu-toggle").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});


(function($){
    $(window).on("load",function(){
        $("#sidebar-wrapper").mCustomScrollbar({
            scrollbarPosition: "outside"
        });
    });
})(jQuery);


$('.dropdown').on('click', function(){
    var thiss=$(this);
    thiss.siblings('.dropdown').removeClass('open');
});
$('.dropdown').on({
    "shown.bs.dropdown": function() {this.closable = false;
        $(this).find('.dropdown-menu').addClass('animated fadeIn');
        setTimeout(function(){
            $('.dropdown-menu').removeClass('animated fade');
        },1000);
    },
    "click":             function() { this.closable = true; },
    "hide.bs.dropdown":  function() { return this.closable;
    }
});