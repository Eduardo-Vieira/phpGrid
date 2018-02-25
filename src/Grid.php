<?php
/**
 * Gerador de tabelas
 * 
 * @author Luiz Schmitt <lzschmitt@gmail.com>
 * @author Eduardo Vieira <gceduvieira@gmail.com>
 */
class Grid {
    
    protected static $config = [];
    
    protected static $columnsConfig = [
        'primaryKey', 
        'actions', 
        'attributes'
    ];

    protected static $pagination = null;

    protected static function getElement($nameOnly = false) {
        $name = trim(preg_replace('/(\#|\.)/', '', self::$config['el']));
        
        if (strpos(self::$config['el'], '.')) {
            $element = "class='$name'";
        } else {
            $element = "id='$name'";
        }

        return ($nameOnly) ? $name : $element;
    }

    protected static function getAttributes($config) {
        foreach ($config as $key => $value) {
            $attributes[] = trim($key . '="' . $value . '"');
        }

        return isset($attributes) ? implode(' ', $attributes) : [];
    }

    protected static function getData() {
        return isset(self::$config['data']) ? self::$config['data'] : [];
    }

    protected static function getRowConfig() {
        foreach (self::$config['columns'] as $key => $value) {
            if (in_array($key, self::$columnsConfig)) {
                $rowConfig[$key] = $value;
            }
        }

        return isset($rowConfig) ? $rowConfig : [];
    }

    protected static function getColumns() {
        foreach (self::$config['columns'] as $column => $config) {
            // ignora os parametros de configuração das columns
            if (is_string($column) && in_array($column, self::$columnsConfig)) {
                continue;
            } else {
                // se o config for string então é o proprio campo
                if (is_string($config)) {
                    $column = $config;
                    $config = null;
                }

                $columns[$column] = $config;
            }
        }

        return $columns;
    }

    protected static function isSearchable() {
        return isset(self::$config['searchable']) && self::$config['searchable'] === false ? false : true; 
    }

    protected static function isSortable() {
        return isset(self::$config['searchable']['sort']) && self::$config['searchable']['sort'] === false ? false : true; 
    }

    protected static function isPaginate() {
        return isset(self::$config['pagination']) && self::$config['pagination'] === false ? false : true; 
    }

    protected static function regexSearchType($value, $type) {
        switch ($type) {
            case 'ANY':
                $value = "/.*$value.*/";
            break;

            case 'THIS':
                $value = "/^$value$/";
            break;

            case 'LEFT':
               $value = "/.$value$/";
            break;

            case 'RIGHT':
                $value = "/$value./";
            break;
        }

        return $value;
    }

    public static function multiSearch($fields = [], $values = [], $data) {

    }

    public static function search($field, $value, $data) {
        $new_data = [];

        foreach ($data as $index => $array) {
            $parts      = explode(':', $field);
            $field      = $parts[0];
            $searchType = isset($parts[1]) ? strtoupper($parts[1]) : 'ANY';

            $value = self::regexSearchType($value, $searchType);
      
            if (preg_match($value, $array[$field])) {
                $new_data[$index] = $data[$index];
            }

        }

        return $new_data;
    }

    protected static function sanitizeRequestPaginate() {
        $new_request = [];

        foreach ($_REQUEST as $key => $value) {
            if (strpos($key, ':')) {
                if (!empty($value)) { 
                    $new_request[$key] = $value; 
                }
            }
        }
        return $new_request;
    }

    protected static function paginate() {
        $name        = self::getElement(true);
        $data        = self::getData();
        $perPage     = self::$config['pagination']['perPage'];
        $maxPage     = self::$config['pagination']['pages'];
        $currentPage = $_REQUEST['thupan-page'] ? $_REQUEST['thupan-page'] : 1;
        
        $formSearchData = self::sanitizeRequestPaginate();

        if (isset($formSearchData)) {
            foreach ($formSearchData as $key => $value) {

            }
        }

        $dataPage  = array_chunk($data, $perPage);
        $totalPage = count($dataPage);
        
        $last  = ceil($totalPage);
        $start = ($currentPage - $maxPage) > 0 ? $currentPage - $maxPage : 1;
        $end   = ($currentPage + $maxPage) < $last ? $currentPage + $maxPage : $last;
        $links = null;
        
        for ($p = $start; $p <= $end; $p++) {
            $links .= "<li>
                            <a href='?page=$p'>$p</a>
                       </li>";
        }
        
        self::$pagination = "<tr>
                                <td colspan='100%'>
                                    <ul id='thupan-pagination-$name' class='pagination thupan-pagination-$name' style='float:right'>
                                        <li><a href='?page=1'>Primeiro</a></li>
                                        $links
                                        <li><a href='?page=$totalPage'>Ultimo</a></li>
                                    </ul>
                                </td>
                            </tr>";
        
        return $dataPage[$currentPage - 1];
    }
 
