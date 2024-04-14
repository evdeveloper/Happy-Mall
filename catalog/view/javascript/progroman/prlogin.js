$(function() {
    $('#prlogin-form-register').submit(function() {
        var form = $(this);
        var submit = form.find('button[type=submit]');

        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            dataType: 'json',
            beforeSend: function() {
                submit.button('loading');
            },
            complete: function() {
                submit.button('reset');
            },
            success: function(json) {
                $('.alert, .text-danger').remove();

                if (json['redirect']) {
                    submit.button('toggle');
                    form.prepend('<div class="alert alert-success">' + json['success'] + '</div>');
                    setTimeout(function() {
                        pr_reload();
                    }, 3000);
                }
                else if (json['error']) {
                    if (json['error']['warning']) {
                        form.prepend('<div class="alert alert-warning">' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    }

                    var i;
                    for (i in json['error']) {
                        var element = $('#prlogin-input-' + i.replace('_', '-'));

                        if ($(element).parent().hasClass('input-group')) {
                            $(element).parent().after('<div class="text-danger">' + json['error'][i] + '</div>');
                        } else {
                            $(element).after('<div class="text-danger">' + json['error'][i] + '</div>');
                        }
                    }

                    // Highlight any found errors
                    $('.text-danger').parent().addClass('has-error');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

        return false;
    });

    $('#prlogin-form-login').submit(function(e) {
        e.preventDefault();

        var form = $(this);
        $('.alert, .text-danger').remove();

        var email = form.find('input[name="email"]');
        var password = form.find('input[name="password"]');
        var submit = form.find('button[type=submit]');

        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: form.serialize(),
            dataType: 'json',
            beforeSend: function() {
                submit.button('loading');
            },
            complete: function() {
                submit.button('reset');
            },
            success: function(json) {
                if (json['redirect']) {
                    pr_reload();
                } else if (json['error']) {
                    if (json['error']['warning']) {
                        if (json['error']['warning']) {
                            form.prepend('<div class="alert alert-warning">' + json['error']['warning'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                        }
                    }
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });

        return false;
    });

    $('#prlogin-popup').on('show.bs.modal', function(e) {
        var action = $(e.relatedTarget).attr('data-prlogin');

        if (action) {
            $('#prlogin-tabs').find('a[href="#prlogin-tab-' + action + '"]').tab('show');
        }
    }).on('shown.bs.modal', function(e) {
        $(e.target).find('.tab-pane.active').find('input[type=text]:first').focus();
    });

    $('#prlogin-tabs').find('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        $($(e.target).attr('href')).find('input[type=text]:first').focus();
    });

    function pr_reload() {
        if (location.href.indexOf('logout') > 0) {
            location = '/';
        } else {
            location.reload();
        }
    }
});