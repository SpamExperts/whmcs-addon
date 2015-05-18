<?php

if(!class_exists('MG_Validation'))
{
    class MG_Validation
    {
        private static $input;
        private static $data;

        public static function setRules($fieldName, $friendlyName, $rules)
        {
            $ex = explode("|", $rules);
            $arr = array();
            foreach($ex as $rule)
            {
                $exploded = explode(':', $rule);

                if(count($exploded) == 1)
                {
                    $arr[] = array
                    (
                        'rule' =>  $exploded[0],
                        'passed'    => 0
                    );
                }
                else
                {
                    $rule = $exploded[0];
                    unset($exploded[0]);

                    $arr[] = array
                    (
                        'rule' =>  $rule,
                        'vars'  =>  $exploded,
                        'passed'    => 0
                    );
                }
            };

            self::$data[$fieldName] = array
            (
                'friendlyName'  =>  $friendlyName,
                'passed'        =>  1,
                'rules'         =>  $arr
            );
        }

        public static function run($input)
        {
            if(!$input)
            {
                $input = $_REQUEST;
            }

            if(!count($input))
            {
                return false;
            }

            self::$input = $input;

            //die(print_r(self::$data));
            $passed = true;
            foreach(self::$data as $fieldName => &$field)
            {
                foreach($field['rules'] as &$rule)
                {
                    $func = $rule['rule'];
                    if(method_exists(__CLASS__, $func)) 
                    {
                        if(isset($rule['vars']))
                        {
                            $rule['passed'] = (int)call_user_func(array('MG_Validation', $func), $fieldName, $rule['vars']);
                        }
                        else
                        {
                            $rule['passed'] = (int)call_user_func(array('MG_Validation', $func), $fieldName);
                        }
                   }
                   else
                   {
                       $rule['passed'] = (int)call_user_func($func, self::$input[$fieldName]);
                   }

                    if(!$rule['passed'])
                    {
                        addError(str_replace("%", $field['friendlyName'], MG_Lang::translate('validation_'.$rule['rule'])));
                        $passed = 0;
                        $field['passed'] = 0;
                    }
                }
            }
            return $passed;
        }

        public static function isValid($field)
        {
            if(!isset(self::$data[$field]))
                return true;

            return (int)self::$data[$field]['passed'];
        }

        private static function required($field)
        {
            if(isset(self::$input[$field]) && is_array(self::$input[$field]))
            {
                return true;
            }

            if(strlen(trim(self::$input[$field])))
            {
                return true;
            }
            return false;
        }

        private static function email($field)
        {
            if(filter_var(self::$input[$field], FILTER_VALIDATE_EMAIL))
            {
                return true;
            }
            return false;
        }

        private static function int($field)
        {
            if(filter_var(self::$input[$field], FILTER_VALIDATE_INT))
            {
                return true;
            }
            return false;
        }

        private static function compare($field, $vars)
        {
            foreach($vars as $var)
            {
                if(self::$input[$field] != self::$input[$var])
                {
                    return false;
                }
            }
            return true;
        }

        private static function domain($field)
        {
            $pieces = explode(".",self::$input[$field]);
            foreach($pieces as $piece)
            {
                if (!preg_match('/^[a-z\d][a-z\d-]{0,62}$/i', $piece) || preg_match('/-$/', $piece) )
                {
                    return false;
                }
            }
            return true;
        }

        private static function datetime($field)
        {
            if(self::$input[$field] == date( 'Y-m-d H:i:s', strtotime(self::$input[$field])))
                    return true;
            return false;
        }

        private static function date($field)
        {
            if(self::$input[$field] == date( 'Y-m-d', strtotime(self::$input[$field])))
                    return true;
            return false;
        }
    }
}
