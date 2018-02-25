/** 
 * Manipula tabela gerada pela classe Grid do Thupan PHP Framework
 * 
 * @author Luiz Schmitt <lzschmitt@gmail.com>
 * @author Eduardo Vieira <geduvieira@gmail.com>
 *  
 */

var gridOptions = {
    id:   null,
    name: null,
    url:  null,
    data: {},
    callback: null,
    xjx: {
        type: 'GET',
        async: true,
        cache: true,
        beforeSend: {
            data: "<i class='fa fa-spin fa-spinner'></i> Carregando dados na tabela....",
            callback: function () {},
        }
    }
};

isLoaded = function () {
    return $(gridOptions.id + ' table').length > 0 ? true : false;
};

setCurrentPage = function (page) {
    $("#thupan-currentPage-" + gridOptions.name).val(page);
};

getCurrentPage = function () {
    return $("#thupan-currentPage-" + gridOptions.name).val();
};

loadData = function (data) {
    if (isLoaded()) {
        $(gridOptions.id + ' table tbody').html(data);
    } else {
        $(gridOptions.id).html(data);
    }
};

serializeData = function(form) {
    let data = $(form).serializeArray();

    data.forEach(function(obj) {
        // por algum motivo desconhecido chaves com . chegam como _ no php
        // então resolvi trocando o . por um | e la no php eu retorno o | para .  no metodo Grid::sanitizeRequestPaginate();
        // quem conseguir resolver esse problema faz um merge-request pra mim
        obj.name = obj.name.replace('.', '|');
        //
        gridOptions.data[obj.name] = obj.value;
    });
};

grid = function (id, url, data, callback) {
    gridOptions.id   = id;
    gridOptions.name = id.replace('#', '');
    gridOptions.url  = url;
    gridOptions.data = data;
    gridOptions.callback = callback;

    data['thupan-page'] = getCurrentPage();

    serializeData('#thupan-formSearch-' + gridOptions.name);

    $.ajax({
        url:  gridOptions.url,
        data: gridOptions.data,
        type: gridOptions.xjx.type,
        async: gridOptions.xjx.async,
        cache: gridOptions.xjx.cache,

        beforeSend: function (xhr, settings) {
            if (gridOptions.xjx.beforeSend.data) {
                loadData(gridOptions.xjx.beforeSend.data);
            }

            gridOptions.xjx.beforeSend.callback ? gridOptions.xjx.beforeSend.callback(xhr, settings) : false;
        },

        success: function (data, textStatus, xhr) {
            loadData(data, textStatus, xhr);

            $(document).off("click", ".thupan-formSearch-" + gridOptions.name + "-search");
            $(document).off("click", ".thupan-formSearch-" + gridOptions.name + "-reload");
            $(document).off("click", ".thupan-formSearch-" + gridOptions.name + "-sort");
            $(document).off("click", ".thupan-pagination-" + gridOptions.name + " a");
            
            $(document).on("click", ".thupan-formSearch-" + gridOptions.name + "-search", function (e) {
                grid(gridOptions.id, gridOptions.url, gridOptions.data, gridOptions.callback);
                return false;
            });

            $(document).on("click", ".thupan-formSearch-" + gridOptions.name + "-reload", function (e) {
                $(".thupan-formSearch-" + gridOptions.name).trigger('reset');
                grid(gridOptions.id, gridOptions.url, gridOptions.data, gridOptions.callback);
                return false;
            });

            $(document).on("click", ".thupan-formSearch-" + gridOptions.name + "-sort", function (e) {
                gridOptions.data['thupan-order-field'] = $(this).data('field');
                gridOptions.data['thupan-order-sort']  = $(this).data('sort');
                gridOptions.data['thupan-reload-data-' + gridOptions.name] = true;

                // fazer a troca da direção do icone
                  
                grid(gridOptions.id, gridOptions.url, gridOptions.data, gridOptions.callback);                
            });

            $(document).on("click", ".thupan-pagination-" + gridOptions.name + " a", function (e) {
                let page = $(this).attr('href').split('=')[1];
               
                setCurrentPage(page);

                gridOptions.data['thupan-page']        = page;
                gridOptions.data['thupan-reload-data-' + gridOptions.name] = true;
                  
                grid(gridOptions.id, gridOptions.url, gridOptions.data, gridOptions.callback);
            
                return false;
            });

            gridOptions.callback ? gridOptions.callback(data, textStatus, xhr) : false;
        },

        error: function (xhr, textStatus, error) {
            gridOptions.callback ? gridOptions.callback(xhr, textStatus, error) : false;
        }
    });
};