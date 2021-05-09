<?php

class Debug{

    /**
     * Class with useful debugging functions.
     * 
     * Update default properties
     * 
     * >>USES<<
     * $cars = array (
     *     array("Volvo",22,18),
     *     array("BMW",15,13),
     *     array("Saab",5,2),
     *     array("Land Rover",17,15)
     * 
     *   );
     * 
     *  
     * ```````````````````````````````````
     * Display or return STRINGS and VARS
     * ```````````````````````````````````
     * (assuming debug class object is $db)
     * Display and array
     * 
     * 
     * 
     * Display formatted multidimensional array. 
     * $db->pr($cars);
     * 
     * Display the string
     * $db->pr('Show me cars');
     * 
     * Display multidimensional array with backtrace info
     * $db->prbt($cars);
     * 
     * Display string with backtrace info
     * $db->prbt('show me cars');
     * 
     * `````````````````````
     * Send debugging email
     * `````````````````````
     * 
     * Email is also used in debugging. 
     * If defaults are updated, all options become optional
     * Custom headers allowed
     * $replyto defaults to $from if omitted
     * 
     * dmail() $message parameters can be passed in the method call, or as a property $db->message before calling the method. Prameter overides property
     * The thinking here is to make it's use quick and easy as it's an everyday debug function, particularly useful in ajax development.
     * 
     * //set properties
     * $db->to='myemail@gmail.com ';//email string
     * $db->message='';//String or array
     * $db->subject='';//String
     * $db->from='me@gmail.com';//email string
     *
     * //typical use 
     * //quick and dirty is sometimes preferred!
     * 
     * $db->dmail($cars);
     * $db->dmail('cars');
     * $db->dmail();
     * 
     * $db->message=$cars;
     * $db->dmail();
     * $db->message='cars2';
     * $db->dmail();
     * $db->message='';
     * $db->dmail();
     * 
     */

    
    public $to='devsemail@somehere.com';//debug default email
    public $message='';//String or array. Alternative property way to send message. Overwritten by method property $message. 
    public $subject='Debug Email - ';//debug email subject
    public $from='noone@nowhere.com';//debug email from. not relevant in debugging
    public $replyto=''; //optional debug email reply to. If empty defaults to $from
    public $headers='';//all or nothing
    public $fromname = "PHP Engine"; 


    public function __construct(){

    }
    public function __destruct(){
    }
    
    public function now(){
        return time();
    }

    public function hello(){
        echo 'hello world!';
    }

    
    public function headers(){
        //probably should add a little injection protection here
        //...But is an admin debug method, so...

        if(empty($this->headers)){
            $this->headers = 'To: Admin <'.$this->to.'>' . "\r\n";
            $this->headers .= "MIME-Version: 1.0" . "\r\n";
            $this->headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $this->headers .= "From:  Debug email <".$this->from."> \r\n";
            $this->headers .= "Reply-To:  ".(!empty($this->replyto)?$this->replyto:(!empty($this->from)?$this->from:'nobody@nowhere.com'))."\r\n".'X-Mailer: PHP/' . phpversion();
        }
        return $this->headers;//necessary? >>test
    }
    
