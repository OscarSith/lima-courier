var $selectize = {};

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

$('#recojo, #entrega').on('click', function() {
    var $this = $(this),
        name = $this.attr('name');

    if (name === 'recojo') {
        $('#entrega').prop('checked', false);
    } else {
        $('#recojo').prop('checked', false);
    }
});

$('#alert-modal').on('hidden.bs.modal', function() {
    $('#alert-terminos').addClass('hidden');
    $(this).find('.modal-body p').empty();
    $('#buttons-send').addClass('hidden');
    $('#alert-footer').removeClass('hidden');
});

$('#contactForm').on('submit', function(e) {
    e.preventDefault();
    var $this = $(this),
        data = $this.serialize(),
        $inputs = $this.find(':input:not(:disabled)'),
        $btn = $this.find('#btn-send');

    $inputs.prop('disabled', true);
    $btn.text('Enviando...');
    $.ajax({
        url: 'send.php',
        data: data,
        type: 'post',
        dataType: 'json'
    }).done(function(rec) {
        if (rec.load) {
            $('#alert-modal').find('.modal-body p').text(rec.success_message)
                .end().modal('show');
            $('#alert-terminos').removeClass('hidden');
            $('#buttons-send').removeClass('hidden');
            $('#alert-footer').addClass('hidden');
        } else {
            alert(rec.error_message);
        }
    }).always(function() {
        $inputs.prop('disabled', false);
        $btn.text('Enviar');
    }).fail(function(x,m) {
        alert(x + ' ' + m);
    });
});
$('#frm-franquicia').on('submit', function(e) {
    e.preventDefault();
    var $this = $(this),
        data = $this.serialize(),
        $inputs = $this.find(':input');

    $inputs.prop('disabled', true);
    $.ajax({
        url: 'send-franchise.php',
        type: 'post',
        data: data,
        dataType: 'json'
    }).done(function(rec) {
        if (rec.load) {
            $this.closest('.modal').modal('hide');
            alert(rec.success_message);
        } else {
            alert(rec.error_message);
        }
    }).always(function() {
        $inputs.prop('disabled', false);
    });
});

$('#sliders-home').carousel({
  interval: 4000,
  pause: 'none'
});

$selectize = $('#emails-contacts').selectize({
    plugins: ['remove_button'],
    delimiter: ',',
    persist: false,
    create: function(input) {
        return {
            value: input,
            text: input
        }
    }
});

$('#btn-other-send').on('click', function() {
    $('#alert-modal').modal('hide');
    $('#name').focus();
});
$('#btn-finish').on('click', function() {
    $('#contactForm').trigger('reset');
    try{
        $selectize.clear();
    } catch(e) {
    }
    $('#alert-modal').modal('hide');
});
$('#pais_deliver, #pais').on('change', function(e) {
    var $this = $(this);
    if ($this.val() != 'Perú') {
        $this.parent().next().children().first().addClass('hidden').next().removeClass('hidden').prop('disabled', false);
    } else {
        $this.parent().next().children().first().removeClass('hidden').next().addClass('hidden').prop('disabled', true);
    }
});

$('#sub-slider-home').slick({
    slidesToShow: 3,
    slidesToScroll: 1
});
