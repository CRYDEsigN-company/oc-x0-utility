<?php namespace x0\Toolbox\Classes\Helpers;

class UrlParamsHelper {
    public $init;
    public $all;
    public $announced;
    public $notAnnounced = [];
    public $partUrl;
    


    public function init($ar){
        $this->init = explode(',',$ar['params']);
        $this->partUrl = $ar['partUrl'];
        $this->all = $this->buildParams();
        $this->paramsToArray();
        $this->filterParams();
    }

        

    
    public function filterNullParams($arr=null) {
        if(!$arr) {
            $arr = &$this->all;
        } 
        $result = array();
        $result = array_filter($arr, function($el) {
            return !empty($el);
        });
        if (!$result) {
            return null;
        }
        return $result;
     }



    public function getValue($ar){
        $result = array();
        foreach ($ar as $k => $v) {
           $result[$v] = $this->all[$v];
        }
        if (!$result) {
            return null;
        }
        return $result;
    }


    public function buildParams($partUrl=null){
        if (!$partUrl){
            $str = $this->partUrl;
        }
       
        $str = explode('/',$str);
        $new = array();
        $count = count($str);

        foreach ($str as $thisKey => $thisValue) {
           $nextKey=$thisKey+1;
            $nextValue = null;
           if ($nextKey <= $count-1) {
                $nextValue = $str[$nextKey];
           }
        
           $thisInit = in_array($thisValue,$this->init);
           $nextInit = in_array($nextValue,$this->init);

            if($thisInit && !$nextInit) {
                
                $arr = explode('=',$nextValue,2);
                if (count($arr) == 2) {
                    if(in_array($arr[0],$this->init)){
                        $new[$thisValue] = null;
                    }
                } else {
                    $new[$thisValue] = $nextValue;
                    $str[$nextKey] = null;
                }
               
            } elseif ($thisInit && $nextInit) {
                $new[$thisValue] = null;
            } elseif (!$thisInit && $str[$thisKey] !== null){
                $arr = explode('=',$thisValue,2);
                if (count($arr) == 2) {
                    if (!$arr[0]){
                        $new['undefined'] = $arr[1];
                    } else {
                        $new[$arr[0]] = $arr[1];
                    }
                } else {
                    $new[$thisValue] = null;
                }
            }
        }
       
        return $new;
    }

        public function paramsToArray($arr=null){
            if(!$arr) {
                $arr = &$this->all;
            }
            $arr = array_map(function($n){
                if ($n){
                    return explode(',',$n);
                }
            }, $arr);
            return $arr;
        }



    public function filterParams($arr=null){
            if (!$arr) {
                $arr = [
                    'params' => $this->all,
                    'patterns' => $this->init
                ];
            }

            $this->notAnnounced = array_diff(
                array_keys($arr['params']),
                array_values($arr['patterns'])
            ); 

            $this->announced = array_diff(
                array_keys( $arr['params']),
                array_values($this->notAnnounced)
            );

        return $this;
    }



    function paramsToString($arr=null){
        if(!$arr || !is_array($arr)){
            return false;
        }
        array_walk($arr, function(&$a, $b) {
            if(is_array($a)) {
                    in_array($b,$this->init) ? $sep='/' : $sep='=';
                    $b == "undefined" ? $b='' : null;
                    $a = $b.$sep.implode(',',$a);
            } else {
                $a = "$b";
            }
        });
           
        $arr = implode('/',$arr);
        $arr = trim($arr,'/');
        return $arr;
    }


}

?>