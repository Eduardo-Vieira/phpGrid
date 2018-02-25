<?php
require_once __DIR__ . '/../src/Grid.php';

if ($_REQUEST['identifier'] == 'xhrPesquisar') {

    $data = [
        [
            'USUARIO.ID_USUARIO' => 1,
            'USUARIO.TX_LOGIN' => 'josh.peter',
            'USUARIO.TX_EMAIL' => 'josh.peter@gmail.com',
            'USUARIO.TX_SENHA' => '123123',
            'USUARIO.CS_SITUACAO' => '1'
        ],
    
        [
            'USUARIO.ID_USUARIO' => 2,
            'USUARIO.TX_LOGIN' => 'joao.pedro',
            'USUARIO.TX_EMAIL' => 'jpedro@gmail.com',
            'USUARIO.TX_SENHA' => '456543',
            'USUARIO.CS_SITUACAO' => '2'
        ],  
        
        [
            'USUARIO.ID_USUARIO' => 3,
            'USUARIO.TX_LOGIN' => 'maria.julia',
            'USUARIO.TX_EMAIL' => 'jmaria@gmail.com',
            'USUARIO.TX_SENHA' => '443445',
            'USUARIO.CS_SITUACAO' => '1'
        ],  
        
        [
            'USUARIO.ID_USUARIO' => 4,
            'USUARIO.TX_LOGIN' => 'carlos.edu',
            'USUARIO.TX_EMAIL' => 'jcedut85@gmail.com',
            'USUARIO.TX_SENHA' => '5566',
            'USUARIO.CS_SITUACAO' => '1'
        ],  
        
        [
            'USUARIO.ID_USUARIO' => 5,
            'USUARIO.TX_LOGIN' => 'suellen.gomes',
            'USUARIO.TX_EMAIL' => 'susu@gmail.com',
            'USUARIO.TX_SENHA' => '46663',
            'USUARIO.CS_SITUACAO' => '2'
        ],  
        
        [
            'USUARIO.ID_USUARIO' => 6,
            'USUARIO.TX_LOGIN' => 'malu.zambea',
            'USUARIO.TX_EMAIL' => 'malu@gmail.com',
            'USUARIO.TX_SENHA' => '433343',
            'USUARIO.CS_SITUACAO' => '1'
        ],  
        
        [
            'USUARIO.ID_USUARIO' => 7,
            'USUARIO.TX_LOGIN' => 'suanyrley.juana',
            'USUARIO.TX_EMAIL' => 'suanyr.ju@gmail.com',
            'USUARIO.TX_SENHA' => '544544',
            'USUARIO.CS_SITUACAO' => '1'
        ],  
        
        [
            'USUARIO.ID_USUARIO' => 8,
            'USUARIO.TX_LOGIN' => 'harry.potter',
            'USUARIO.TX_EMAIL' => 'harrypotter@gmail.com',
            'USUARIO.TX_SENHA' => '4434554',
            'USUARIO.CS_SITUACAO' => '2'
        ],  
        
        [
            'USUARIO.ID_USUARIO' => 9,
            'USUARIO.TX_LOGIN' => 'amanda.sandra',
            'USUARIO.TX_EMAIL' => 'amanda.sandra@gmail.com',
            'USUARIO.TX_SENHA' => '6666',
            'USUARIO.CS_SITUACAO' => '2'
        ],  
        
        [
            'USUARIO.ID_USUARIO' => 10,
            'USUARIO.TX_LOGIN' => 'mario pereira',
            'USUARIO.TX_EMAIL' => 'm.pereira@gmail.com',
            'USUARIO.TX_SENHA' => '43333',
            'USUARIO.CS_SITUACAO' => '1'
        ],  
            
    ];

    Grid::create([
        'el' => '#tabela',
        'attributes' => [
            'class' => 'table table-striped',
            'style' => 'width:100%; border:1px solid #ccc'
        ],
        'attributesHeader' => [
            
        ],
        'attributesBody' => [
            
        ],
        'attributesFooter' => [
            
        ],
        'data'       => $data,
        'searchable' => [
            'sort' => true
        ],//true,
        'pagination' => [
            'perPage' => 5,
            'pages' => 10
        ], // or true/false,

        'readyonly' => false,

        'columns' => [
    
            'primaryKey' => ['USUARIO.ID_USUARIO'],

            'attributes' => [
                'editable' => 'true',
                'class' => 'table-row',
                'style' => 'border:1px solid #ccc'
            ],
    
            'actions' => [
                'detail',
                'edit',
                'delete',
                'save'
            ],
    
            'USUARIO.ID_USUARIO',

            'USUARIO.TX_LOGIN' => [
                'label' => 'Login',
                'attributes' => [
                    'class' => 'row'
                ],

            ],
    
    
            'USUARIO.TX_EMAIL',
    
    
            'USUARIO.TX_SENHA' => [
                'label' => 'SENHA',
                'searchField' => [
                    'attributes' => [
                        'class' => 'form-control',
                        'id' => 'comboSearch'
                    ]
                ],
            ],
    
            'USUARIO.CS_SITUACAO' => [
                'value' => [
                    1 => [
                        'attributes' => [
                            'style' => 'color:blue;'
                        ],
    
                        'output' => 'ATIVADO'
                    ],
    
                    2 => 'DESATIVADO',
                ],
    
                'attributes' => [
                    'style' => 'color:red'
                ]
            ],
    
        ]
    ]);

}