(function($) 
{
    $(document).ready(function() 
    {
        $(document).on('change', '#ldap-client-list', function() 
        {
            let selected_value  = $(this).val();

            if (selected_value)
            {
                $('#ldap-query-types').show();
            }
            else
            {
                reset_query_types();
            }
        });

        $(document).on('changed.bs.select', '#ldap-query-type', function(e, clickedIndex, isSelected, previousValue) 
        {
            $(this).data('prev-val', previousValue);
        })
        .on('change', '#ldap-query-type', function() 
        {
            let selected_value  = $(this).val(),
                previous_value  = $(this).data('prev-val'),
                client_code     = $('#ldap-client-list').val();

            if (selected_value)
            {
                switch (selected_value)
                {
                    case 'fetch':
                        $('#ldap-fetch-area input[name="client_code"]').val(client_code);
                        $('#ldap-fetch-area').show();

                        if (previous_value === 'search')
                        {
                            reset_search_area();
                        }

                        break;
                    case 'search':
                        $('#ldap-search-area input[name="client_code"]').val(client_code);
                        $('#ldap-search-area').show();

                        if (previous_value === 'fetch')
                        {
                            reset_fetch_area();
                        }

                        break;
                }
            }
            else
            {
                if (previous_value === 'fetch')
                {
                    reset_fetch_area();
                }
                else if (previous_value === 'search')
                {
                    reset_search_area();
                }
            }
        });

        $(document).on('changed.bs.select', '#ldap-fetch-attr', function(e, clickedIndex, isSelected, previousValue) 
        {
            $(this).data('prev-val', previousValue);
        })
        .on('change', '#ldap-fetch-attr', function() 
        {
            let selected_value  = $(this).val(),
                previous_value  = $(this).data('prev-val'),
                query_type      = $('#ldap-query-type').val(),
                submit_button   = $('#ldap-fetch-submit-btn'),
                submit_enabled  = submit_button.is(':enabled'),
                value_element   = $('#ldap-fetch-value'),
                values_selector = '#ldap-fetch-values',
                values_element  = $(values_selector);

            if (submit_enabled)
            {
                submit_button.prop('disabled', true);
                reset_query_results();
                reset_data_display_area();
            }

            if (selected_value)
            {
                switch (selected_value)
                {
                    case 'c':
                    case 'co':
                    case 'countryCode':
                        $.get('/ldap-browser/country-info/'+query_type+'/'+selected_value, function(data) 
                        {
                            values_element.html(data);

                            $(values_selector+' .selectpicker').selectpicker({
                                style: 'btn-select',
                                showTick: true
                            });
                        });
                        break;
                    default:
                        switch (previous_value)
                        {
                            case 'c':
                            case 'co':
                            case 'countryCode':
                                $.get('/ldap-browser/generic-input/'+query_type, function(data) 
                                {
                                    values_element.html(data);
                                });
                                break;
                            default:
                                if (value_element.val().length)
                                {
                                    value_element.val('');
                                }
                                break;
                        }
                        break;
                }
            }
            else
            {
                switch (previous_value)
                {
                    case 'c':
                    case 'co':
                    case 'countryCode':
                        $.get('/ldap-browser/generic-input/'+query_type, function(data) 
                        {
                            values_element.html(data);
                        });
                        break;
                    default:
                        if (value_element.val().length)
                        {
                            value_element.val('');
                        }
                        break;
                }
            }
        });

        $(document).on('keyup change input', '#ldap-fetch-value', function() 
        {
            let fetch_attr_len  = $('#ldap-fetch-attr').val().length,
                fetch_value_len = $(this).val().length,
                submit_button   = $('#ldap-fetch-submit-btn'),
                submit_disabled = submit_button.is(':disabled');

            if (!submit_disabled)
            {
                reset_query_results();
                reset_data_display_area();
            }

            if (fetch_attr_len && fetch_value_len)
            {
                if (submit_disabled)
                {
                    submit_button.prop('disabled', false);
                }
            }
            else
            {
                submit_button.prop('disabled', true);
            }
        });

        $(document).on('changed.bs.select', 'select[id^=ldap-search-attr]', function(e, clickedIndex, isSelected, previousValue) 
        {
            $(this).data('prev-val', previousValue);
        })
        .on('change', 'select[id^=ldap-search-attr]', function() 
        {
            let selected_value  = $(this).val(),
                previous_value  = $(this).data('prev-val'),
                digit_regex     = /\d+/,
                id_number       = parseInt($(this).attr('id').match(digit_regex)[0], 10),
                value_element   = $('#ldap-search-value-'+id_number), 
                values_selector = '#ldap-search-values-'+id_number,
                values_element  = $(values_selector),
                query_type      = $('#ldap-query-type').val();

            reset_query_results();
            reset_data_display_area();

            if (selected_value)
            {
                switch (selected_value)
                {
                    case 'c':
                    case 'co':
                    case 'countryCode':
                        $.get('/ldap-browser/country-info/'+query_type+'/'+selected_value+'/'+id_number, function(data) 
                        {
                            values_element.html(data);

                            $(values_selector+' .selectpicker').selectpicker({
                                style: 'btn-select',
                                showTick: true
                            });
                        });
                        break;
                    default:
                        switch (previous_value)
                        {
                            case 'c':
                            case 'co':
                            case 'countryCode':
                                $.get('/ldap-browser/generic-input/'+query_type+'/'+id_number, function(data) 
                                {
                                    values_element.html(data);
                                });
                                break;
                            default:
                                if (value_element.val().length)
                                {
                                    value_element.val('');
                                }
                                break;
                        }
                        break;
                }
            }
            else
            {
                switch (previous_value)
                {
                    case 'c':
                    case 'co':
                    case 'countryCode':
                        $.get('/ldap-browser/generic-input/'+query_type+'/'+id_number, function(data) 
                        {
                            values_element.html(data);
                        });
                        break;
                    default:
                        if (value_element.val().length)
                        {
                            value_element.val('');
                        }
                        break;
                }
            }
        });

        $(document).on('change', 'select[id^=ldap-search-operator]', function() 
        {
            reset_query_results();
            reset_data_display_area();
        });

        $(document).on('keyup change input', '[id^=ldap-search-value]', function() 
        {
            reset_query_results();
            reset_data_display_area();
        });

        $(document).on('submit', '#ldap-fetch-form, #ldap-search-form', function() 
        {  			
            reset_query_results();
            reset_data_display_area();

            $(this).ajaxSubmit({ 
                beforeSubmit:  showGeneralRequest, 
                success:       show_query_results,
                error:         showGeneralError,
                type:          'POST',
                timeout:       30000 
            });

            return false; 
        });

        $(document).on('click', 'button[data-name]', function () 
        {
            let btn     = $(this),
                name    = btn.data('name'),
                client 	= btn.data('client'),
                url     = '/ldap-browser/fetch-details/'+client+'/'+encodeURIComponent(name).replace(/['()]/g, c => `%${c.charCodeAt(0).toString(16).toUpperCase()}`);

            reset_data_display_area();

            btn.button('loading');

            $.get(url, function(data, status) 
            {
                if (status) 
                {
                    let response_obj = jQuery.parseJSON(data);

                    if (response_obj.success)
                    {
                        let results         = response_obj.results,
                            rendered_html   = results.map(get_ldap_html).join('');

                        $('#ldap-tab-contents').html(rendered_html);

                        $('.ldap-browser-placeholder').removeClass('show');
                        $('.ldap-data-display-area').show();
                        $('#ldap-tabs > li:first-child > a').tab('show');

                        btn.removeClass('btn-default').addClass('btn-success').button('reset');
                    }
                    else
                    {
                        showValidationError(response_obj.message);

                        if (btn.hasClass('btn-success'))
                        {
                            btn.removeClass('btn-success').addClass('btn-default').button('reset');
                        }
                        else
                        {
                            btn.button('reset');
                        }
                    }
                }
            });
        });

        $(document).on('click', '#add-filter-btn', function () 
        {
            let last_search_row     = $('div[id^=ldap-search-row]:last'),
                logical_operator    = $('#ldap-logical-operator');

            if (last_search_row.length === 1)
            {
                let digit_regex         = /\d+/,
                    current_row_number  = parseInt(last_search_row.attr('id').match(digit_regex)[0], 10),
                    next_row_number     = current_row_number + 1,
                    next_row_selector   = '#ldap-search-row-'+next_row_number;

                $.get('/ldap-browser/get-search-row/'+next_row_number, function(data) 
                {
                    $(last_search_row).after(data);

                    $(next_row_selector+' .selectpicker').selectpicker({
                        style: 'btn-select',
                        showTick: true
                    });
                });

                if (current_row_number === 1)
                {
                    logical_operator.css('visibility', 'visible');
                    $('#delete-filter-btn').show();
                }
                else if (current_row_number === 3)
                {
                    $(this).hide();
                }
            }
        });

        $(document).on('click', '#delete-filter-btn', function () 
        {
            let last_search_row     = $('div[id^=ldap-search-row]:last'),
                boolean_element     = $('#ldap-search-boolean'),
                boolean_element_val = boolean_element.val(),
                boolean_element_len = boolean_element_val.length,
                logical_operator    = $('#ldap-logical-operator');

            if (last_search_row.length === 1)
            {
                let digit_regex         = /\d+/,
                    current_row_number  = parseInt(last_search_row.attr('id').match(digit_regex)[0], 10);

                last_search_row.remove();

                if (current_row_number === 2)
                {
                    if (boolean_element_len && boolean_element_val === 'or')
                    {
                        boolean_element.val('and');
                        boolean_element.selectpicker('refresh');
                    }

                    logical_operator.css('visibility', 'hidden');
                    $(this).hide();
                }
                else if (current_row_number === 4)
                {
                    $('#add-filter-btn').show();
                }
            }
        });

    });

    // =================================================
    // BLOCK Functions - Start Here
    // =================================================

    get_ldap_html = function(data) 
    {
        let ldap_detail_template    = $('#ldap-detail-template').html();
        return Mustache.render(ldap_detail_template, data);
    };

    get_ldap_row_template = function(data) 
    {
        let ldap_row_template   = $('#ldap-row-template').html();		
        return Mustache.render(ldap_row_template, data);
    };

    show_query_results = function(response)  
    {
        let response_obj    = jQuery.parseJSON(response),
            success         = response_obj.success,
            results_element = $('.ldap-query-results');

        if (success) 
        {			
            results_element.find('tbody').html('');

            if (response_obj.result_count)
            {
                let results         = response_obj.results,
                    rendered_html   = results.map(get_ldap_row_template).join('');

                results_element.find('tbody').append(rendered_html);
            }
            else
            {
                results_element.find('tbody').append('<tr><td colspan="3" class="text-left">No Results Found</td></tr>');
            }										

            results_element.show();
        }
        else
        {
            if (response_obj.csrf_name)
            {
                $('input[name="'+response_obj.csrf_name+'"]').val(response_obj.csrf_value);
            }

            showValidationError(response_obj.message);
        }

        $('.ldap-browser button[type="submit"]').button('reset');
    };

    reset_query_types = function() 
    {
        let query_type_element  = $('#ldap-query-type'),
            query_type          = query_type_element.val();

        if (query_type === 'fetch')
        {
            reset_fetch_area();
        }
        else if (query_type === 'search')
        {
            reset_search_area();
        }

        if (query_type.length)
        {
            query_type_element.val('');
            query_type_element.selectpicker('refresh');
        }

        $('#ldap-query-types').hide();
    };

    reset_fetch_area = function() 
    {
        let attr_element    = $('#ldap-fetch-attr'),
            fetch_attr      = attr_element.val(),
            fetch_attr_len  = fetch_attr.length,
            value_element   = $('#ldap-fetch-value'),
            fetch_value     = value_element.val(),
            fetch_value_len = fetch_value.length,
            values_element  = $('#ldap-fetch-values'),
            submit_button   = $('#ldap-fetch-submit-btn'),
            submit_enabled  = submit_button.is(':enabled');

        $('#ldap-fetch-area').hide();

        if (submit_enabled)
        {
            submit_button.prop('disabled', true);
            reset_query_results();
            reset_data_display_area();
        }

        if (fetch_attr_len)
        {
            switch (fetch_attr)
            {
                case 'c':
                case 'co':
                case 'countryCode':
                    $.get('/ldap-browser/generic-input/fetch', function(data) 
                    {
                        values_element.html(data);
                    });
                    break;
                default:
                    if (fetch_value_len)
                    {
                        value_element.val('');
                    }
                    break;
            }

            attr_element.val('');
            attr_element.selectpicker('refresh');
        }
        else
        {
            if (fetch_value_len)
            {
                value_element.val('');
            }
        }
    };

    reset_search_area = function() 
    {
        let last_search_row     = $('div[id^=ldap-search-row]:last'),
            row_selector        = '#ldap-search-row-',
            boolean_element     = $('#ldap-search-boolean'),
            boolean_element_val = boolean_element.val(),
            boolean_element_len = boolean_element_val.length,
            logical_operator    = $('#ldap-logical-operator'),
            add_filter_button   = $('#add-filter-btn'),
            first_attribute     = $('#ldap-search-attr-1'),
            first_operator      = $('#ldap-search-operator-1'),
            first_value         = $('#ldap-search-value-1'),
            values_element      = $('#ldap-search-values-1');

        reset_query_results();
        reset_data_display_area();

        if (last_search_row.length === 1)
        {
            let digit_regex         = /\d+/,
                current_row_number  = parseInt(last_search_row.attr('id').match(digit_regex)[0], 10);

            $('#ldap-search-area').hide();

            if (logical_operator.css('visibility') === 'visible')
            {
                if (boolean_element_len && boolean_element_val === 'or')
                {
                    boolean_element.val('and');
                    boolean_element.selectpicker('refresh');
                }

                logical_operator.css('visibility', 'hidden')
                $('#delete-filter-btn').hide();
            }

            if (add_filter_button.is(':hidden'))
            {
                add_filter_button.show();
            }

            for (i = current_row_number; i > 1; i--)
            {
                $(row_selector + i).remove();
            }

            if (first_attribute.val().length)
            {
                switch (first_attribute.val())
                {
                    case 'c':
                    case 'co':
                    case 'countryCode':
                        $.get('/ldap-browser/generic-input/search/1', function(data) 
                        {
                            values_element.html(data);
                        });
                        break;
                    default:
                        if (first_value.val().length)
                        {
                            first_value.val('');
                        }
                        break;
                }

                first_attribute.val('');
                first_attribute.selectpicker('refresh');
            }

            if (first_operator.val() !== '=')
            {
                first_operator.val('=');
                first_operator.selectpicker('refresh');
            }
        }
    };

    reset_query_results = function() 
    {
        let ldap_query_results      = $('.ldap-query-results'),
            query_results_visible   = ldap_query_results.is(':visible');

        if (query_results_visible)
        {
            ldap_query_results.hide();
            ldap_query_results.find('tbody').html('');
        }
    };

    reset_data_display_area = function() 
    {
        let browser_placeholder_hidden = $('.ldap-browser-placeholder').is(':hidden');

        if (browser_placeholder_hidden)
        {
            $('.ldap-data-display-area').hide();
            $('#ldap-tab-contents').html('');
            $('.ldap-browser-placeholder').addClass('show');
        }
    };

})(jQuery);