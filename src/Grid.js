/** 
 * plugin JQuery para manipular a tabela da classe Grid do
 * Thupan Framework PHP v2
 * 
 * @author Luiz Schmitt <lzschmitt@gmail.com>
 * @author Eduardo Vieira <geduvieira@gmail.com>
 *  
 */

(function($){

    $.fn.grid = function(url, data, cb, cbError, options) {
        var defaults = {
            url: url,
            data: data,
            type: 'GET',
            cache: true,
            async: true,
            page: 1,
            beforeSend: {
                data: "<p style='width:100%; float:left; padding:30px; text-align:center;' colspan='100%'><i class='fa fa-spin fa-spinner'></i> Carregando....</p>",
                callback: null
            }
        };

        if (options) {
            $.extend(defaults, options);
        }

        var el = $(this);

        var isLoaded = function () {
            return el.find('table').length > 0 ? true : false;
        };

        var setCurrentPage = function (page) {
            el.find('.currentPage').val(page);
            defaults.page = page;
        };

        var getCurrentPage = function () {
            return defaults.page;
        };

        var loadData = function (data) {
            if (isLoaded()) {
                el.find('table tbody').html('<tr><td colspan="100%">' + data + '</td></tr>');
            } else {
                el.html(data);
            }
        };

        var loadPagination = function (data) {
            el.find('table tfoot').html(data);
        };

        var serializeData = function() {
            let data = el.find('.formSearch').serializeArray();
        
            data.forEach(function(obj) {
                // por algum motivo desconhecido chaves com . chegam como _ no php
                // então resolvi trocando o . por um | e la no php eu retorno o | para .  no metodo Grid::sanitizeRequestPaginate();
                // quem conseguir resolver esse problema faz um merge-request pra mim
                obj.name = obj.name.replace('.', '|');
                //
                defaults.data[obj.name] = obj.value;
            });
        };

        var resetForm = function () {
            setCurrentPage(1);
            el.find('form').trigger('reset');
        };

        var reloadData = function () {
            el.grid(defaults.url, defaults.data, defaults.callback);
        };

        // começa aqui
        serializeData();

        $.ajax({
            url: defaults.url,
            data: defaults.data,
            type: defaults.type,
            async: defaults.async,
            cache: defaults.cache,
    
            beforeSend: function (xhr, settings) {
                if (defaults.beforeSend.data) {
                    loadData(defaults.beforeSend.data);
                }
    
                defaults.beforeSend.callback ?
                    defaults.beforeSend.callback(xhr, settings) 
                :
                    false;
            },
    
            success: function (data, textStatus, xhr) {
                data = JSON.parse(data);

                loadData(data.output);
                
                if (data.pagination) {
                    loadPagination(data.pagination);
                }

                el.off('click', '.formSearch-search');
                el.off('click', '.formSearch-reload');
                el.off('click', '.formSearch-sort');
                el.off('click', '.pagination a');

                el.on('click', '.formSearch-search', function (e) {
                    setCurrentPage(1);
                    reloadData();
                    return false;
                });

                el.on('click', '.formSearch-reload', function (e) {
                    resetForm();
                    reloadData();
                    return false;
                });

                el.on('click', '.formSearch-sort', function (e) {

                });                

                el.on('click', '.pagination a', function (e) {
                    let page = $(this).attr('href').split('=')[1];

                    defaults.data['page'] = page;
                    defaults.data['reloadData'] = true;

                    reloadData();
                    return false;
                });
                
                cb ? cb(data, textStatus, xhr) : false;
            },

            error: function (xhr, textStatus, error) {
                cbError ? cbError(xhr, textStatus, error) : false;
            }
        });        
        // termina aqui
    
        return this;
    };
})(jQuery);