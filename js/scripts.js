$('.navbar-collapse ul li a').click(function() {
    $('.navbar-toggle:visible').click();
});

$('body').scrollspy({
    target: '.navbar-fixed-top'
});

var links = $('.navbar-nav li a, #init'),
    slide = $('.slide'),
    button = $('.button'),
    mywindow = $(window),
    htmlbody = $('html,body');

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

var $fecha = $('#date');
$fecha.attr('value', moment().format('YYYY-MM-DD'));
new Pikaday({
    field: $fecha[0],
    minDate: moment().toDate()
});

var events = 'mouseover mouseleave';
if ('ontouchstart' in document.documentElement) {
    events = 'click';
}
var n = 1;
$('#fixed-contact-rigth').on(events, function(e){
    e.stopPropagation();
    e.stopImmediatePropagation();
    if((e.type === 'mouseover' || e.type === 'click') && n == 1) {
      $(this).children('#panel-left').animate({width:'230px'}, 'fast');
      n++;
    } else if((e.type === 'mouseout' || e.type === 'mouseleave' || e.type === 'click') && n == 2) {
        $(this).children('#panel-left').animate({width:'0px'}, 'fast');
        n = 1;
    }
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
            $('#alert-modal').find('.modal-body p').text(rec.success_message)
                .end().modal('show');
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