    protected static function createHeader() {
        $name = self::getElement(true);

        foreach(self::getColumns() as $column => $config) {
            // verifica se o campo informa o tipo de pesquisa
            if (strpos($column, ':')) {
                $parts      = explode(':', $column);
                $column     = $parts[0];
                $searchType = $parts[1];
            } else {
                $searchType = 'ANY';
            }

            $searchField = "<input class='form-control' name='$column:$searchType'>";

            // se o campo possuir configurações especificas
            if (is_array($config)) {
                $label       = isset($config['label']) ? $config['label'] : $column;
                
                if (isset($config['searchField']) && is_array($config['searchField'])) {
                    $attributes = self::getAttributes($config['searchField']['attributes']);
                    // campo de buscar padrão
                    $searchField = "<input $attributes name='$column:$searchType'>";
                } 
                
                else if (isset($config['searchField']) && !is_array($config['searchField'])) {
                    // campo de buscar padrão
                    $searchField = $config['searchField'];
                } 

            } else {
                $label      = $column;
            }

            $sortIcon = self::isSortable() ? "<i class='fa fa-sort-amount-down thupan-formSearch-$name-sort' data-sort='ASC' data-field='$column' style='float:right; cursor:pointer'></i>" : false;

            $header[] = "<th>$label $sortIcon</th>";
            $search[] = "<th>$searchField</th>";
        }

        $header = implode(' ', $header);
        $header = "<tr>$header</tr>";

        if (self::isSearchable()) {
            $search[] = "<th style='width:100px !important'>
                            <button id='thupan-formSearch-$name-search' class='btn btn-info thupan-formSearch-$name-search'>
                                <i class='fa fa-search'></i>
                            </button>

                            <button id='thupan-formSearch-$name-reload' class='btn btn-default thupan-formSearch-$name-reload'>
                                <i class='fa fa-undo'></i>
                            </button>
                        </th>";
            $search = implode(' ', $search);
            $header .= "<tr>$search</tr>";
        }

        return $header;   
    }

    protected static function createBody() {

        $data = self::isPaginate() ? self::paginate() : self::getData();

        foreach($data as $index => $row) {
            $body[] = "<tr>";

            foreach(self::getColumns() as $column => $config) {
                // verifica se o campo informa o tipo de pesquisa
                if (strpos($column, ':')) {
                    $parts      = explode(':', $column);
                    $column     = $parts[0];
                    $searchType = $parts[1];
                }

                // se o campo possuir configurações especificas
                if (is_array($config)) {
                    $attributes  = self::getAttributes($config['attributes']);
                    // se o valor do campo for array
                    if (isset($config['value']) && is_array($config['value'])) {
                        // se o valor possuir definições
                        if (is_array($config['value'][$row[$column]])) {
                            // override
                            $attributes = self::getAttributes($config['value'][$row[$column]]['attributes']);
                            $value = $config['value'][$row[$column]]['output'];
                        } 
                        // se não houver definicoes pega o valor para substituição
                        else {
                            $value = $config['value'][$row[$column]];
                        }
                    } 
                    // valor do campo original
                    else {
                        $value = $row[$column];
                    }

                } else {
                    $attributes = null;
                    $value = $row[$column];
                }

                $body[] = "<td $attributes>$value</td>";
            }
            
            if (self::isSearchable()) {
                $body[] = "<td></td>";
            }
            
            $body[] = "</tr>";
        }

        return implode(' ', $body); 
    }

    protected static function createFooter() {
        return self::$pagination;
    }

    public static function create($config = []) {
        self::$config = $config;

        $identifier    = self::getElement();
        $name          = self::getElement(true);
        
        $attributes    = self::getAttributes($config['attributes']);
        $attributesH   = self::getAttributes($config['attributesHeader']);
        $attributesB   = self::getAttributes($config['attributesBody']);
        $attributesF   = self::getAttributes($config['attributesFooter']);
        
        $headerContent = self::createHeader();
        $bodyContent   = self::createBody();
        $footerContent = self::createFooter();

        echo $_REQUEST['thupan-reload-data-' . $name] ?

            $bodyContent
        
        :   "
            <form id='thupan-formSearch-$name' class='thupan-formSearch-$name' name='thupan-formSearch-$name' action='' method='GET'>
                <table $identifier $attributes>
                    <thead $attributesH>
                        $headerContent
                    </thead>
                
                    <tbody $attributesB thupan-data-load='true'>
                        $bodyContent
                    </tbody>

                    <tfoot $attributesF>
                        $footerContent
                    </tfoot>
                </table>
                <input type='hidden' id='thupan-reload-data-$name' class='thupan-reload-data-$name' name='thupan-reload-data-$name' value='true'>
                <input type='hidden' id='thupan-currentPage-$name' class='thupan-currentPage-$name' name='thupan-currentPage-$name' value='1'>
            </form>";
    }
}