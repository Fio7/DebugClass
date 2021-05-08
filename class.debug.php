<?php

class Debug{

    public $level='all';//all, 
    public $backtrace=0;
    public $count;
    public $to='someone@somehere.com';//debug default email
    public $message='';//can be string or array
    public $subject='Debug Email - ';
    public $from='noone@nowhere.com';
    public $replyto='noone@nowhere.com';
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
        //...But is an admin debug method, so meh

        if(empty($this->headers)){
            $this->headers = 'To: Admin <'.$this->to.'>' . "\r\n";
            $this->headers .= "MIME-Version: 1.0" . "\r\n";
            $this->headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $this->headers .= "From:  Debug email <".$this->from."> \r\n";
            $this->headers .= "Reply-To:  ".$this->replyto."\r\n".'X-Mailer: PHP/' . phpversion();
        }
        return $this->headers;//necessary? >>test
    }
    
    public function dmail(){
        /**
         * Debug mailer
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
        $debug->headers='$headers = 'To: Simon <'.$to.'>' . "\r\n";
        //replace ***CONTENT*** if adding custom headers
        $headers .= "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From:  ***FROM NAME*** <***ADD_EMAIL_ADDRESS***> \r\n";
        $headers .= "Reply-To:  ***ADD_EMAIL_ADDRESS***\r\n".'X-Mailer: PHP/' . phpversion();';
        $debug->fromname='';//email string
        */

        
        $this->headers=$this->headers();

        if(!empty($this->message)){
            if(is_array($this->message)){
                $message=$this->prs($this->message,1);
                $message=$message."<br/><br/>"; //adda space before dumping backtrace data
            }
        }
        
        $details=debug_backtrace();
        foreach($details as $k=>$v){
            foreach($details as $k2=>$v2){
                if($k2=='file'){
                    $message.='<p>';
                    $message.= 'File - '.$v['file'];
                    $message.= '<br />Line - '.$v['line'];
                    $message.= '</p>';
                }
            }
        }
        
        $html_message = '<html><body>';
        $html_message .=$message;
        $html_message .= '</body></html>';

        mail($this->to,$this->subject.' - '.time(),$html_message,$this->headers);
    }
    
    /*display tools*/
    private function prep_display_array($arr,$count){
        //display_array()'s worker bee function
        //style not classes (for email use)
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
         * print_r() clone with formatting
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

    public function prbt($val='',$return=0){
        /**
         * print_r() clone with additional backtrace information
         * $v is a string or array
         * Formatted and extended print_r()
         * Provides
         */

        //initalize the return string
        $retstr='';

        //gprint the string or recursive travel the array
        if(!empty($val))$retstr.=$retstr=$this->pr($v,1);
        
        
        
        //ob_start();
        //echo '<pre>';
        //var_dump(debug_backtrace());
        //echo '<pre>';
        //$details= ob_get_clean();
        $details=debug_backtrace();
        

        //$this->prs($details);
        foreach($details as $k=>$v){
            //if(!is_object($v))$this->prs($v);


            $retstr.=$v['file'].' - Line '.$v['line'];
            if(!empty($v['function']))$retstr.=' <br/>Function - '.$v['function'];
                if(!empty($v['class']))$retstr.=' <br/>Class - '.$v['class'];
                $retstr.='<br/><br/>';      
            
            //if(!empty($btarr))$retstr.=implode('<br/><br/>',$btarr);
            
        }
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


