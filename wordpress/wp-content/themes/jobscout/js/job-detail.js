(function($){
    'use strict';

    $(document).ready(function(){

        // Share button: use Web Share API or fallback to copy URL
        $(document).on('click', '.btn-share', function(e){
            e.preventDefault();
            var url = $(this).data('url') || window.location.href;
            var title = document.title || '';

            if ( navigator.share ){
                navigator.share({
                    title: title,
                    url: url
                }).catch(function(err){
                    // ignore
                });
                return;
            }

            // Fallback: copy to clipboard
            function copyToClipboard(text){
                var temp = document.createElement('textarea');
                temp.value = text;
                document.body.appendChild(temp);
                temp.select();
                try { document.execCommand('copy'); } catch(e) {}
                document.body.removeChild(temp);
            }

            copyToClipboard(url);
            // Notify user
            var $btn = $(this);
            var orig = $btn.text();
            $btn.text('Link copied');
            setTimeout(function(){ $btn.text(orig); }, 2000);
        });

        // Apply button: scroll to apply form
        $(document).on('click', '.btn-apply', function(e){
            e.preventDefault();
            var target = $(this).data('target') || '#apply';
            var $t = $(target);
            if ( $t.length ){
                $('html, body').animate({ scrollTop: $t.offset().top - 30 }, 500);
                // focus first field
                setTimeout(function(){ $t.find('input, textarea').filter(':visible').first().focus(); }, 600);
            }
        });

        // AJAX submit apply form
        $(document).on('submit', '#job-apply-form', function(e){
            e.preventDefault();
            var $form = $(this);
            var $status = $form.find('.apply-status');

            var data = {
                action: 'jobscout_apply_job',
                nonce: jobscout_job_detail.nonce,
                name: $form.find('input[name="name"]').val(),
                email: $form.find('input[name="email"]').val(),
                message: $form.find('textarea[name="message"]').val(),
                job_id: $form.find('input[name="job_id"]').val()
            };

            $status.text('Sending...');

            $.post(jobscout_job_detail.ajax_url, data, function(resp){
                if ( resp && resp.success ){
                    $status.css('color', 'green').text(resp.data.message || 'Application sent.');
                    $form[0].reset();
                } else {
                    var msg = (resp && resp.data && resp.data.message) ? resp.data.message : 'Submission failed.';
                    $status.css('color', 'red').text(msg);
                }
            }).fail(function(){
                $status.css('color', 'red').text('Submission failed.');
            });
        });

    });

})(jQuery);