    public function dmail($message=''){
        /**
         * Debug mailer
         * $message overrides class property for ease of use
         * 
         * Full backtrace baked in ($all=1)
         * 
         * create the to/from/subject/message/headers variables before calling the method
         * update defaults to actual emails
         * time() creates unique subject to prevent email clients like gmail, grouping debug mails into a conversation
         * 
         * Set default or pass available properties:
         * 
         * Setting defaults in Debug class make all properties optional
         * 
         * public $to='someone@somehere.com';//debug default email
         * public $message='';//can be string or array
         * public $subject='Debug Email - ';
         * public $from='noone@nowhere.com';
         * public $replyto='noone@nowhere.com';
         * public $headers='';//all or nothing
         * public $fromname = "PHP Engine";
         */

         /* copy/pasta

        $debug->to='';//email string
        $debug->message='';//String or array
        $debug->subject='';//String
        $debug->from='';//email string
        $debug->replyto='';//email string

        //replace ***CONTENT*** if adding custom headers
        $debug->headers='$headers = 'To: Simon <'.$to.'>' . "\r\n";
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From:  ***FROM NAME*** <***ADD_EMAIL_ADDRESS***> \r\n";
        $headers .= "Reply-To:  ***ADD_EMAIL_ADDRESS***\r\n".'X-Mailer: PHP/' . phpversion();';
        $debug->fromname='';//email string
        */

        
        
        $this->headers=$this->headers();

        $message=(!empty($message)?$this->prbt($message,1,1):(!empty($this->message)?$this->prbt($this->message,1,1):$this->prbt('',1,1)));
        //echo $message;
        
        $html_message = '<html><body>'.$this->pr($message,1).'</body></html>';

        mail($this->to,$this->subject.' - '.time(),$html_message,$this->headers);
    }
    
    /*display tools*/
    private function prep_display_array($arr,$count){
        /**
         * display_array()'s worker bee function
         * inline style not classes (for email friendliness)
         */

        ksort($arr);
        if(is_array($arr)){
            foreach($arr as $k=> $v){
                $margin=$count*20;
                if(is_array($v)){
                    echo '  <div style="margin-bottom:3px;margin-left:'.$margin.'px;">['.$k.']=>Array</div>
                            <div style="clear:both"></div>';
                            $count++;
                            $this->prep_display_array($v,$count);
                            $count--;
                }else{
                    if(!is_object($v)){
                        echo '  <div style="margin-bottom:3px;margin-left:'.$margin.'px;">['.$k.']=>'.$v.'</div>
                                <div style="clear:both"></div>';
                    }
                }
            }
        }else{
            return 'Not an array';  
        }
    }
    
    public function pr($val='',$return=0){
        /**
         * print_r() clone with display formatting
         */
        //recursive array display buffered and returned
        $retstr='';
        if(is_array($val)){
            ob_start();
                $this->prep_display_array($val,1);
            $retstr= ob_get_clean();

        }else{
            if(!empty($val))$retstr= '<div style="margin:5px 0;">'.$val.'&nbsp;</div>';
        }

        if(empty($return)){
            echo $retstr;
        }else{
            return $retstr; 
        }
    }

    public function prbt($val='',$return=0,$all=0){
        /**
         * print_r() clone with formatting and additional backtrace information 
         * $v is a string or array
         * $return with 1, or echo with 0. Default is to echo to screen
         * $all Provides all backtrace info, 0 (default) is just first part of backtrace originaing the call. 1 for all
         * intentionally not passing in properties for quick dirty uses. 
         */

        //initalize the return string
        $retstr='';

        //gprint the string or recursive travel the array
        if(!empty($val))$retstr.=$this->pr($val,1);
        
        $details=debug_backtrace();
        
        //$this->prs($details);

        foreach($details as $k=>$v){
            //format and display the backtrace
            $retstr.='<br/>';   
            $retstr.=$v['file'].' - Line '.$v['line'];
            if(!empty($v['function']))$retstr.=' <br/>Function - '.$v['function'];
                if(!empty($v['class']))$retstr.=' <br/>Class - '.$v['class'];   
                $retstr.='<br/>';
                if ($all===0)break; //stunt the backtrace to first instance only
            
        }
        $retstr.='<br/>---<br/>';   
        if(empty($return)){
            echo $retstr;
        }else{
            return $retstr;
        }

        //if($this->backtrace==1)$this->pb();

    }

    public function pb(){
        $details=debug_backtrace();
        $this->pr($details);
    }
    
    public function line(){ 
        $details=debug_backtrace();
        echo '<br/>'.$details[0]['line'].'<br/>';
    }

}


