@extends('adminlte::page')

@section('js')
    @parent
    <script>
        $(document).ready(function() {
            $('form').on('submit', function() {
                var $form = $(this);

                // If using jquery-validation, check if form is valid
                if ($.isFunction($form.valid) && !$form.valid()) {
                    return false;
                }

                var $submitBtn = $form.find('button[type="submit"]');

                // Prevent double submission
                if ($submitBtn.data('submitted')) {
                    return false;
                }

                $submitBtn.data('submitted', true);
                $submitBtn.prop('disabled', true);
                $submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
            });
        });
    </script>
@stop
