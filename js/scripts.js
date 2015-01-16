jQuery(document).ready(function ($) {

    $('.navbar-collapse ul li a').click(function() {
        $('.navbar-toggle:visible').click();
    });

    $('body').scrollspy({
        target: '.navbar-fixed-top'
    });

    var links = $('.navbar-nav li a, .navbar-brand'),
        slide = $('.slide'),
        button = $('.button'),
        mywindow = $(window),
        htmlbody = $('html,body');


    slide.waypoint(function (event, direction) {

        dataslide = $(this).attr('data-slide');

        if (direction === 'down') {
            $('.navigation li[data-slide="' + dataslide + '"]').addClass('active').prev().removeClass('active');
        }
        else {
            $('.navigation li[data-slide="' + dataslide + '"]').addClass('active').next().removeClass('active');
        }

    });
 
    mywindow.scroll(function () {
        if (mywindow.scrollTop() == 0) {
            $('.navigation li[data-slide="1"]').addClass('active');
            $('.navigation li[data-slide="2"]').removeClass('active');
        }
    });

    function goToByScroll(dataslide) {
        htmlbody.animate({
            scrollTop: $('.slide[data-slide="' + dataslide + '"]').offset().top
        }, 2000, 'easeInOutQuint');
    }



    links.click(function (e) {
        e.preventDefault();
        dataslide = $(this).attr('data-slide');
        goToByScroll(dataslide);
    });

    button.click(function (e) {
        e.preventDefault();
        dataslide = $(this).attr('data-slide');
        goToByScroll(dataslide);

    });

    $('form :checkbox').on('click', function() {
        var $this = $(this),
            name = $this.attr('name');

        if (name === 'recojo') {
            $('#entrega').prop('checked', false);
        } else {
            $('#recojo').prop('checked', false);
        }
    });

    $('#contactForm').on('submit', function(e){
        e.preventDefault();
        var $this = $(this),
            data = $this.serialize(),
            $inputs = $this.find(':input'),
            $btn = $this.find('#btn-send');

        $inputs.prop('disabled', true);
        $btn.text('Enviando...');
        $.ajax({
            url: 'send.php',
            data: data,
            type: 'post',
            dataType: 'json'
        }).done(function(rec){
            if (rec.load) {
                $this[0].reset();
                $('div.alert-success').fadeIn('slow').children('span').text(rec.success_message);
            } else {
                alert(rec.error_message);
            }
        }).always(function(){
            $inputs.prop('disabled', false);
            $btn.text('Enviar');
        }).error(function(x,m){
            alert(x+' '+m);
        });
    });
});