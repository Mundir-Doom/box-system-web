<script type="text/javascript">
    $(document).ready(function(){
        var current_fs, next_fs, previous_fs; //fieldsets
        var opacity;
        $(".form-next").click(function(){
            current_fs = $(this).parent().parent();
            next_fs = $(this).parent().parent().next();
            //Add Class Active
            $("#progressbar li").eq($(".tab-pane").index(next_fs)).addClass("active");
            //show the next fieldset
            next_fs.show();
            //hide the current fieldset with style
            current_fs.animate({opacity: 0}, {
                step: function(now) {
                    // for making fielset appear animation
                    opacity = 1 - now;
                    current_fs.css({
                        'display': 'none',
                        'position': 'relative'
                    });
                    next_fs.css({'opacity': opacity});
                },
                duration: 600
            });
        });
        $(".previous").click(function(){
            current_fs = $(this).parent().parent();
            previous_fs = $(this).parent().parent().prev();
            //Remove class active
            $("#progressbar li").eq($(".tab-pane").index(current_fs)).removeClass("active");
            //show the previous fieldset
            previous_fs.show();
            //hide the current fieldset with style
            current_fs.animate({opacity: 0}, {
                step: function(now) {
                    // for making fielset appear animation
                    opacity = 1 - now;
                    current_fs.css({
                        'display': 'none',
                        'position': 'relative'
                    });
                    previous_fs.css({'opacity': opacity});
                },
                duration: 600
            });
        });
        $('.radio-group .radio').click(function(){
            $(this).parent().find('.radio').removeClass('selected');
            $(this).addClass('selected');
        });
        $(".submit").click(function(){
            return false;
        })
        // Generate strong password for admin
        $(document).on('click', '#gen-pass', function(){
            function randomString(len){
                var chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789!@#$%^&*()_+';
                var out = ''; for(var i=0;i<len;i++){ out += chars.charAt(Math.floor(Math.random()*chars.length)); }
                return out;
            }
            $('#admin_password').val(randomString(14));
        });

        // Test DB connection
        $(document).on('click', '#btn-test-db', function(){
            var $btn = $(this);
            var $out = $('#db-test-result');
            $out.removeClass('text-success text-danger').text('Testing...');
            $btn.prop('disabled', true);
            $.ajax({
                url: '{{ route('install.test-db') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    host: $('#host').val(),
                    dbport: $('#dbport').val(),
                    dbuser: $('input[name="dbuser"]').val(),
                    dbpassword: $('input[name="dbpassword"]').val(),
                    dbname: $('input[name="dbname"]').val(),
                },
                success: function(resp){
                    $out.addClass('text-success').text(resp.message || 'Success');
                },
                error: function(xhr){
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to connect';
                    $out.addClass('text-danger').text(msg);
                },
                complete: function(){
                    $btn.prop('disabled', false);
                }
            });
        });
        });
</script>
