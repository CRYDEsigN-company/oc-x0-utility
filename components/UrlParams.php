<?php namespace x0\Toolbox\Components;

use Cms\Classes\ComponentBase;
use \x0\Toolbox\Classes\Helpers\UrlParamsHelper;


class UrlParams extends ComponentBase {

    
    static public $urlParamsHelper;

    public function componentDetails()
    {
        return [
            'name'        => 'Url Params',
            'description' => 'Получение параметров из URL.'
        ];
    }

    public function defineProperties()
   {
    return [
                'partUrl'  => [
                'title'   => 'Откуда брать параметры ?',
                'description'   => 'Указывается переменная со знаком вопроса, например (:params?). В поле адрес вводится эта же переменная со звёздочкой в конце, например (:params?*)',
                'type'    => 'string',
                'default' => '{{ :params? }}',
                ],
                'listParams' => [
                    'title'   => 'Объявить параметры',
                    'description'   => 'Указывать через запятую',
                    'type'    => 'string',
                    'default' => '',
                ],
                'return' => [
                    'title'       => 'Возвращать параметры',
                    'type'        => 'dropdown',
                    'default'     => 'default',
                    'description'   => 'При вызове метода get() без аргументов, будут возвращены следующие параметры.',
                    'placeholder' => 'Select action',
                    'options'     => [
                        'default' => 'Объявленные',
                        'all'=>'Все',
                        'notAnnounced'=>'Необъявленные'
                        ]
                ],
                'notAnnouncedAction' => [
                    'title'       => 'С необъявленными параметры',
                    'type'        => 'dropdown',
                    'default'     => 'default',
                    'description'   => 'Если в url окажутся необъявленные параметры',
                    'placeholder' => 'Select action',
                    'options'     => [
                        'default' => 'Ничего не делать', 
                        '404'=>'404', 
                        'redirect'=>'Redirect or 404'
                        ]
                ],
                'nullParams' => [
                    'title'       => 'С пустыми параметрами',
                    'type'        => 'dropdown',
                    'default'     => 'default',
                    'description'   => 'Если в параметрах будут найдены пустые значения',
                    'placeholder' => 'Select action',
                    'options'     => [
                        'default' => 'Ничего не делать', 
                        '404'=> '404', 
                        'redirect'=>'Redirect or 404'
                        ]
                ]
        ];
    }

    
    static public $return;
    public $partUrl;
    public $listParams;

    protected function prepareVars(){
        $this->partUrl = $this->property('partUrl');
        if($this->partUrl == "*") $this->partUrl = null;
        $this->listParams = $this->property('listParams');
        self::$return = $this->property('return');
    }


    public function er404(){
        return $this->controller->run('404');
    }



    public function onRun() {

        if(!$this->partUrl){
            return null;
        }


    $UPH = &self::$urlParamsHelper;
    $redirectUrl = $UPH->all;

     if ($this->property('notAnnouncedAction') != 'default') {
        $redirectUrl = $UPH->getValue($UPH->announced);
     }
     
     if($this->property('nullParams') != 'default') {
        $redirectUrl = $UPH->filterNullParams($redirectUrl);
    }


        /* 
        404 
        */
        if($this->property('notAnnouncedAction') == '404' && $UPH->notAnnounced) {
            return $this->er404();
        }
        if($this->property('notAnnouncedAction') == 'redirect' && $UPH->notAnnounced && !$UPH->announced) {
            return $this->er404();
        }
        if ($redirectUrl === null) {
            return $this->er404();
        }


        /* 
        Redirect
        */
        if ($redirectUrl){
            $redirectUrl = $UPH->paramsToString($redirectUrl);
            if($this->property('partUrl') != $redirectUrl){
                return \Redirect::to($this->page->id.'/'.$redirectUrl,302);
            }
        }

    }


    /* 
    methods
     */

    static public function get($scopeParams=null){
        $UPH = &self::$urlParamsHelper;
        if(!isset($UPH->all)) return;
        $return = &self::$return;
    
        if ($scopeParams == 'all') {
        return $UPH->all;
        } elseif ($scopeParams == 'notAnnounced'){
        return $UPH->getvalue($UPH->notAnnounced);
        } elseif ($scopeParams == 'announced'){
        return $UPH->getvalue($UPH->announced);
        }

        if($return == 'default') {
        return $UPH->getvalue($UPH->announced);
        } elseif ($return == 'all') {
        return $UPH->all;
        } elseif ($return == 'notAnnounced'){
        return $UPH->getvalue($UPH->notAnnounced);
        }

    }

    
    
    static public function getStringParams($scopeParams=null){
        $UPH = &self::$urlParamsHelper;
        return $UPH->paramsToString(
            self::get($scopeParams)
        );
    }



    public function init(){
        $this->prepareVars();
        $UPH = new UrlParamsHelper;
      
        if ($this->partUrl) {
            $UPH->init([
                'params' => $this->listParams,
                'partUrl' => $this->partUrl
            ]);
        }

        self::$urlParamsHelper = $UPH;
    }

        





}

?>
